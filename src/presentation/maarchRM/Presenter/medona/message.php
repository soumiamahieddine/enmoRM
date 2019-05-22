<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona.
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\Presenter\medona;

/**
 * Serializer html retentionRule
 *
 * @package RecordsManagement
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.com>
 */
class message
{

    use archiveDeliveryTrait,
        archiveDestructionTrait,
        archiveTransferTrait,
        archiveOutgoingTransferTrait,
        archiveAuthorizationTrait,
        archiveNotificationTrait,
        archiveRestitutionTrait,
        \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $sdoFactory;
    protected $json;
    protected $translator;

    protected $dashboardPresenter;
    protected $menu;
    protected $packageSchemas = [];

    /**
     * Constuctor of message html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\sdo\Factory                      $sdoFactory The SDO dependency
     * @param \dependency\json\JsonObject                  $json       The JSON dependency
     * @param \dependency\localisation\TranslatorInterface $translator The localisation dependency
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\sdo\Factory $sdoFactory,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    ) {
        $this->view = $view;
        $this->sdoFactory = $sdoFactory;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('medona/messages');
        $this->menu = \laabs::configuration()['medona']['menu'];

        $this->dashboardPresenter = \laabs::newService('presentation/dashboard');

        if (isset(\laabs::configuration('medona')['packageSchemas'])) {
            $this->packageSchemas = \laabs::configuration('medona')['packageSchemas'];
        }
    }

    /**
     * Dashobord for messages
     */
    public function index()
    {
        $this->view->addContentFile("medona/message/menu.html");

        $menu = $this->dashboardPresenter->filterMenuAuth($this->menu);

        $this->view->translate();

        $this->view->setSource('medonaMenu', $menu);

        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * View message description
     * @param medona/message $message The message object
     *
     * @return string
     */
    public function read($message)
    {
        $this->view->addContentFile("medona/message/message.table.html");
        $this->view->translate();

        $this->view->setSource("message", $message);

        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Initialise the history form
     */
    public function initHistoryForm()
    {
        $archivalAgreements = \laabs::callService('medona/archivalAgreement/readIndex');
        $currentService = \laabs::getToken("ORGANIZATION");
        $ownerOriginatorOrgs = [];
        $ownerDepositorOrgs = [];
        $ownerArchiverOrgs = [];

        if ($currentService) {
            $originators = \laabs::callService('organization/organization/readByrole_role_', 'originator');
            $depositors = \laabs::callService('organization/organization/readByrole_role_', 'depositor');
            $archivers = \laabs::callService('organization/organization/readByrole_role_', 'archiver');


            $owner = is_array($currentService->orgRoleCodes) && in_array('owner', $currentService->orgRoleCodes);

            foreach ($originators as $originator) {
                if (!isset($ownerOriginatorOrgs[(string) $originator->ownerOrgId])) {
                    $orgObject = \laabs::callService('organization/organization/read_orgId_', (string) $originator->ownerOrgId);

                    $ownerOriginatorOrgs[(string) $orgObject->orgId] = new \stdClass();
                    $ownerOriginatorOrgs[(string) $orgObject->orgId]->displayName = $orgObject->displayName;
                    $ownerOriginatorOrgs[(string) $orgObject->orgId]->originators = [];
                }

                $ownerOriginatorOrgs[(string) $orgObject->orgId]->originators[] = $originator;
            }
            foreach ($depositors as $depositor) {
                if (!isset($ownerDepositorOrgs[(string) $depositor->ownerOrgId])) {
                    $orgObject = \laabs::callService('organization/organization/read_orgId_', (string) $depositor->ownerOrgId);
                    $ownerDepositorOrgs[(string) $orgObject->orgId] = new \stdClass();
                    $ownerDepositorOrgs[(string) $orgObject->orgId]->displayName = $orgObject->displayName;
                    $ownerDepositorOrgs[(string) $orgObject->orgId]->depositors = [];
                }

                $ownerDepositorOrgs[(string) $orgObject->orgId]->depositors[] = $depositor ;
            }
            foreach ($archivers as $archiver) {
                if (!isset($ownerArchiverOrgs[(string) $archiver->ownerOrgId])) {
                    $orgObject = \laabs::callService('organization/organization/read_orgId_', (string) $archiver->ownerOrgId);
                    $ownerArchiverOrgs[(string) $orgObject->orgId] = new \stdClass();
                    $ownerArchiverOrgs[(string) $orgObject->orgId]->displayName = $orgObject->displayName;
                    $ownerArchiverOrgs[(string) $orgObject->orgId]->archivers = [];
                }

                $ownerArchiverOrgs[(string) $orgObject->orgId]->archivers[] = $archiver ;
            }
        }

        $this->view->setSource('archivalAgreements', $archivalAgreements);
        $this->view->setSource('ownerOriginatorOrgs', $ownerOriginatorOrgs);
        $this->view->setSource('ownerDepositorOrgs', $ownerDepositorOrgs);
        $this->view->setSource('ownerArchiverOrgs', $ownerArchiverOrgs);
    }

    /**
     * get a message's information for history view
     * @param medona/message $message The message objects
     *
     * @return string
     */
    public function displayInHistory($message)
    {
        return $this->display($message, $withActionsButtons = false);
    }

    /**
     * get a message's information
     * @param medona/message $message            The message objects
     * @param boolean        $withActionsButtons Set the action buttons up on screen
     *
     * @return string
     */
    public function display($message, $withActionsButtons = true)
    {
        if (isset(\laabs::configuration('medona')['parentsDerogation']) && \laabs::configuration('medona')['parentsDerogation']) {
            $registrationNumber = \laabs::callService('organization/userPosition/readDescendantservices');
        } else {
            $organization = \laabs::getToken("ORGANIZATION");
            $registrationNumber = array($organization->registrationNumber);
        }

        $messageObjects = $unitIdentifiers = [];

        $messages = array($message);
        $baseMessage = reset($messages);
        if (isset($message->acknowledgement)) {
            $messages = array_merge($messages, [$message->acknowledgement]);
        }

        if (isset($message->authorizationMessages)) {
            $messages = array_merge($messages, $message->authorizationMessages);
        }

        if (isset($message->replyMessage)) {
            $messages[] = $message->replyMessage;
        }

        $schemaConf = $presenter = null;
        if (isset($this->packageSchemas[$baseMessage->schema])) {
            $schemaConf = $this->packageSchemas[$baseMessage->schema];
            if (isset($schemaConf['presenter'])) {
                $presenter = \laabs::newPresenter($schemaConf['presenter']);
            }
        }

        foreach ($messages as $message) {
            $messageObject = $message->object;
            $messageObject->type = $message->type;
            $messageObject->schema = $message->schema;
            $messageObject->messageId = $message->messageId;
            $messageObject->xml = isset($message->xml);
            $messageObject->archived = $message->archived;
            $messageObject->status = $message->status;
            $messageObject->archivalAgreement = $message->archivalAgreementReference;
            $messageObject->reference = $message->reference;
            $messageObject->size = number_format($message->size, 0, '', ' ');

            $user = \laabs::callService('auth/userAccount/read_userAccountId_', $message->accountId);
            $messageObject->accountDisplayName = $user->displayName;
            $messageObject->accountName = $user->accountName;

            if (isset($message->lifeCycleEvent)) {
                $messageObject->lifeCycleEvent = $message->lifeCycleEvent;
            }

            // Load the organizations names
            if (isset($messageObject->archivalAgency)) {
                $messageObject->archivalAgency->name = $this->loadOrganizationName($messageObject->archivalAgency, $message);
            }
            if (isset($messageObject->transferringAgency)) {
                $messageObject->transferringAgency->name = $this->loadOrganizationName($messageObject->transferringAgency, $message);
            }
            if (isset($messageObject->controlAuthority)) {
                $messageObject->controlAuthority->name = $this->loadOrganizationName($messageObject->controlAuthority, $message);
            }
            if (isset($messageObject->originatingAgency)) {
                $messageObject->originatingAgency->name = $this->loadOrganizationName($messageObject->originatingAgency, $message);
            }
            if (isset($messageObject->requester)) {
                $messageObject->requester->name = $this->loadOrganizationName($messageObject->requester, $message);
            }
            if (isset($messageObject->sender)) {
                $messageObject->sender->name = $this->loadOrganizationName($messageObject->sender, $message);
            }
            if (isset($messageObject->receiver)) {
                $messageObject->receiver->name = $this->loadOrganizationName($messageObject->receiver, $message);
            }

            // Set messages action buttons
            if ($withActionsButtons) {
                $this->setMessageActions($message, $messages, $messageObject, $registrationNumber);
            }

            if ($message->status === 'error') {
                $messageObject->retryButton = "/medona/message/". $message->messageId . "/retry";
            }

            if (is_array($message->unitIdentifier)) {
                foreach ($message->unitIdentifier as $unitIdentifier) {
                    $unitIdentifiers[(string) $unitIdentifier->objectId] = $unitIdentifier;
                }
            }

            if ($baseMessage->schema == 'recordsManagement') {
                if (!isset($descriptionField)) {
                    $descriptionFields = \laabs::callService('recordsManagement/descriptionField/readIndex');
                }

                $messageObject->sender = $message->senderOrg;
                $messageObject->receiver = $message->recipientOrg;

                if (isset($messageObject->binaryDataObject)) {
                    $messageObject->binaryDataObject = (array) $messageObject->binaryDataObject;
                }

                if (isset($messageObject->descriptiveMetadata)) {
                    $messageObject->descriptiveMetadata = (array) $messageObject->descriptiveMetadata;

                    foreach ($messageObject->descriptiveMetadata as $descriptiveMetadata) {
                        if (isset($descriptiveMetadata->descriptionObject)) {
                            $descriptionObjects = $descriptiveMetadata->descriptionObject;
                            $descriptiveMetadata->descriptionObject = [];

                            foreach($descriptionObjects as $key => $value) {
                                $object = new \stdClass();
                                $object->key = $key;

                                foreach ($descriptionFields as $descriptionField) {
                                    if ($descriptionField->name === $key) {
                                        $object->label = $descriptionField->label;
                                    }
                                }

                                $object->value = $value;

                                $descriptiveMetadata->descriptionObject[] = $object;
                            }
                        }
                    }
                }
            } elseif ($baseMessage->schema == 'seda') {
                if (isset($messageObject->archive)) {
                    foreach ($messageObject->archive as $archive) {
                        if (isset($archive->document)) {
                            foreach ($archive->document as $document) {
                                if (isset($document->integrity)) {
                                    $document->integrity->hashAlgorithm = substr($document->integrity->algorithme, strrpos($document->integrity->algorithme, "#") + 1);
                                }
                            }
                        }
                    }
                }
            }
            if (isset($messageObject->archive)) {
                foreach ($messageObject->archive as $archive) {
                    if (isset($archive->appraisalRule)) {
                        $dateInter = new \DateInterval($archive->appraisalRule->duration);
                        $numberDuration = 0;
                        $toDisplay = '';
                        
                        if ($dateInter->y != 0) {
                            if ($dateInter->y == 999999999) {
                                $toDisplay = "Unlimited";
                            } else {
                                $numberDuration = $dateInter->y;
                                $toDisplay = "Year(s)";
                            }
                        } elseif ($dateInter->m != 0) {
                            $numberDuration = $dateInter->m;
                            $toDisplay = "Month(s)";
                        } elseif ($dateInter->d != 0) {
                            $numberDuration = $dateInter->d;
                            $toDisplay = "Day(s)";
                        }
                        $archive->appraisalRule->durationNumber = $numberDuration;
                        $archive->appraisalRule->durationToDisplay = $toDisplay;
                    }
                }
            }
            $messageObjects[] = $messageObject;
        }

        end($messageObjects)->last = true;
        $this->view->addContentFile("medona/message/messageModal.html");

        $messageViewer = $this->view->getElementById('messageViewer');

        $messageView = $presenter->read($messageObjects);
        $this->view->addContent($messageView, $messageViewer);
        /*if ($baseMessage->schema == 'medona') {
            $this->view->addContentFile('medona/message/message.html', $messageViewer);

            // Append description specific form
            $descriptionMetadataDiv = $this->view->getElementById('descriptiveMetadata');
            if (isset($messageObjects[0]->dataObjectPackage)) {
                $this->view->addContentFile($messageObjects[0]->dataObjectPackage->descriptiveMetadataClass.'.html', $descriptionMetadataDiv);
                if ($messageObjects[0]->dataObjectPackage->descriptiveMetadataClass == 'medona/archivePackage') {
                    foreach ($messageObjects[0]->dataObjectPackage->descriptiveMetadata->archive as $archive) {
                        if (isset($archive->descriptionObject)) {
                            $archive->descriptionObject->json = json_encode($archive->descriptionObject);
                        }
                    }
                }
            }
        }*/

        if ($this->view->getElementsByClass("dataTable")->item(2)) {
            $dataTable = $this->view->getElementsByClass("dataTable")->item(2)->plugin['dataTable'];
            $dataTable->setPaginationType("full_numbers");
            $dataTable->setSorting(array(array(0, 'desc')));
        }
        
        $this->view->setSource('messages', $messageObjects);
        $this->view->setSource('unitIdentifiers', $unitIdentifiers);
        $this->view->merge();

        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Get a message as a certificate
     * @param medona/message $message The message objects
     *
     * @return string
     */
    public function getCertificate($message)
    {
        $message->last = true;
        $messages = array($message);

        $this->view->addContentFile("medona/message/messageModal.html");
        $this->view->setSource('messages', $messages);
        $this->view->merge();

        $messageSerializer = $this->getMessageTypeSerializer($message);
        $html = $messageSerializer->display($message);
        $html = utf8_decode($html);
        if ($html) {
            $messageContent = $this->view->createDocumentFragment();
            $messageContent->appendHTML($html);

            $messageDiv = $this->view->getElementById($message->messageId);
            $messageDiv->appendChild($messageContent);
        }
        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Get a form to search resource
     * @param recordsManagement/digitalResource $digitalResource The digital resouce
     *
     * @return string
     */
    public function getDataObjectAttachment($digitalResource)
    {
        $contents = $digitalResource->getContents();
        $mimetype = $digitalResource->mimetype;
        \laabs::setResponseType($mimetype);

        switch ($mimetype) {
            case 'application/xml':
            case 'text/xml':
                $dom = new \DOMDocument();
                $dom->formatOutput = true;

                $dom->loadXml($contents);

                $contents = "<pre>".htmlentities(preg_replace('#\>\n(\s*\n)*#', ">\n", $dom->saveXml()))."</pre>";
                \laabs::setResponseType("text/html");
                break;

            default:
                break;
        }

        return $contents;
    }

    /**
     * Get a form to search resource
     * @param string $mimetype The mime type
     *
     * @return string
     */
    public function showDataObjectAttachmentsContent($mimetype)
    {
        $this->view->addContentFile("recordsManagement/archive/modalViewDocument.html");
        $this->view->translate();

        //$this->view->setSource('url', $url);
        $this->view->setSource('mimetype', $mimetype);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * List archive conversion request
     *
     * @param array $archivesConversionRequest Array of medona/message object
     *
     * @return string
     */
    public function listConversionRequest($archivesConversionRequest)
    {
        $this->view->addContentFile("medona/message/conversionRequestList.html");

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(3);

        $this->view->translate();

        $this->view->setSource('archivesConversionRequest', $archivesConversionRequest);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Get the message import form
     * @param string $messageZip The message zip
     *
     * @return string
     */
    public function messageExport($messageZip)
    {
        \laabs::setResponseType("application/zip");

        return $messageZip;
    }

    //JSON

    /**
     * Get messages numbers
     * @param array $messagesCount Array of number for each message type
     *
     * @return array
     */
    public function countMessages($messagesCount)
    {
        return json_encode($this->count($messagesCount));
    }

    /**
     * Serializer JSON for acceptArchiveTransfer method
     *
     * @return type
     */
    public function acceptArchiveTransfer()
    {
        $this->json->message = $this->translator->getText("Message accepted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveTransfer method
     *
     * @return type
     */
    public function rejectArchiveTransfer()
    {
        $this->json->message = $this->translator->getText("Message rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for validateArchiveTransfer method
     *
     * @return type
     */
    public function validateArchiveTransfer()
    {
        $this->json->message = $this->translator->getText("Message validated");

        return $this->json->save();
    }

    /**
     * Serializer JSON for processBash method
     *
     * @return type
     */
    public function processArchiveTransfer()
    {
        $this->json->message = $this->translator->getText("Message processed");

        return $this->json->save();
    }

    /**
     * Serializer JSON for derogationDeliveryRequest method
     *
     * @return type
     */
    public function derogationDeliveryRequest()
    {
        $this->json->message = $this->translator->getText("Message sent to derogation");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveTransfer method
     *
     * @return type
     */
    public function rejectDeliveryRequest()
    {
        $this->json->message = $this->translator->getText("Message rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveTransfer method
     *
     * @return type
     */
    public function rejectArchiveDestructionRequest()
    {
        $this->json->message = $this->translator->getText("Message rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectArchiveTransfer method
     *
     * @return type
     */
    public function validateArchiveDestructionRequest()
    {
        $this->json->message = $this->translator->getText("Message accepted");

        return $this->json->save();
    }


    /**
     * Serializer JSON for sendAuthorizationOriginatingAgencyRequest method
     *
     * @return type
     */
    public function sendAuthorizationOriginatingAgencyRequest()
    {
        $this->json->message = $this->translator->getText("Authorization request send to originator agency");

        return $this->json->save();
    }

    /**
     * Serializer JSON for sendAuthorizationControlAuthorityRequest method
     *
     * @return type
     */
    public function sendAuthorizationControlAuthorityRequest()
    {
        $this->json->message = $this->translator->getText("Authorization request send to control authority");

        return $this->json->save();
    }

    /**
     * Serializer JSON for acceptAuthorizationOriginatingAgencyRequest method
     *
     * @return type
     */
    public function acceptAuthorizationOriginatingAgencyRequest()
    {
        $this->json->message = $this->translator->getText("Authorization accepted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectAuthorizationOriginatingAgencyRequest method
     *
     * @return type
     */
    public function rejectAuthorizationOriginatingAgencyRequest()
    {
        $this->json->message = $this->translator->getText("Authorization rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for acceptAuthorizationControlAuthorityRequest method
     *
     * @return type
     */
    public function acceptAuthorizationControlAuthorityRequest()
    {
        $this->json->message = $this->translator->getText("Authorization accepted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for rejectAuthorizationControlAuthorityRequest method
     *
     * @return type
     */
    public function rejectAuthorizationControlAuthorityRequest()
    {
        $this->json->message = $this->translator->getText("Authorization rejected");

        return $this->json->save();
    }

    /**
     * Serializer JSON for acceptArchiveTransfer method
     *
     * @return type
     */
    public function retry()
    {
        $this->json->message = $this->translator->getText("Message restarted");

        return $this->json->save();
    }

    /**
     * Serializer JSON for invalid message exception
     * @param Exception $exception The exception
     *
     * @return object JSON object with a status
     */
    public function invalidMessageException($exception)
    {
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getFormat());
        $this->json->message = vsprintf($this->json->message, $exception->getVariables());

        if (count($exception->errors)) {
            foreach ($exception->errors as $error) {
                if (method_exists($error,'getFormat')) {
                    $format = $this->translator->getText($error->getFormat());
                    $error->setMessage(vsprintf($format, $error->getVariables()));
                }
            }
        }
        $this->json->errors = $exception->errors;

        return $this->json->save();
    }

    /**
     * Serializer JSON for invalid status exception
     * @param Exception $exception The exception
     *
     * @return object JSON object with a status
     */
    public function invalidStatusException(\Exception $exception)
    {
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }

    /**
     * Serializer JSON for receive method
     * @param medona/message $ack The ack message object
     *
     * @return object JSON object with a status
     */
    public function receive($ack)
    {
        $this->json->status = true;
        $this->json->message = $this->translator->getText("Message received");
        $this->json->messageId = (string) $ack->receivedMessageId;
        if (isset($ack->xml)) {
            $this->json->acknowledgement = $ack->xml->saveXML();
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for validate method
     *
     * @return object JSON object with a status
     */
    public function validate()
    {
        $this->json->status = true;
        $this->json->message = $this->translator->getText("Message received");

        return $this->json->save();
    }

    /**
     * Load organization name
     * @param object $message            The message
     * @param object $messages           The message list
     * @param object $messageObject      The message object
     * @param string $registrationNumber The current user registration number
     */
    protected function setMessageActions($message, $messages, $messageObject, $registrationNumber)
    {

        if (in_array($message->recipientOrgRegNumber, $registrationNumber)) {
            $messageId = (string) $messageObject->messageId;

            switch ($messageObject->type) {
                case 'ArchiveTransfer':
                    if ($message->status == "received" || $message->status == "modified") {
                        if (!$message->isIncoming) {
                            $messageObject->acknowledgeButton = "/restitution/".$messageId."/Acknowledge";
                        }
                        $messageObject->validateButton = "/transferValidate/" . $messageId;
                        $messageObject->exportButton = "/medona/message/".$messageId."/Export";
                    } elseif ($message->status == "valid") {
                        $messageObject->acceptButton = "/transferAcceptance/".$messageId;
                        $messageObject->rejectButton = "/transferRejection/".$messageId;
                        $messageObject->exportButton = "/medona/message/".$messageId."/Export";
                    } elseif ($message->status == "toBeModified") {
                        $messageObject->rejectButton = "/transferRejection/".$messageId;
                        $messageObject->retryButton = "/medona/message/". $message->messageId . "/retry";
                        if ($message->schema === 'seda') {
                            $messageObject->modifyButton = "/transferModify/" . $message->messageId;
                        }
                    } elseif ($message->status == "accepted") {
                        $messageObject->processButton = "/transferProcess/".$messageId;
                        $messageObject->exportButton = "/medona/message/".$messageId."/Export";
                    } elseif ($message->status == "sent") {
                        $messageObject->exportButton = "/outgoingTransfer/".$messageId."/Export";
                    } elseif ($message->status == "downloaded") {
                        $messageObject->acknowledgeButton = "/outgoingTransfer/".$messageId."/Acknowledge";
                        $messageObject->rejectButton = "/outgoingTransfer/".$messageId."/Reject";
                        $messageObject->exportButton = "/medona/message/".$messageId."/Export";
                    } elseif ($message->status == "processing") {
                        $messageObject->retryButton = "/medona/message/". $message->messageId . "/retry";
                    }
                    break;

                case 'Acknowledgement':
                    $messageObject->exportButton = "/medona/message/".$messageId."/Export";
                    break;

                case 'ArchiveDeliveryRequest':
                    if (count($messages) == 1) {
                        if ($message->status === "sent") {
                            $messageObject->rejectButton = "/delivery/".$messageId."/Reject";
                            $messageObject->derogationButton = "/delivery/".$messageId."/Derogation";
                        }
                    }
                    break;

                case 'ArchiveDeliveryRequestReply':
                    if (count($messages) == 1) {
                        if ($message->status == "sent") {
                            $messageObject->exportButton = "/delivery/".$messageId."/Export";
                        }
                    }
                    break;

                case 'ArchiveRestitutionRequest':
                    if (count($messages) == 1) {
                        if ($message->status == "sent") {
                            $messageObject->rejectButton = "/restitutionRequest/".$messageId."/Reject";
                            $messageObject->acceptWarningButton = "/restitutionRequest/".$messageId."/Accept";
                        }
                    } elseif ($message->status == "accepted") {
                        $messageObject->processButton = "/restitutionRequest/".$messageId."/process";
                    }
                    break;

                case 'ArchiveRestitution':
                    if ($message->status == "sent") {
                        $messageObject->exportButton = "/restitution/".$messageId."/Export";
                    } elseif ($message->status == "received") {
                        $messageObject->rejectButton = "/restitution/".$messageId."/Reject";
                        $messageObject->acknowledgeButton = "/restitution/".$messageId."/Acknowledge";
                    } elseif ($message->status == "acknowledge") {
                        $messageObject->validationButton = "/restitution/".$messageId."/process";
                        $messageObject->exportButton = "/medona/message/".$messageId."/Export";
                    }
                    break;

                case 'ArchiveDestructionRequest':
                    if ($message->status == "accepted") {
                        $messageObject->validationButton = "/destructionRequest/".$messageId."/Accept";
                        //$messageObject->rejectButton = "/destructionRequest/".(string) $message->messageId."/Reject";
                    }
                    break;

                case 'AuthorizationOriginatingAgencyRequest':
                    $messageObject->acceptButton = "/authorizationOriginatingAgencyRequest/".(string) $message->messageId."/Accept";
                    $messageObject->rejectButton = "/authorizationOriginatingAgencyRequest/".(string) $message->messageId."/Reject";
                    break;

                case 'AuthorizationOriginatingAgencyRequestReply':
                    if (($messages[0]->status != "originator_authorization_wait" && $messages[0]->status != "control_authorization_wait") || ($message != end($messages))) {
                        break;
                    }

                    if (strtoupper($message->replyCode) == "000") {
                        if ($messages[0]->type == "ArchiveDeliveryRequest") {
                            $messageObject->acceptButton = "/deliveryRequest/".(string) $messages[0]->messageId."/Accept";
                            $messageObject->sendRequestAuth = "/authorizationControlAuthorityRequest/".(string) $messages[0]->messageId;
                        }

                        if ($messageObject[0]->type == "ArchiveDestructionRequest") {
                            $messageObject->acceptButton = "/destructionRequest/".(string) $messages[0]->messageId."/Accept";
                            $messageObject->sendRequestAuth = "/authorizationControlAuthorityRequest/".(string) $messages[0]->messageId;
                        }
                    } else {
                        if ($messages[0]->type == "ArchiveDeliveryRequest") {
                            $messageObject->rejectButton = "/deliveryRequest/".$messageId."/Reject";
                        }

                        if ($messages[0]->type == "ArchiveDestructionRequest") {
                            $messageObject->rejectButton = "/destructionRequest/".(string) $messages[0]->messageId."/Reject";
                        }
                    }
                    break;

                case 'AuthorizationControlAuthorityRequest':
                    $messageObject->acceptButton = "/authorizationControlAuthorityRequest/".(string) $message->messageId."/Accept";
                    $messageObject->rejectButton = "/authorizationControlAuthorityRequest/".(string) $message->messageId."/Reject";
                    break;

                case 'AuthorizationControlAuthorityRequestReply':
                    if (strtoupper($message->replyCode) == "000") {
                        if ($messages[0]->type == "ArchiveDeliveryRequest") {
                            $messageObject->acceptButton = "/deliveryRequest/".(string) $messages[0]->messageId."/Accept";
                        }
                    } else {
                        if ($messages[0]->type == "ArchiveDeliveryRequest") {
                            $messageObject->rejectButton = "/deliveryRequest/".(string) $messages[0]->messageId."/Reject";
                        }
                    }
                    break;
                /*
                */
            }
        } elseif (in_array($message->senderOrgRegNumber, $registrationNumber)) {
            $messageId = (string) $messageObject->messageId;

            switch ($messageObject->type) {
                case 'ArchiveRestitution':
                    if ($message->status == "acknowledge") {
                        $messageObject->validationButton = "/restitution/".$messageId."/process";
                    }
                    break;
                case 'ArchiveTransfer' :
                    if ($message->status == "acknowledge") {
                        $messageObject->validationButton = "/outgoingTransfer/".$messageId."/process";
                    }
                    break;
            }
        }
    }

    /**
     * Load organization name
     * @param object         $object
     * @param medona/message $message
     */
    protected function loadOrganizationName($object, $message)
    {
        $organization = null;

        if (!isset($object->identifier) && !isset($object->identification->value)) {
            return;
        }

        if (isset($object->identifier)) {
            $name = 'identifier';
        } else {
            $name = 'identification';
        }

        if ($object->{$name}->value == $message->recipientOrg->registrationNumber) {
            $organization = $message->recipientOrg;
        } elseif ($object->{$name}->value == $message->senderOrg->registrationNumber) {
            $organization = $message->senderOrg;
        } else {
            try {
                $organization = \laabs::callService('organization/organization/readByregnumber', $object->{$name}->value)->orgName;
            } catch (\Exception $e) {
                $organization = "Organisation inconnue (" . $object->{$name}->value . ")";
            }
        }

        return $organization;
    }

    /**
     * Get the message type and schema specific serializer
     * @param medona/message $message The message object
     * @param string         $format  The implementation format
     *
     * @return The parser
     */
    protected function getMessageTypeSerializer($message, $format = "html")
    {
        if (!isset($message->type)) {
            $message->type = $message->xml->documentElement->nodeName;
        }

        if (!isset($message->schema)) {
            $messageNamespace = $message->xml->documentElement->namespaceURI;
            if (!$messageSchema = \laabs::resolveXmlNamespace($messageNamespace)) {
                throw new \Exception('Unknown message namespace'.$messageNamespace);
            }
            $message->schema = $messageSchema;
        }

        $this->messageTypeSerializer = \laabs::newSerializer($message->schema.LAABS_URI_SEPARATOR.$message->type, $format);

        return $this->messageTypeSerializer;
    }

    /**
     * Show message list
     * @param array     $messages Array of message object
     * @param boolean   $history  Type of table (true => history and false => other)
     *
     * @return string The view
     */
    protected function prepareMesageList($messages, $history = null)
    {

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        if ($history) {
            $dataTable->setUnsortableColumns(6);
            $dataTable->setSorting(array(array(4, 'desc')));
        } else {
            $dataTable->setUnsortableColumns(5);
            $dataTable->setSorting(array(array(3, 'desc')));
        }

        $this->view->translate();

        foreach ($messages as $message) {
            $message->status = $this->view->translator->getText($message->status, false, "medona/messages");
            $message->typeTranslate = $this->view->translator->getText($message->type, false, "medona/messages");
        }

        $this->loadSendingModal(false, false);
        $this->view->setSource("messages", $messages);
    }

    /**
     * load sending modal
     * @param type $showOrganization
     * @param type $showReference
     */
    protected function loadSendingModal($showOrganization = true, $showReference = true)
    {
        if ($showOrganization) {
            $orgController = \laabs::newController('organization/organization');

            $organizations = $orgController->index();
            $userOrg = $orgController->listMyOrg();
            $currentOrg = \laabs::getToken("ORGANIZATION");
            if ($currentOrg) {
                foreach ($userOrg as $key => $org) {
                    if ($currentOrg->orgId == $org->orgId) {
                        unset($userOrg[$key]);
                        break;
                    }
                }
            }
            if (count($userOrg) < 2) {
                $userOrg = null;
            }
            $this->view->setSource('organizations', $organizations);
            $this->view->setSource('currentOrg', $currentOrg);
            $this->view->setSource('userOrg', $userOrg);
        }

        $this->view->setSource('reference', $showReference);
    }

    /**
     * Get numbers of messages
     */
    protected function count($messagesCount)
    {
        foreach ($messagesCount as $key => $tab) {
            foreach ($tab as $list => $number) {
                if ($number == "0") {
                    $number = "";
                    $messagesCount[$key][$list] = "";
                }
            }
        }

        return $messagesCount;
    }


    /**
     * Wrong archive infos exceptions
     * @param object $exception The exception
     *
     * @return string String serialized in JSON
     */
    public function notDisposableArchiveException($exception)
    {
        $this->translator->setCatalog('recordsManagement/messages');

        // Manage errors
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }
}
