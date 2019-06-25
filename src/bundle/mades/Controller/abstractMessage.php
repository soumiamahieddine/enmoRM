<?php

/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of bundle mades.
 *
 * Bundle mades is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle mades is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle mades.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\mades\Controller;

/**
 * Class abstractMessage
 *
 * @package mades
 *
 */
abstract class abstractMessage
{
    protected $currentDigitalResources;

    protected function send($message)
    {
        $this->currentDigitalResources = [];

        $madesMessage = new \stdClass();

        $message->object = $madesMessage;

        $madesMessage->id = (string) $message->messageId;

        $madesMessage->date = $message->date;

        $madesMessage->comment = $message->comment;

        $madesMessage->version = "1.0";

        return $madesMessage;
    }

    protected function sendError($code, $message = false)
    {
        if ($message) {
            array_push($this->errors, new \core\Error($message, null, $code));
        } else {
            array_push($this->errors, new \core\Error($this->getReplyMessage($code), null, $code));
        }

        if ($this->replyCode == null) {
            $this->replyCode = $code;
        }
    }

    protected function sendOrganization($orgOrganization)
    {
        $organization = new \StdClass();
        $organization->identifier = $orgOrganization->registrationNumber;
        $organization->name = $orgOrganization->orgName;

        if (isset($orgOrganization->address)) {
            foreach ($orgOrganization->address as $address) {
                $organization->address[] = $this->sendAddress($address);
            }
        }

        if (isset($orgOrganization->communication)) {
            foreach ($orgOrganization->communication as $communication) {
                $organization->communication[] = $this->sendCommunication($communication);
            }
        }

        if (isset($orgOrganization->contact)) {
            foreach ($orgOrganization->contact as $contact) {
                $organization->contact[] = $this->sendContact($contact);
            }
        }

        return $organization;
    }

    protected function sendAddress($orgAddress)
    {
        $address = \laabs::newInstance('contact/address');
        $address->id = (string) $orgAddress->addressId;
        $address->blockName = $orgAddress->block;
        $address->buildingName = $orgAddress->building;
        $address->buildingNumber = $orgAddress->number;
        $address->cityName = $orgAddress->city;
        $address->citySubDivisionName = $orgAddress->citySubDivision;
        $address->country = $orgAddress->country;
        $address->floorIdentification = $orgAddress->floor;
        $address->postCode = $orgAddress->postCode;
        $address->postOfficeBox = $orgAddress->postBox;
        $address->roomIdentification = $orgAddress->room;
        $address->streetName = $orgAddress->street;

        return $address;
    }

    protected function sendCommunication($orgCommunication)
    {
        $communication = \laabs::newInstance('contact/communication');
        $communication->id = (string) $orgCommunication->communicationId;
        $communication->channel = $orgCommunication->comMeanCode;

        switch ($orgCommunication->comMeanCode) {
            case 'EM':
            case 'FTP':
                $communication->URIID = $orgCommunication->value;
                break;

            default:
                $communication->completeNumber = $orgCommunication->value;
        }

        return $communication;
    }

    protected function sendContact($orgContact)
    {
        $contact = \laabs::newInstance('contact/contact');
        $contact->id = (string) $orgContact->contactId;
        $contact->departmentName = $orgContact->service;
        $contact->identification = $orgContact->contactId;
        $contact->personName = $orgContact->displayName;
        $contact->responsibility = $orgContact->function;

        if (isset($orgContact->address)) {
            foreach ($orgContact->address as $address) {
                $contact->address[] = $this->sendAddress($address);
            }
        }

        if (isset($orgContact->communication)) {
            foreach ($orgContact->communication as $communication) {
                $contact->communication[] = $this->sendCommunication($communication);
            }
        }

        return $contact;
    }

    protected function sendUnitIdentifiers($message) {
        if (isset($message->unitIdentifier)) {
            foreach ($message->unitIdentifier as $unitIdentifier) {
                $message->object->unitIdentifier[] = $unitIdentifier->objectId;
            }
        }
    }

    protected function sendReplyCode($message) {
        if (isset($message->replyCode)) {
            $message->object->replyCode = $message->replyCode;
        }
    }

