<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle lifeCycle.
 *
 * Bundle lifeCycle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle lifeCycle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle lifeCycle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\lifeCycle;

/**
 * Serializer html journal
 *
 * @package lifeCycle
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.com>
 */
class journal
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;

    private $sdoFactory;

    private $eventsFormat;

    private $translator;

    /**
     * Constuctor of archival Agreement html serializer
     * @param \dependency\html\Document $view         The view
     * @param \dependency\sdo\Factory   $sdoFactory   The sdo factory
     * @param array                     $eventsFormat The events format
     * @param \dependency\json\JsonObject                  $json
     * @param \dependency\localisation\TranslatorInterface $translator
     */
    public function __construct(
            \dependency\html\Document $view,
            \dependency\sdo\Factory $sdoFactory,
            $eventsFormat,
            \dependency\json\JsonObject $json,
            \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;
        $this->sdoFactory = $sdoFactory;
        $this->eventsFormat = $eventsFormat;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('lifeCycle/messages');
    }

    /**
     * Show the events search form
     *
     * @return string
     */
    public function searchForm()
    {
        $this->view->addContentFile('lifeCycle/searchForm.html');

        $eventTypes = \laabs::callService('lifeCycle/event/readEventtypelist');

        $this->view->translator->setCatalog("lifeCycle/messages");

        $eventDomains = [];
        foreach ($eventTypes as $eventType) {
            $bundle = strtok($eventType, '/');
            $name = strtok('');

            switch ($bundle) {
                case 'recordsManagement':
                    $domainLabel = 'Archive';
                    $objectType = 'recordsManagement/archive';
                    switch ($name) {
                        case 'profileCreation':
                        case 'profileDestruction':
                        case 'archivalProfileModification':
                            $objectType = 'recordsManagement/archivalProfile';
                            $domainLabel = 'Archival profile';
                            break;

                        case 'periodicIntegrityCheck':
                            $objectType = 'recordsManagement/serviceLevel';
                            $domainLabel = 'Service level';
                            break;
                    }
                    break;

                case 'medona':
                    $objectType = 'medona/message';
                    $domainLabel = 'Message';
                    break;
                case 'organization':
                    $objectType = 'organization/organization';
                    $domainLabel = 'Organization';
                    break;
                case 'digitalResource':
                    $objectType = 'digitalResource/repository';
                    $domainLabel = 'Repository';
                    break;
            }

            $domainLabel = $this->view->translator->getText($domainLabel);
            if (!isset($eventDomains[$objectType])) {
                $eventDomain = new \StdClass();
                $eventDomain->objectType = $objectType;
                $eventDomain->label = $domainLabel;
                $eventDomain->eventTypes = [];

                $eventDomains[$objectType] = $eventDomain;
            }

            $eventTypeLabel = $this->view->translator->getText($eventType);
            $eventOption = new \StdClass();
            $eventOption->value = $eventType;
            $eventOption->label = $eventTypeLabel;

            $eventDomains[$objectType]->eventTypes[] = $eventOption;
        }

        foreach ($eventDomains as $eventDomain) {
            // Sort by translated event type using locale and normalized string
            uasort($eventDomain->eventTypes, function ($eventType1, $eventType2) {
                return strcoll(\laabs::normalize($eventType1->label), \laabs::normalize($eventType2->label));
            });
        }

        $maxResults = null;
        if (isset(\laabs::configuration('presentation.maarchRM')['maxResults'])) {
            $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        }

        $this->view->setSource("eventType", $eventDomains);
        $this->view->setSource("maxResults", $maxResults);

        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Show the journal list
     * @param lifeCycle/journal[] $journals
     *
     * @return string
     */
    public function getJournalList($journals)
    {
        $this->view->addContentFile('lifeCycle/journalList.html');
        $this->view->translate();

        $this->view->setSource('journals', $journals);
        $this->view->merge();

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(2);
        $dataTable->setUnsearchableColumns(2);

        return $this->view->saveHtml();
    }

    /**
     * Show the journal
     * @param lifeCycle/event[] $events The event list
     *
     * @return string
     */
    public function readJournal($events)
    {
        $this->view->addContentFile("lifeCycle/journal.html");
        $this->view->translate();

        foreach ($events as $event) {
            if (isset($this->eventsFormat[$event->eventType])) {
                $event->format = $this->eventsFormat[$event->eventType];
            }
        }

        $this->view->setSource('events', $events);
        $this->view->merge();

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(0);

        return $this->view->saveHtml();
    }

    /**
     * Show the result of the event search
     *
     * @param array   $events       The list of events
     * @param integer $totalResults Max number of results returned from query without limit
     *
     * @return string
     */
    public function searchEvent($events, $totalResults)
    {
        $this->view->addContentFile("lifeCycle/searchResult.html");

        $conf = \laabs::configuration('lifeCycle');
        if (is_array($conf) && array_key_exists('separateInstance', $conf)) {
            $multipleInstance = !(bool) $conf['separateInstance'];
        } else {
            $multipleInstance = false;
        }

        $hasReachMaxResults = false;
        if (isset(\laabs::configuration('presentation.maarchRM')['maxResults'])
            && $totalResults >= \laabs::configuration('presentation.maarchRM')['maxResults']) {
            $hasReachMaxResults = true;
        }

        $this->view->setSource('hasReachMaxResults', $hasReachMaxResults);
        $this->view->setSource('totalResults', $totalResults);
        $this->view->setSource('multipleInstance', $multipleInstance);
        $this->view->setSource('events', $events);
        $this->view->merge();
        $this->view->translate();

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        if ($multipleInstance) {
            $dataTable->setUnsortableColumns(5);
            $dataTable->setSorting(array(array(2, 'desc')));
        } else {
            $dataTable->setUnsortableColumns(4);
            $dataTable->setSorting(array(array(1, 'desc')));
        }
        return $this->view->saveHtml();
    }

    //JSON
    /**
     * Serializer JSON for create method
     * @param lifeCycle/event $event
     *
     * @return object JSON object with a status and message parameters
     */
    public function getEvent($event)
    {
        $newEvent = new \stdClass();
        foreach ($event as $key => $value) {
            $newKey = $this->translator->getText($key);
            $newEvent->$newKey = $value;
        }
        $this->json->event = json_encode($newEvent);

        return $this->json->save();
    }

    /**
     * Serializer JSON for readEvent method
     * @param array $event The event
     *
     * @return string
     */
    public function readEvent($event)
    {
        $eventObject = new \stdClass();

        foreach ($event as $key => $value) {
            $newKey = $this->translator->getText($key, 'eventInfo');
            $eventObject->$newKey = $value;
        }

        $user = \laabs::callService('auth/userAccount/read_userAccountId_', $event->accountId);

        $eventObject->accountDisplayName = $user->displayName.' ('.$user->accountName.')';

        $this->translator->setCatalog('lifeCycle/messages');

        $eventObject->description = $this->translator->getText($event->description);
        $eventObject->objectClass = $this->translator->getText($event->objectClass);
        $eventObject->eventType = $this->translator->getText($event->eventType);

        // check event type to add button "download certificate"
        $hasCertificatePrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "journal/certificate");
        $eventsToCertificate = ['recordsManagement/deposit', 'recordsManagement/integrityCheck', 'recordsManagement/destruction'];

        if ($hasCertificatePrivilege && in_array($event->eventType, $eventsToCertificate)) {
            $eventObject->hasCertificate = true;
        }

        $this->json->load($eventObject);

        $this->json->formatDateTimes();

        return $this->json->save();
    }

    /**
     * Exception
     * @param lifeCycle/Exception/journalException $journalException
     *
     * @return string
     */
    public function journalException($journalException)
    {
        $this->json->load($journalException);
        $this->json->status = false;

        return $this->json->save();
    }

    /**
     * Display a pdf attestation
     *
     * @param  lifeCycle/event $event   Event to certificate
     *
     * @return  pdf            $pdfFile Certificate in pdf format of the event
     */
    public function certificate($event)
    {
        $wkhtmltopdf = \laabs::newService('dependency/fileSystem/plugins/wkhtmltopdf');
        $fragment = $this->view->createDocumentFragment();

        $eventObject = new \stdClass();

        $this->translator->setCatalog('lifeCycle/messages');

        $event->certificateName = $event->eventType;

        foreach ($event as $key => $value) {
            $translatedKey = $this->translator->getText($key, 'eventInfo');
            $eventObject->$key = new \stdClass();
            $eventObject->$key->translatedKey = $translatedKey;
            $eventObject->$key->value = $value;
        }

        $fragment->appendHtmlFile("lifeCycle/certificate.html");
        $eventObject->eventType = $this->translator->getText($eventObject->eventType->value);

        $user = \laabs::callService('auth/userAccount/read_userAccountId_', $eventObject->accountId->value);
        $eventObject->accountDisplayName = new \stdClass();
        $eventObject->accountDisplayName->value = $user->displayName.' ('.$user->accountName.')';
        $eventObject->accountDisplayName->translatedKey = $this->translator->getText('User identifier');

        $eventObject->eventId->translatedKey = $this->translator->getText($eventObject->eventId->translatedKey);
        $eventObject->timestamp->translatedKey = $this->translator->getText('Timestamp');
        $eventObject->operationResult->translatedKey = $this->translator->getText($eventObject->operationResult->translatedKey);
        $eventObject->objectId->translatedKey = $this->translator->getText($eventObject->objectId->translatedKey);
        $eventObject->description->translatedKey = $this->translator->getText($eventObject->description->translatedKey);
        $eventObject->objectClass->translatedKey = $this->translator->getText("Archive identifier");

        $eventObject->certificateName = new \stdClass();
        $eventObject->certificateName->value = $event->eventType;
        $eventObject->certificateName->translatedKey = $this->translator->getText('certificateName', $event->eventType);

        if ($eventObject->operationResult->value) {
            $eventObject->operationResult->value = $this->translator->getText('Success');
        } else {
            $eventObject->operationResult->value = $this->translator->getText('Failure');
        }

        $eventObject->certificationDateTime = new \stdClass();
        $eventObject->certificationDateTime->translatedKey = $this->translator->getText('certificationDateTime');
        $eventObject->certificationDateTime->value = \laabs::newDateTime()->format(\laabs::configuration('dependency.localisation')['dateTimeFormat']);

        if (isset($event->size)) {
            $eventObject->sizeValue = new \stdClass();
            $eventObject->sizeValue->translatedKey = $this->translator->getText('bytes');
        }

        $this->view->setSource('logo', dirname(getcwd()) . '/src/presentation/maarchRM/Resources/public/img/RM.svg');
        $this->view->setSource('title', \laabs::configuration('presentation.maarchRM')['title']);

        $this->view->setSource("event", $eventObject);
        $this->json->load($eventObject);

        $this->view->translate();
        $this->view->merge($fragment);
        $html = $this->view->saveHtml($fragment);

        $exportDirectory = \laabs\tempdir();
        $htmlFile = $exportDirectory . DIRECTORY_SEPARATOR . $eventObject->eventId->value . '.html';
        $pdfFile = $exportDirectory . DIRECTORY_SEPARATOR . $eventObject->eventId->value . '.pdf';
        file_put_contents($htmlFile, $html);

        $wkhtmltopdf->send($htmlFile, $pdfFile);
        unlink($htmlFile);

        \laabs::setResponseType('application/pdf');
        $response = \laabs::kernel()->response;
        $response->setHeader("Content-Disposition", "inline;");

        return file_get_contents($pdfFile);
    }
}
