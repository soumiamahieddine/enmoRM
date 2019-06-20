<?php
/* 
 * Copyright (C) Maarch
 *
 * This file is part of bundle Mades
 *
 * Bundle Mades is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle Mades is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle Mades. If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\mades\Controller;

/**
 * Class for archive transfer
 *
 * @package Mades
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class ArchiveTransfer extends Message implements \bundle\medona\Controller\ArchiveTransferInterface
{
    public $errors = [];
    public $infos = [];
    public $replyCode;
    public $filePlan = [];
    public $originatorOrgs = [];
    public $processedArchives = [];

    public $orgController;
    public $filePlanController;
    public $archivalProfileController;
    public $archiveController;
    public $sdoFactory;

    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->orgController = \laabs::newController("organization/organization");
        $this->filePlanController = \laabs::newController("filePlan/filePlan");
        $this->archivalProfileController = \laabs::newController("recordsManagement/archivalProfile");
        $this->archiveController = \laabs::newController("recordsManagement/archive");
    }

    /**
     * Receive message with all contents embedded
     * @param string $message The message object
     *
     * @return medona/message The acknowledgement
     */
    public function receive($message)
    {
        $this->loadMessage($message);

        if (isset($message->object->comment)) {
            $message->comment = $message->object->comment;
        }
        $message->date = $message->object->date;

        $this->receiveTransferringAgency($message);
        $message->recipientOrgRegNumber = $message->object->archivalAgency->identifier;

        $message->reference = $message->object->messageIdentifier;
        
        if (isset($message->object->archivalAgreement)) {
            $message->archivalAgreementReference = $message->object->archivalAgreement;
        }

        $binaryDataObjects = $physicalDataObjects = [];
        $message->dataObjectCount = 0;

        if (isset($message->object->dataObjectPackage->binaryDataObject)) {
            $message->dataObjectCount += count($message->object->dataObjectPackage->binaryDataObject);
            $this->receiveAttachments($message);
        }
        if (isset($message->object->dataObjectPackage->physicalDataObject)) {
            $message->dataObjectCount += count($message->object->dataObjectPackage->physicalDataObject);
        }

        return $message;
    }

    protected function receiveTransferringAgency($message)
    {
        // Organization object OR Identifier object OR string
        // Identifier is object OR string 

        if (is_object($message->object->transferringAgency)) {
            if (isset($message->object->transferringAgency->identifier)) {
                $message->senderOrgRegNumber = $message->object->transferringAgency->identifier;
            }
        }
    }

    protected function receiveAttachments($message)
    {
        $this->validateReference($message->object->dataObjectPackage->descriptiveMetadata, $message->object->dataObjectPackage->binaryDataObject);
        
        $messageDir = dirname($message->path);
        // List received files
        $receivedFiles = glob($messageDir.DIRECTORY_SEPARATOR."*.*");

        $messageFiles = [$message->path];
        foreach ($message->object->dataObjectPackage->binaryDataObject as $dataObjectId => $binaryDataObject) {
            $message->size += (integer) $binaryDataObject->size;

            if (isset($binaryDataObject->attachment)) {
                $attachment = $binaryDataObject->attachment;

                $filepath = $messageDir.DIRECTORY_SEPARATOR.$attachment->filename;
                if (!is_file($filepath)) {
                    $this->sendError(
                        "211",
                        "Le document identifié par le nom '$attachment->filename' n'a pas été trouvé."
                    );

                    continue;
                }

                $contents = file_get_contents($filepath);

                $messageFiles[] = $attachment->filename;
            } elseif ($binaryDataObject->uri) {
                $contents = file_get_contents($messageDir.DIRECTORY_SEPARATOR.basename($binaryDataObject->uri));

                if (!$contents) {
                    $this->sendError("211", "Le document à l'adresse '$attachment->uri' est indisponible.");

                    continue;
                }

                $filepath = $messageDir.DIRECTORY_SEPARATOR.basename($binaryDataObject->uri);
                $messageFiles[] = $filepath;
            } else {
                if (strlen($attachmentElement->value) == 0) {
                    $this->sendError("211", "Le contenu du document n'a pas été transmis.");

                    continue;
                }

                $contents = base64_decode($attachment->value);

                $filepath = $messageDir.DIRECTORY_SEPARATOR.$dataObjectId;
            }

            // Validate hash
            $messageDigest = $binaryDataObject->messageDigest;
            if (strtolower($messageDigest->content) != strtolower(hash($messageDigest->algorithm, $contents))) {
                $this->sendError(
                    "207",
                    "L'empreinte numérique du document '".basename($filepath)."' ne correspond pas à celle transmise."
                );
            }
        }

        // Check all files received are part of the message
        foreach ($receivedFiles as $receivedFile) {
            if (!in_array($receivedFile, $messageFiles) &&
                !in_array(basename($receivedFile), $messageFiles) &&
                !in_array(basename($receivedFile), $messageFiles)) {
                $this->sendError(
                    "101",
                    "Le fichier '".basename($receivedFile)."' n'est pas référencé dans le bordereau."
                );
            }
        }
    }

    protected function validateReference($archiveUnitContainer, $binaryDataObjects)
    {
        foreach ($archiveUnitContainer as $archiveUnit) {
            if (!empty($archiveUnit->dataObjectReference)) {
                foreach ($archiveUnit->dataObjectReference as $dataObjectReference) {
                    $res = false;
                    foreach ($binaryDataObjects as $id => $binaryDataObject) {
                        if ($id == $dataObjectReference) {
                            $res = true;
                        }
                    }
                    if (!$res) {
                        $this->sendError(
                            "213",
                            "Le document identifié par '$dataObjectReference->dataObjectReferenceId' est introuvable."
                        );
                    }
                }
            }

            if (!empty($archiveUnit->archiveUnit)) {
                $this->validateReference($archiveUnit, $binaryDataObjects);
            }
        }
    }

    /**
     * Validate message against schema and rules
     * @param string $messageId The message identifier
     * @param object $archivalAgreement The archival agreement
     *
     * @return boolean The validation result
     */
    public function validate($message, $archivalAgreement = null)
    {
        $this->errors = array();
        $this->replyCode = null;

        $message->object->binaryDataObject = get_object_vars($message->object->binaryDataObject);
        $message->object->descriptiveMetadata = get_object_vars($message->object->descriptiveMetadata);
        
        $this->validateDescriptiveMetadata($message->object->descriptiveMetadata);

        return true;
    }

    protected function validateDescriptiveMetadata($archiveUnitContainer)
    {
        foreach ($archiveUnitContainer as $archiveUnit) {
            $this->archiveController->validateFileplan($archiveUnit);
            $this->archiveController->validateArchiveDescriptionObject($archiveUnit);
            $this->archiveController->validateManagementMetadata($archiveUnit);
        }

        if (!empty($archiveUnit->archiveUnit)) {
            $this->validateDescriptiveMetadata($archiveUnit);
        }
    }

    /**
     * Process the archive transfer
     * @param mixed $message The message object or the message identifier
     *
     * @return string The reply message identifier
     */
    public function process($message)
    {
        $message->object->binaryDataObject = get_object_vars($message->object->binaryDataObject);
        $message->object->descriptiveMetadata = get_object_vars($message->object->descriptiveMetadata);

        $this->processedArchives = [];

        foreach ($message->object->descriptiveMetadata as $key => $archiveUnit) {
            $archiveUnit = \laabs::castMessageObject($archiveUnit, "recordsManagement/ArchiveUnit");
            $archive = \laabs::newInstance("recordsManagement/archive");
            $archive->archiveId = \laabs::newId();
            $archive = $this->processArchiveUnit($archive, $message, $archiveUnit);

            $this->processedArchives[] = $archive;
        }

        return [$this->processedArchives, null];
    }

    protected function processArchiveUnit($archive, $message, $archiveUnit)
    {
        $archive->archiveName = $archiveUnit->archiveName;

        $archive->depositorOrgRegNumber = $message->senderOrgRegNumber;
        $archive->archiverOrgRegNumber = $message->recipientOrgRegNumber;

        $archive->originatorArchiveId = $archiveUnit->originatorArchiveId;

        $archive->depositorArchiveId = $archiveUnit->depositorArchiveId;

        $archive->archiverArchiveId = $archiveUnit->archiverArchiveId;

        $archive->descriptionClass = $archiveUnit->descriptionClass;

        $archive->description = $archiveUnit->descriptionObject;

        $archive->originatingDate = $archiveUnit->originatingDate;

        if (empty($message->object->managementMetadata)) {
            $message->object->managementMetadata = null;
        }

        $this->processManagementMetadata(
            $archive,
            $archiveUnit->managementMetadata,
            $message->object->managementMetadata
        );

        $this->processOriginitorOrg($archive, $archiveUnit);

        $this->processFilePlan($archive, $archiveUnit);

        $this->processBinaryDataObject($archive, $archiveUnit, $message);

        $this->processLifeCycleEvents($archive, $archiveUnit);
        $this->processRelationship($archive, $archiveUnit, $message);
        
        if (empty($archiveUnit->archiveUnit)) {
            return $archive;
        }

        foreach ($archiveUnit->archiveUnit as $key => $subArchiveUnit) {
            $subArchiveUnit = \laabs::castMessageObject($subArchiveUnit, "recordsManagement/ArchiveUnit");
            $content = \laabs::newInstance("recordsManagement/archive");
            $content->archiveId = $key;
            $content->parentArchiveId = $archive->archiveId;
            $content->serviceLevelReference = $archive->serviceLevelReference;

            $subArchiveUnit->originatorId = $subArchiveUnit->originatorId;

            $content = $this->processArchiveUnit($content, $message, $subArchiveUnit);

            $this->processedArchives[] = $content;

        }

        return $archive;
    }

    protected function processManagementMetadata($archive, $archiveManagementMetadata, $messageManagementMetadata)
    {
        if (!empty($archiveManagementMetadata->archivalProfile)) {
            $archive->archivalProfileReference = $archiveManagementMetadata->archivalProfile;
        } elseif (!empty($messageManagementMetadata->archivalProfile)) {
            $archive->archivalProfileReference = $messageManagementMetadata->archivalProfile;
        }

        if (!empty($archiveManagementMetadata->serviceLevel)) {
            $archive->serviceLevelReference = $archiveManagementMetadata->serviceLevel;
        } elseif (!empty($messageManagementMetadata->serviceLevel)) {
            $archive->serviceLevelReference = $messageManagementMetadata->serviceLevel;
        }

        if (!empty($archiveManagementMetadata->accessRule)) {
            $this->processAccessRule($archive, $archiveManagementMetadata);
        } elseif (!empty($messageManagementMetadata->accessRule)) {
            $this->processAccessRule($archive, $messageManagementMetadata);
        }

        if (!empty($archiveManagementMetadata->appraisalRule)) {
            $this->processAppraisalRule($archive, $archiveManagementMetadata);
        } elseif (!empty($messageManagementMetadata->appraisalRule)) {
            $this->processAppraisalRule($archive, $messageManagementMetadata);
        }

        if (!empty($archiveManagementMetadata->classificationRule)) {
            $this->processClassificationRule($archive, $archiveManagementMetadata);
        } elseif (!empty($messageManagementMetadata->classificationRule)) {
            $this->processClassificationRule($archive, $messageManagementMetadata);
        }
    }

    protected function processAccessRule($archive, $managementMetadata)
    {
        if (!empty($managementMetadata->accessRule->startDate)) {
            $archive->accessRuleCode = $managementMetadata->accessRule->code;
        }

        if (!empty($managementMetadata->accessRule->startDate)) {
            $archive->accessRuleStartDate = $managementMetadata->accessRule->startDate;
        }
    }

    protected function processAppraisalRule($archive, $managementMetadata)
    {
        if (!empty($managementMetadata->appraisalRule->code)) {
            $archive->retentionRuleCode = $managementMetadata->appraisalRule->code;
        }

        if (!empty($managementMetadata->appraisalRule->startDate)) {
            $archive->retentionStartDate = $managementMetadata->appraisalRule->startDate;
        }
        if (!empty($managementMetadata->appraisalRule->finalDisposition)) {
            $archive->finalDisposition = $managementMetadata->appraisalRule->finalDisposition;
        }
    }

    protected function processOriginitorOrg($archive, $descriptiveMetadata)
    {
        $archive->originatorOrgRegNumber = $descriptiveMetadata->originatorId;

        if (!isset($this->originatorOrgs[$archive->originatorOrgRegNumber])) {
            $originatorOrg = $this->orgController->getOrgByRegNumber($archive->originatorOrgRegNumber);
            $this->originatorOrgs[$archive->originatorOrgRegNumber] = $originatorOrg;
        } else {
            $originatorOrg = $this->originatorOrgs[$archive->originatorOrgRegNumber];
        }

        $archive->originatorOwnerOrgId = $originatorOrg->ownerOrgId;
        $archive->originatorOwnerOrgRegNumber = $originatorOrg->registrationNumber;
    }

    protected function processClassificationRule($archive, $managementMetadata)
    {
        $archive->classificationRuleCode = $managementMetadata->classificationRule->code;
        $archive->classificationRuleStartDate = $managementMetadata->classificationRule->startDate;
        $archive->classificationLevel = $managementMetadata->classificationRule->classificationLevel;
        $archive->classificationOwner = $managementMetadata->classificationRule->classificationOwner;
    }

    protected function processFilePlan($archive, $descriptiveMetadata) {
        if (isset($this->filePlan[$descriptiveMetadata->folderPath])) {
            $archive->filePlanPosition = $this->filePlan[$descriptiveMetadata->folderPath];
        } else {
            $archive->filePlanPosition = $this->filePlanController->createFromPath(
                $descriptiveMetadata->folderPath,
                $archive->depositorOrgRegNumber,
                true
            );
            $this->filePlan[$descriptiveMetadata->folderPath] = $archive->filePlanPosition;
        }

        $archive->fileplanLevel = $descriptiveMetadata->archiveType;
    }

    protected function processBinaryDataObject($archive, $archiveUnit, $message)
    {
        if (empty($archiveUnit->dataObjects)) {
            return;
        }

        foreach ($archiveUnit->dataObjects as $dataObjectId) {
            $binaryDataObject = $message->object->binaryDataObject[$dataObjectId];

            $digitalResource = \laabs::newInstance("digitalResource/digitalResource");
            $digitalResource->archiveId = $archive->archiveId;
            $digitalResource->resId = \laabs::newId();
            $digitalResource->size = $binaryDataObject->size;
            if (isset($binaryDataObject->format)) {
                $digitalResource->puid = $binaryDataObject->format;
            }
            $digitalResource->mimetype = $binaryDataObject->mimetype;
            if (isset($binaryDataObject->messageDigest)) {
                $digitalResource->hash = $binaryDataObject->messageDigest->value;
                $digitalResource->hashAlgorithm = $binaryDataObject->messageDigest->algorithm;
            }
            $digitalResource->fileName = $binaryDataObject->attachment->filename;
            $digitalResource->setContents(base64_decode($binaryDataObject->attachment->value));

            $archive->digitalResources[] = $digitalResource;
        }
    }

    protected function processLifeCycleEvents($archive, $archiveUnit)
    {
        if (empty($archiveUnit->lifeCycleEvents)) {
            return;
        }

        foreach ($archiveUnit->lifeCycleEvents as $event) {
            $newEvent = \laabs::newInstance("lifeCycle/event");
            $newEvent->eventType = $event->type;
            $newEvent->objectClass = "recordsManagement/archive";
            $newEvent->objectId = $archive->archiveId;
            $newEvent->description = $event->description;
            $newEvent->eventInfo = $event->eventInfo;

            $archive->lifeCycleEvents[] = $newEvent;
        }
    }

    protected function processRelationship($archive, $archiveUnit, $message)
    {
        $archive->archiveRelationship = [];

        if (empty($archiveUnit->relationships)) {
            return;
        }

        foreach ($archiveUnit->relationships as $relationships) {
            $archiveRelationship = \laabs::newInstance("recordsManagement/archiveRelationship");
            $archiveRelationship->archiveId = $archiveUnit->archiveId;
            $archiveRelationship->relatedArchiveId = $relationships->relatedArchiveId;
            $archiveRelationship->typeCode = $relationships->relationshipType;
            $archiveRelationship->description = $relationships->description;

            $archive->archiveRelationship[] = $archiveRelationship;
        }
    }
}