    protected function sendDataObjectPackage($message, $withBinaries = false) {
        $message->object->dataObjectPackage = new \stdClass();
        $message->object->dataObjectPackage->descriptiveMetadata = new \stdClass();
        $message->object->dataObjectPackage->binaryDataObjects = new \stdClass();

        if (is_array($message->archive)) {
            foreach ($message->archive as $archive) {
                $message->object->dataObjectPackage->descriptiveMetadata->{$archive->archiveId} = $this->sendArchiveMetadata($archive);
            }
        }
        if ($withBinaries) {
            $this->sendArchiveBinaries($message->object->dataObjectPackage);
        }
    }

    protected function sendArchiveMetadata($archive) {
        $archiveUnit = new \stdClass();
        
        if (isset($archive->archiveName)) {
            $archiveUnit->displayName = $archive->archiveName;
        }
        $archiveUnit->refDate = $archive->originatingDate;
        $archiveUnit->profile = $archive->archivalProfileReference;
        $archiveUnit->description = $archive->description;

        $archiveUnit->filing = new \stdClass();
        $archiveUnit->filing->level = $archive->fileplanLevel;
        $archiveUnit->filing->folder = $archive->filePlanPosition;
        $archiveUnit->filing->activity = $archive->originatorOrgRegNumber;
        $archiveUnit->filing->originator = $archive->originatorOwnerOrgRegNumber;
        $archiveUnit->filing->container = $archive->parentArchiveId;

        $archiveUnit->management = new \stdClass();
        $archiveUnit->management->preservationStatus = $archive->status;
        $archiveUnit->management->processingStatus = $archive->processingStatus;
        $archiveUnit->management->serviceLevel = $archive->serviceLevelReference;

        $archiveUnit->management->appraisalRule = new \stdClass();
        $archiveUnit->management->appraisalRule->code = $archive->retentionRuleCode;
        $archiveUnit->management->appraisalRule->startDate = $archive->retentionStartDate;
        $archiveUnit->management->appraisalRule->finalDisposition = $archive->finalDisposition;
        $archiveUnit->management->appraisalRule->duration = $archive->retentionDuration;
        $archiveUnit->management->appraisalRule->status = $archive->retentionRuleStatus;
        $archiveUnit->management->appraisalRule->disposalDueDate = $archive->disposalDate;

        $archiveUnit->management->accessRule = new \stdClass();
        $archiveUnit->management->accessRule->code = $archive->accessRuleCode;
        $archiveUnit->management->accessRule->startDate = $archive->accessRuleStartDate;
        $archiveUnit->management->accessRule->duration = $archive->accessRuleDuration;
        // $archiveUnit->management->accessRule->status = $archive->accessRuleStatus;
        $archiveUnit->management->accessRule->disclosureDueDate = $archive->accessRuleComDate;

        $archiveUnit->management->classificationRule = new \stdClass();
        $archiveUnit->management->classificationRule->code = $archive->classificationRuleCode;
        $archiveUnit->management->classificationRule->startDate = $archive->classificationRuleStartDate;
        $archiveUnit->management->classificationRule->duration = $archive->classificationRuleDuration;
        $archiveUnit->management->classificationRule->level = $archive->classificationLevel;
        $archiveUnit->management->classificationRule->owner = $archive->classificationOwner;
        // $archiveUnit->management->classificationRule->audience = $archive->classificationAudience;
        // $archiveUnit->management->classificationRule->status = $archive->classificationStatus;
        // $archiveUnit->management->classificationRule->reassessingDate = $archive->classificationReassessingDate;
        $archiveUnit->management->classificationRule->releaseDueDate = $archive->classificationEndDate;

        if (isset($archive->relationships)
            && is_array($archive->relationships)
            && (
                !empty($archive->relationships->parentRelationships) 
                || !empty($archive->relationships->childrenRelationships)
                )
            )
        {
            $archiveUnit->relationships = [];
            foreach ($archive->relationships->parentRelationships as $parentRelationship) {
                $relationship = new \stdClass();
                $relationship->refId = $parentRelationship->relatedArchiveId;
                $relationship->type = $parentRelationship->typeCode;
                $relationship->description = $parentRelationship->description;
                $archiveUnit->relationships[] = $relationship;
            }
            foreach ($archive->relationships->childrenRelationships as $childrenRelationship) {
                $relationship = new \stdClass();
                $relationship->refId = $childrenRelationship->archiveId;
                $relationship->type = $childrenRelationship->typeCode;
                $relationship->description = $childrenRelationship->description;
                $archiveUnit->relationships[] = $relationship;
            }
        }

        foreach($archive->digitalResources as $digitalResource) {
            $archiveUnit->dataObjectReferences[] = $digitalResource->resId;

            $this->currentDigitalResources[] = $digitalResource;
        }

        if (isset($archive->contents) && is_array($archive->contents)) {
            $archiveUnit->archiveUnits = new \stdClass();
            foreach ($archive->contents as $content) {
                $archiveUnit->archiveUnits->{$content->archiveId} = $this->sendArchiveMetadata($content);
            }
        }
        
        // TODO
        // $archiveUnit->security = new \stdClass();
        // $archiveUnit->security->user
        // $archiveUnit->security->group
        // $archiveUnit->security->org
        // $archiveUnit->security->accessControlList

        $archiveUnit->control = new \stdClass();
        $archiveUnit->control->creationDate = $archive->depositDate;
        $archiveUnit->control->lastModificationDate = $archive->lastModificationDate;
        // $archiveUnit->control->lastUseDate = 
        // $archiveUnit->control->status = "active";

        // $archiveUnit->log = 

        return $archiveUnit;
    }

