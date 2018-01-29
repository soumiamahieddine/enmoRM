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
                case 'recordsManagement' :
                    $domainLabel = 'Archive';
                    $objectType = 'recordsManagement/archive';
                    switch ($name) {
                        case 'profileCreation':
                        case 'profileDestruction':
                        case 'archivalProfileModification':
                            $objectType = 'recordsManagement/archivalProfile';
                            $domainLabel = 'Archival profile';
                            break;
                    }
                    break;

                case 'medona' : 
                    $objectType = 'medona/message';
                    $domainLabel = 'Message';
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

        foreach ($eventDomains as $domain => $eventDomain) {
            // Sort by translated event type using locale and normalized string
            uasort($eventDomain->eventTypes, function ($eventType1, $eventType2) {
                return strcoll(\laabs::normalize($eventType1->label), \laabs::normalize($eventType2->label));
            });

            //$eventDomains[$domain] = $eventTypes;
        }



        if (!\laabs::hasBundle('medona')) {
            $messageObjectType = $this->view->XPath->query('//option[@value="medona/message"]')->item(0)->setAttribute('class', 'hide');
        }

        $this->view->setSource("eventType", $eventDomains);

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
     * @param array $events The list of events
     *
     * @return string
     */
    public function searchEvent($events)
    {
        $this->view->addContentFile("lifeCycle/searchResult.html");
        
        $conf = \laabs::configuration('lifeCycle');
        if (is_array($conf) && array_key_exists('separateInstance', $conf)) {
            $multipleInstance = !(bool) $conf['separateInstance'];
        } else {
            $multipleInstance = false;

        }

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

        $objectClass = \laabs\dirname($event->objectClass);
        $this->translator->setCatalog('lifeCycle/messages');
        
        $eventObject->description = $this->translator->getText($event->description);
        $eventObject->objectClass = $this->translator->getText($event->objectClass);
        $eventObject->eventType = $this->translator->getText($event->eventType);
        $this->json->load($eventObject);

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
}