    protected function sendArchiveBinaries($dataObjectPackage) {
        foreach ($this->currentDigitalResources as $digitalResource) {
            $dataObjectPackage->binaryDataObjects->{$digitalResource->resId} = $this->sendArchiveBinary($digitalResource);
        }
    }

    protected function sendArchiveBinary($digitalResource) {
        
        $binaryDataObject = new \stdClass();

        if (isset($digitalResource->resId)) {
            $binaryDataObject->attachment = new \stdClass();
            // $binaryDataObject->attachment->uri = $digitalResource->address[0]->path;
            $binaryDataObject->attachment->fileName = $digitalResource->resId;
            // $binaryDataObject->attachment->content = base64_encode($digitalResource->getContents());
            $binaryDataObject->size = $digitalResource->size;
        }

        if (isset($digitalResource->mimetype)) {
            $binaryDataObject->format = new \stdClass();
            // $binaryDataObject->format->name = 
            $binaryDataObject->format->mimeType = $digitalResource->mimetype;
            // $binaryDataObject->format->identifier = $digitalResource->puid;
            // $binaryDataObject->format->containerType =
        }

        if (isset($digitalResource->hash)) {
            $binaryDataObject->messageDigest = new \stdClass();
            // $binaryDataObject->messageDigest->uri = 
            // $binaryDataObject->messageDigest->fileName = 
            $binaryDataObject->messageDigest->content = $digitalResource->hash;
            $binaryDataObject->messageDigest->algorithm = $digitalResource->hashAlgorithm;
        }

        if (isset($digitalResource->fileName)) {
            $binaryDataObject->fileInformation = new \stdClass();
            $binaryDataObject->fileInformation->fileName = $digitalResource->fileName;
            // $binaryDataObject->fileInformation->application = 
            // $binaryDataObject->fileInformation->creationDate = 
            // $binaryDataObject->fileInformation->lastModificationDate = 
        }

        // $binaryDataObject->technicalMetadata = new \stdClass();
        // $binaryDataObject->technicalMetadata->text = 
        // $binaryDataObject->technicalMetadata->audio = 
        // $binaryDataObject->technicalMetadata->video =
        // $binaryDataObject->technicalMetadata->image =
        // $binaryDataObject->technicalMetadata->document = 
        // $binaryDataObject->technicalMetadata->{'3D'} = 
        // $binaryDataObject->technicalMetadata->nom = ?? // TODO

        if (isset($digitalResource->relatedResId)) {
            $relationship = new \stdClass();
            $relationship->type = $digitalResource->relationshipType;
            $relationship->refId = $digitalResource->relatedResId;
            // $relationship->displayName = 
            $binaryDataObject->relationships[] = $relationship;
        }

        return $binaryDataObject;
    }
}