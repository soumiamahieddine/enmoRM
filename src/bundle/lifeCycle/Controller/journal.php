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

namespace bundle\lifeCycle\Controller;

use function laabs\hash_stream;

/**
 * Class of archives life cycle journal
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class journal
{
    protected $sdoFactory;
    protected $separateInstance;
    protected $interval;
    protected $currentOffset;

    // Journal files reading
    protected $currentJournalFilePath;
    protected $currentJournalId;
    protected $currentEvent;
    protected $journalCursor;
    protected $eventFormats;
    protected $journalHandler;

    protected $journals;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory       The sdo factory
     * @param boolean                 $separateInstance Read only instance events
     * @param integer                 $interval         The time bewteen 2 journal changes
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $separateInstance = false, $interval = 86400)
    {
        $this->separateInstance = $separateInstance;
        $this->interval = $interval;
        $this->sdoFactory = $sdoFactory;

        $this->currentJournalFilePath = null;
        $this->currentJournalId = null;
        $this->currentOffset = 0;

        $this->eventFormats = $this->sdoFactory->index('lifeCycle/eventFormat');
        foreach ($this->eventFormats as $eventFormat) {
            $eventFormat->format = explode(' ', $eventFormat->format);
        }
    }

    /**
     * Get event type list
     *
     * @return lifeCycle/eventFormat[] The eventType list
     */
    public function listEventType()
    {
        return $this->sdoFactory->index('lifeCycle/eventFormat', 'type');
    }

    /**
     * Add an event to the journal
     * @param string $eventType       The type of the event
     * @param string $objectClass     The aimed object class
     * @param string $objectId        The aimed object id
     * @param mixed  $context         The description of the event
     * @param bool   $operationResult The operation result
     *
     * @throws \Exception
     *
     * @return lifeCycle/event The new event
     */
    public function logEvent($eventType, $objectClass, $objectId, $context = null, $operationResult = true)
    {
        $event = \laabs::newInstance('lifeCycle/event');

        $event->eventId = \laabs::newId();
        $event->instanceName = \laabs::getInstanceName();

        $event->timestamp = \laabs::newTimestamp();
        $event->eventType = $eventType;
        $event->objectClass = $objectClass;
        $event->objectId = $objectId;
        $event->operationResult = $operationResult;
        $event->description = "";

        if ($accountToken = \laabs::getToken('AUTH')) {
            $event->accountId = $accountToken->accountId;
        } else {
            $event->accountId = '__system__';
        }

        if ($currentOrganization = \laabs::getToken("ORGANIZATION")) {
            $organizationController = \laabs::newController('organization/organization');
            $organization = $organizationController->read($currentOrganization->ownerOrgId);

            $event->orgRegNumber = $organization->registrationNumber;
            $event->orgUnitRegNumber = $currentOrganization->registrationNumber;
        }

        $arrayToMerge[] = (string) $event->eventId;
        $arrayToMerge[] = $event->eventType;
        $arrayToMerge[] = (string) $event->timestamp;
        $arrayToMerge[] = (string) $event->accountId;
        $arrayToMerge[] = $event->objectClass;
        $arrayToMerge[] = (string) $event->objectId;
        $arrayToMerge[] = $event->operationResult;
        $arrayToMerge[] = $event->description; // 8th line

        $eventInfo = array();

        // Event info from position 9 to ...
        if ($context) {
            if (is_object($context)) {
                $context = get_object_vars($context);
            }

            if (!isset($this->eventFormats[$event->eventType])) {
                throw \laabs::newException("lifeCycle/journalException", "Unknown event type: %s", 404, null, [$event->eventType]);
            }

            $eventFormat = $this->eventFormats[$event->eventType];
            foreach ($eventFormat->format as $item) {
                if (isset($context[$item])) {
                    $eventInfo[] = $context[$item];
                    $arrayToMerge[] = $context[$item];
                } else {
                    $eventInfo[] = "";
                    $arrayToMerge[] = "";
                }
            }
        }

        if (!empty($eventInfo)) {
            $event->eventInfo = json_encode($eventInfo);
        }

        $event->description = vsprintf($eventFormat->message, $arrayToMerge);

        $this->sdoFactory->create($event);

        if (!$operationResult && $eventFormat->notification == true) {
            $this->notify($event);
        }

        return $event;
    }

    /**
     * Retrieve an archive event in a lifeCycle journal
     * @param mixed  $date   The journal date
     * @param string $needle The string to search in the journal
     *
     * @return object[] Array of life cycle event
     */
    public function matchEvent($date, $needle)
    {
        $events = [];
        $logController = \laabs::newController('recordsManagement/log');

        $tmpDir = \laabs::getTmpDir();
        $journal = $logController->getByDate('lifeCycle', (string) $date);

        if (!$journal) {
            return $events;
        }

        $archiveController = \laabs::newController('recordsManagement/archive');
        $digitalResourceController = \laabs::newController('digitalResource/digitalResource');

        if (!\laabs\file_exists($tmpDir . DIRECTORY_SEPARATOR . $journal->archiveId)) {
            $resources = $archiveController->getDigitalResources($journal->archiveId, $checkAccess = false);
            $journalResource = $resources[0];

            $journalContents = $digitalResourceController->contents($journalResource->resId);

            if (!file_put_contents($tmpDir . DIRECTORY_SEPARATOR . $journal->archiveId, $journalContents)) {
                throw \laabs::newException("lifeCycle/journalException", "Journal file cannot be written");
            }
        } else {
            $journalContents = file_get_contents($tmpDir . DIRECTORY_SEPARATOR . $journal->archiveId);
        }

        $offset = 0;

        do {
            $offset = strpos($journalContents, (string) $needle, $offset);

            if ($offset) {
                $journalLength = strlen($journalContents);
                $startOffset = strrpos($journalContents, "\n", -$journalLength + $offset) + 1;
                $endOffset = strpos($journalContents, "\n", $startOffset);
                $eventLine = substr($journalContents, $startOffset, $endOffset - $startOffset);

                $events[] = $this->getEventFromLine($eventLine);
                $offset = $endOffset;
            }
        } while ($offset);

        return $events;
    }

    /**
     * Get the events for a given object id and class
     * @param string $objectId    The identifier of the object
     * @param string $objectClass The class of the object
     * @param mixed  $eventType   An event type or an array of event types to retrieve
     *
     * @throws \Exception
     *
     * @return object[] Array of life cycle event
     */
    public function getObjectEvents($objectId, $objectClass, $eventType = null)
    {
        $query = "objectId='$objectId' AND objectClass='$objectClass'";

        if ($eventType) {
            if (is_array($eventType)) {
                $query .= " AND eventType=['".\laabs\implode("', '", $eventType)."']";
            } else {
                $query .= " AND eventType='$eventType'";
            }
        }

        $events = $this->sdoFactory->find('lifeCycle/event', $query, [], 'timestamp');

        foreach ($events as $key => $event) {
            $events[$key] = $this->decodeEventFormat($event);
        }

        return $events;
    }

    /**
     * Get an events by id from journal file
     * @param mixed $eventId The event or the identifier of the event
     *
     * @throws \Exception
     *
     * @return string An event
     */
    public function getEventFromJournal($eventId)
    {
        if (is_scalar($eventId) || get_class($eventId) == 'core\Type\Id') {
            $eventFromBase = $this->sdoFactory->read('lifeCycle/event', $eventId);
        } else {
            $eventFromBase = $eventId;
        }

        $logController = \laabs::newController('recordsManagement/log');

        // Read journal file
        $journalReference = $logController->getByDate('lifeCycle', $eventFromBase->timestamp);

        if ($journalReference) {
            $this->openJournal($journalReference->archiveId);

            // Get the eventId position on the journal file
            while ($row = fgetcsv($this->journalHandler)) {
                if ($row[0] == (string) $eventFromBase->eventId) {
                    $eventLine = '"' . implode('","', $row);
                    break;
                }
            }

            // Place cursor to the begin of line
            if ($this->journalHandler) {
                // Get the event
                $event = $this->getEventFromLine($eventLine);

                // Retrieves information that is not in the csv file from database
                $event->instanceName = $eventFromBase->instanceName;
                $event->orgRegNumber = $eventFromBase->orgRegNumber;
                $event->orgUnitRegNumber = $eventFromBase->orgUnitRegNumber;
            } else {
                $event = $eventFromBase;
                return $event;

                //throw \laabs::newException("lifeCycle/journalException", "Event can't be found.");
            }
        } else {
            $event = $eventFromBase;
            $this->decodeEventFormat($event);
        }

        $this->currentEvent = $event;

        return $event;
    }

    /**
     * Search a journal event
     *
     * @param string    $eventType   The type of the event
     * @param string    $objectClass The object class
     * @param string    $objectId    The identifier of the object
     * @param timestamp $minDate     The minimum date of the event
     * @param timestamp $maxDate     The maximum date of the event
     * @param string    $org         An org reg number on one of the event header or info
     * @param string    $sortBy      The event sorting request
     * @param integer   $maxResults  The number of result
     *
     * @throws \Exception
     *
     * @return object[] The result of the request
     */
    public function searchEvent(
        $eventType = null,
        $objectClass = null,
        $objectId = null,
        $minDate = null,
        $maxDate = null,
        $org = null,
        $sortBy = ">timestamp",
        $maxResults = null
    ) {
        list($queryParams, $queryString) = $this->queryBuilder($eventType, $objectClass, $objectId, $minDate, $maxDate, $org);

        $events = $this->sdoFactory->find(
            'lifeCycle/event',
            $queryString,
            $queryParams,
            $sortBy,
            null,
            $maxResults
        );

        $userAccountController = \laabs::newController('auth/userAccount');
        $users = $userAccountController->index();
        foreach ($users as $i => $user) {
            $users[(string) $user->accountId] = $user;
            unset($users[$i]);
        }

        $serviceAccountController = \laabs::newController('auth/serviceAccount');
        $services = $serviceAccountController->index();
        foreach ($services as $i => $service) {
            $services[(string) $service->accountId] = $service;
            unset($services[$i]);
        }

        foreach ($events as $i => $event) {
            if (isset($event->accountId) && isset($users[(string) $event->accountId])) {
                $event->accountName = $users[(string) $event->accountId]->accountName;
            } elseif (isset($event->accountId) && isset($services[(string) $event->accountId])) {
                $event->accountName = $services[(string) $event->accountId]->accountName;
            } else {
                $event->accountName = "__system__";
            }
        }

        return $events;
    }

    /**
     * Count journal events
     *
     * @param string    $eventType      The type of the event
     * @param string    $objectClass    The object class
     * @param string    $objectId       The identifier of the object
     * @param timestamp $minDate        The minimum date of the event
     * @param timestamp $maxDate        The maximum date of the event
     * @param string    $org            An org reg number on one of the event header or info
     * @param string    $sortBy         The event sorting request
     *
     * @throws \Exception
     *
     * @return integer Count results of request
     */
    public function searchCount(
        $eventType = null,
        $objectClass = null,
        $objectId = null,
        $minDate = null,
        $maxDate = null,
        $org = null
    ) {
        list($queryParams, $queryString) = $this->queryBuilder($eventType, $objectClass, $objectId, $minDate, $maxDate, $org);

        $count = $this->sdoFactory->count(
            'lifeCycle/event',
            $queryString,
            $queryParams
        );

        return $count;
    }

    /**
     * Build a search sql query
     *
     * @param string    $eventType      The type of the event
     * @param string    $objectClass    The object class
     * @param string    $objectId       The identifier of the object
     * @param timestamp $minDate        The minimum date of the event
     * @param timestamp $maxDate        The maximum date of the event
     * @param string    $org            An org reg number on one of the event header or info
     */
    protected function queryBuilder(
        $eventType = null,
        $objectClass = null,
        $objectId = null,
        $minDate = null,
        $maxDate = null,
        $org = null
    ) {
        $query = [];
        $queryParams = [];

        if ($this->separateInstance) {
            $queryParams['instanceName'] = \laabs::getInstanceName();
            $query['instanceName'] = "instanceName = :instanceName";
        }

        if ($eventType) {
            $queryParams['eventType'] = $eventType;
            $query['eventType'] = "eventType = :eventType";
        }

        if ($objectClass) {
            $queryParams['objectClass'] = $objectClass;
            $query['objectClass'] = "objectClass = :objectClass";
        }

        if ($objectId) {
            $queryParams['objectId'] = $objectId;
            $objectIdQueryParts['objectId'] = "objectId = :objectId";

            // Use on event info, depending on object class and eventType
            $businessIdQueryParts = [];
            // Query parts for archive or global
            if (empty($objectClass) || $objectClass == 'recordsManagement/archive') {
                // Bind originatorArchiveId to the right column for each event
                if (empty($eventType) || $eventType = 'recordsManagement/deposit'
                    || $eventType = 'recordsManagement/depositNewResource') {
                    $businessIdQueryParts[] = '"eventInfo"::jsonb->>9 = :objectId';
                }
                if (empty($eventType) || $eventType = 'recordsManagement/addRelationship'
                    || $eventType = 'recordsManagement/deleteRelationship'
                    || $eventType = 'recordsManagement/delivery'
                    || $eventType = 'recordsManagement/destruction'
                    || $eventType = 'recordsManagement/destructionRequest'
                    || $eventType = 'recordsManagement/destructionRequestCanceling'
                    || $eventType = 'recordsManagement/elimination'
                    || $eventType = 'recordsManagement/outgoingTransfer'
                    || $eventType = 'recordsManagement/restitution'
                    || $eventType = 'recordsManagement/restitutionRequest'
                    || $eventType = 'recordsManagement/restitutionRequestCanceling') {
                    $businessIdQueryParts[] = '"eventInfo"::jsonb->>7 = :objectId';
                }
                if (empty($eventType) || $eventType = 'recordsManagement/consultation'
                    || $eventType = 'recordsManagement/periodicIntegrityCheck') {
                    $businessIdQueryParts[] = '"eventInfo"::jsonb->>5 = :objectId';
                }
                if (empty($eventType) || $eventType = 'recordsManagement/conversion'
                    || $eventType = 'recordsManagement/depositOfLinkedResource'
                    || $eventType = 'recordsManagement/accessRuleModification') {
                    $businessIdQueryParts[] = '"eventInfo"::jsonb->>10 = :objectId';
                }
                if (empty($eventType) || $eventType = 'recordsManagement/freeze'
                    || $eventType = 'recordsManagement/unfreeze'
                    || $eventType = 'recordsManagement/integrityCheck'
                    || $eventType = 'recordsManagement/metadataModification'
                    || $eventType = 'recordsManagement/resourceDestruction') {
                    $businessIdQueryParts[] = '"eventInfo"::jsonb->>6 = :objectId';
                }
                if (empty($eventType) || $eventType = 'recordsManagement/retentionRuleModification') {
                    $businessIdQueryParts[] = '"eventInfo"::jsonb->>12 = :objectId';
                }
            }

            if (empty($objectClass) || $objectClass == 'medona/message') {
                $businessIdQueryParts[] = '"eventInfo"::jsonb->>5 = :objectId';
            }

            if (!empty($businessIdQueryParts)) {
                $objectIdQueryParts['businessId'] = '<?SQL ('.implode(' OR ', $businessIdQueryParts) .' ) ?>';
            }

            $query['objectId'] = '('.implode(' OR ', $objectIdQueryParts).')';
        }

        if ($minDate) {
            $queryParams['minDate'] = $minDate;
            $query['minDate'] = "timestamp >= :minDate";
        }

        if ($maxDate) {
            $queryParams['maxDate'] = $maxDate->add(new \DateInterval('PT23H59M59S'));
            $query['maxDate'] = "timestamp <= :maxDate";
        }

        if ($org) {
            $queryParams['org'] = $org;
            $orgQueryParts['org'] = "orgRegNumber = :org OR orgUnitRegNumber = :org";

            // Use on event info, depending on object class and eventType
            $infoOrgQueryParts = [];
            // Query parts for archive or global
            if (empty($objectClass) || $objectClass == 'recordsManagement/archive') {
                // Bind org query parts according to type of event
                if (empty($eventType) || $eventType = 'recordsManagement/deposit'
                    || $eventType = 'recordsManagement/depositNewResource'
                    || $eventType = 'recordsManagement/depositOfLinkedResource') {
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>4 = :org';
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>5 = :org';
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>6 = :org';
                }
                if (empty($eventType) || $eventType = 'recordsManagement/delivery'
                    || $eventType = 'recordsManagement/destruction'
                    || $eventType = 'recordsManagement/deleteRelationship'
                    || $eventType = 'recordsManagement/addRelationship'
                    || $eventType = 'recordsManagement/destruction'
                    || $eventType = 'recordsManagement/destructionRequest'
                    || $eventType = 'recordsManagement/destructionRequestCanceling'
                    || $eventType = 'recordsManagement/elimination'
                    || $eventType = 'recordsManagement/freeze'
                    || $eventType = 'recordsManagement/unfreeze'
                    || $eventType = 'recordsManagement/outgoingTransfer'
                    || $eventType = 'recordsManagement/restitution'
                    || $eventType = 'recordsManagement/restitutionRequest'
                    || $eventType = 'recordsManagement/restitutionRequestCanceling'
                    || $eventType = 'recordsManagement/metadataModification') {
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>4 = :org';
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>5 = :org';
                }
                if (empty($eventType) || $eventType = 'recordsManagement/accessRuleModification') {
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>8 = :org';
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>9 = :org';
                }
                if (empty($eventType) || $eventType = 'recordsManagement/integrityCheck') {
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>4 = :org';
                }
                if (empty($eventType) || $eventType = 'recordsManagement/retentionRuleModification') {
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>10 = :org';
                    $infoOrgQueryParts[] = '"eventInfo"::jsonb->>11 = :org';
                }
            }

            if (empty($objectClass) || $objectClass == 'medona/message') {
                $infoOrgQueryParts[] = '"eventInfo"::jsonb->>1 = :org';
                $infoOrgQueryParts[] = '"eventInfo"::jsonb->>3 = :org';
            }

            if (!empty($infoOrgQueryParts)) {
                $orgQueryParts['otherOrg'] = '<?SQL ('.implode(' OR ', $infoOrgQueryParts) .') ?>';
            }

            $query['org'] = '('.implode(' OR ', $orgQueryParts).')';
        }

        $queryString = implode(' AND ', $query);

        return [$queryParams, $queryString];
    }

    /**
     * Load a journal
     * @param string $journalReference The id of the journal or the journal object
     *
     * @throws \Exception
     *
     * @return boolean The result of the operation
     */
    public function openJournal($journalReference)
    {
        $logController = \laabs::newController('recordsManagement/log');
        $digitalResourceController = \laabs::newController('digitalResource/digitalResource');

        if (is_scalar($journalReference) || get_class($journalReference) == 'core\Type\Id') {
            $journalReference = $logController->read($journalReference);
        }

        if (isset($journalReference->toDate)) {
            $archiveController = \laabs::newController('recordsManagement/archive');
            $resources = $archiveController->getDigitalResources($journalReference->archiveId, $checkAccess = false);
            $journalResource = $digitalResourceController->retrieve($resources[0]->resId);

            // $journalFile = $journalResource->getContents();
            // $journalFile = $journalFile->getHandler();
            // $journalFilePath = $this->copyJournalIntoCsv($journalResource);
            $journalHandler = $journalResource->getHandler();

            $this->journalCursor = 0;

            if ($journalHandler == null) {
                throw \laabs::newException("lifeCycle/journalException", "The journal file can't be opened");
            } else {
                $this->journalHandler = $journalHandler;
                $this->checkIntegrity($journalReference);
                $this->currentJournalId = $journalReference->archiveId;
            }
        }

        $this->currentOffset = 0;

        return true;
    }

    public function copyJournalIntoCsv($journalFile)
    {
        $tmpdir = \laabs::getTmpDir();
        $fileName = $tmpdir.DIRECTORY_SEPARATOR. 'Journal' . \laabs::newId() .".csv";
        $file = fopen($fileName, "w");
        $value = $journalFile->getHandler();
        while (!feof($value)) {
            $chunk = fread($value, 2654208);
            fwrite($file, $chunk);
        }
        rewind($value);
        rewind($file);
        fclose($file);

        return $fileName;
    }

    /**
     * Get event object
     * @param id   $archiveId          The archive identifier
     * @param date $searchingStartDate The searching start date
     *
     * @throws \Exception
     *
     * @return object[] Array of live cycle event
     */
    public function getEvents($archiveId, $searchingStartDate = null)
    {
        // Open the journal to start with
        $logController = \laabs::newController('recordsManagement/log');
        $journal = $logController->getByDate('lifeCycle', $searchingStartDate);

        if (!isset($journal) || is_null($journal)) {
            $events = $this->sdoFactory->find('lifeCycle/event', "objectClass='recordsManagement/archive' AND  objectId='$archiveId'", [], ">timestamp");
            foreach ($events as $key => $event) {
                $events[$key] = $this->decodeEventFormat($event);
            }

            return $events;
        }

        if (is_array($journal)) {
            $journal = end($journal);
        }

        $this->openJournal($journal->archiveId);

        // Searching for related events not in the journal yet
        $events = $this->sdoFactory->find('lifeCycle/event', "objectClass='recordsManagement/archive' AND  objectId='$archiveId' AND timestamp>'$journal->toDate'", [], ">timestamp");
        foreach ($events as $key => $event) {
            $events[$key] = $this->decodeEventFormat($event);
        }

        // Searching for related events
        $events = [];
        while ($row = fgetcsv($this->journalHandler)) {
            if ($row[0] == (string) $archiveId) {
                $eventLine = '"' . implode('","', $row);
                $events[] = $this->getEventFromLine($eventLine);
                break;
            }
        }

        return $events;
    }


    /**
     * Get the last usable journal
     *
     * @return lifeCycle/journal The journal object
     */
    public function getLastJournal()
    {
        $logController = \laabs::newController('recordsManagement/log');
        $journals = $logController->query("type='lifeCycle'", ">fromDate", 1);

        if (empty($journals)) {
            return null;
        }

        $journal = end($journals);

        return $journal;
    }

    /**
     * Get the current journal
     * @param string  $journalId The journal identifier
     * @param integer $offset    The reading offset
     * @param integer $limit     The maximum number of event to load
     *
     * @throws \Exception
     *
     * @return object[] Array of life cycle event
     */
    public function readJournal($journalId, $offset = 0, $limit = null)
    {
        $this->openJournal($journalId);

        $events = array();

        if (!$limit) {
            $limit = \laabs::configuration('presentation.maarchRM')['maxResults'];
        }

        // Get the eventId position on the journal file
        while ($row = fgetcsv($this->journalHandler)) {
            if ($row[0] == (string) $journalId) {
                $eventLine = '"' . implode('","', $row);
                $event = $this->getEventFromLine($eventLine);
                $events[] = $event;
                break;
            }
        }

        return $events;
    }

    /**
     * Check integrity
     * @param string $archiveId
     *
     * @throws \Exception
     *
     * @return bool The result of the operation
     */
    public function checkIntegrity($archiveId)
    {
        $logController = \laabs::newController('recordsManagement/log');
        $archiveController = \laabs::newController('recordsManagement/archive');
        $digitalResourceController = \laabs::newController('digitalResource/digitalResource');

        // Read journal
        if (is_scalar($archiveId) || get_class($archiveId) == 'core\Type\Id') {
            $journal = $logController->read($archiveId);
        } else {
            $journal = $archiveId;
            $archiveId = (string) $journal->archiveId;
        }
        $resources = $archiveController->getDigitalResources($journal->archiveId, $checkAccess = false);
        $journalResource = $digitalResourceController->retrieve($resources[0]->resId);
        $resIntegrity = $archiveController->verifyIntegrity($journal->archiveId, false);

        if (is_array($resIntegrity["error"]) && !empty($resIntegrity["error"])) {
            throw \laabs::newException(
                'recordsManagement/journalException',
                "Invalid journal: invalid hash integrity."
            );
        }

        $nextJournal = $logController->getNextJournal($journal);

        // Journal is the last... simply check its hash against min rotate
        if ($nextJournal == null) {
            $now = \laabs::newTimestamp();

            $diff = $journal->toDate->diff($now);

            // In the future ????
            if ($diff->invert) {
                throw \laabs::newException(
                    'recordsManagement/journalException',
                    "Invalid journal date: latest date is in the future."
                );
            }

            return true;
        }

        $resources = $archiveController->getDigitalResources($nextJournal->archiveId, $checkAccess = false);
        $nextJournalResource = $digitalResourceController->retrieve($resources[0]->resId);
        $nextJournalContents = $nextJournalResource->getHandler();
        $chainEvent = fgetcsv($nextJournalContents);
        // For older version compatibility
        if (count($chainEvent) < 7) {
            if (empty($chainEvent[3]) || empty($chainEvent[4]) || empty($chainEvent[5])) {
                throw \laabs::newException(
                    'recordsManagement/journalException',
                    "Invalid journal: Next journal chaining event is incomplete."
                );
            }

            $chainedJournalId = $chainEvent[3];
            if ($chainedJournalId != $archiveId) {
                throw \laabs::newException(
                    'recordsManagement/journalException',
                    "Invalid journal: Next journal is missing or chaining event has an invalid journal identifier."
                );
            }

            $chainedJournalHashAlgo = $chainEvent[4];
            $chainedJournalHash = $chainEvent[5];

            $calcJournalHash = hash_stream($chainedJournalHashAlgo, $journalResource->getHandler());
            if ($calcJournalHash != $chainedJournalHash) {
                unlink($journalFilename);
                throw \laabs::newException(
                    'recordsManagement/journalException',
                    "Invalid journal: Chaining event has a different hash."
                );
            }
        } else {
            if (empty($chainEvent[8]) || empty($chainEvent[9]) || empty($chainEvent[10])) {
                throw \laabs::newException(
                    'recordsManagement/journalException',
                    "Invalid journal: Next journal chaining event is incomplete."
                );
            }

            $chainedJournalId = $chainEvent[8];
            if ($chainedJournalId != $archiveId) {
                throw \laabs::newException(
                    'recordsManagement/journalException',
                    "Invalid journal: Next journal is missing or chaining event has an invalid journal identifier."
                );
            }

            $chainedJournalHashAlgo = $chainEvent[9];
            $chainedJournalHash = $chainEvent[10];

            $calcJournalHash = hash_stream($chainedJournalHashAlgo, $journalResource->getHandler());
            if ($calcJournalHash != $chainedJournalHash) {
                throw \laabs::newException(
                    'recordsManagement/journalException',
                    "Invalid journal: Chaining event has a different hash."
                );
            }
        }
        return $chainEvent;
    }

    /**
     * Chain the last journal
     *
     * @return string The chained journal file name
     */
    public function chainJournal()
    {
        $journalArray = [];

        if (isset(\laabs::configuration('lifeCycle')['chainJournalByOrganization']) && \laabs::configuration('lifeCycle')['chainJournalByOrganization']) {
            $orgController = \laabs::newController('organization/organization');

            $organizations = $orgController->index(null, "isOrgUnit=false");

            foreach ($organizations as $organization) {
                $journalArray[] = $this->processChaining($organization->registrationNumber);
            }
        }

        $journalArray[] = $this->processChaining();

        if (count($journalArray) == 1) {
            $journalArray = $journalArray[0];
        }

        return $journalArray;
    }

    /**
     * process the chaining of the last journal
     * @param string $ownerOrgRegNumber The journal owner organization registration number
     *
     * @return string The chained journal file name
     */
    protected function processChaining($ownerOrgRegNumber = null)
    {
        $tmpdir = \laabs::getTmpDir();
        $timestampFileName = null;
        $logController = \laabs::newController('recordsManagement/log');
        $archiveController = \laabs::newController('recordsManagement/archive');
        $digitalResourceController = \laabs::newController('digitalResource/digitalResource');

        $newJournal = \laabs::newInstance('recordsManagement/log');
        $newJournal->archiveId = \laabs::newId();
        $newJournal->type = "lifeCycle";
        $newJournal->toDate = \laabs::newTimestamp();
        $newJournal->ownerOrgRegNumber = $ownerOrgRegNumber;

        $previousJournal = $logController->getLastJournal('lifeCycle', $ownerOrgRegNumber);

        if ($previousJournal) {
            $newJournal->fromDate = $previousJournal->toDate;
            $newJournal->previousJournalId = $previousJournal->archiveId;

            $queryString = "timestamp > '$newJournal->fromDate' AND timestamp <= '$newJournal->toDate'";

            if ($ownerOrgRegNumber) {
                $queryString .= " AND eventInfo = '*$ownerOrgRegNumber*'";
            }

            if ($this->separateInstance) {
                $queryString .= "AND instanceName = '".\laabs::getInstanceName()."'";
            }

            $events = $this->sdoFactory->find('lifeCycle/event', $queryString, [], "<timestamp");
        } else {
            // No previous journal, select all events
            $queryString = "timestamp <= '$newJournal->toDate'";

            if ($ownerOrgRegNumber) {
                $queryString .= " AND eventInfo = '*$ownerOrgRegNumber*'";
            }

            $events = $this->sdoFactory->find('lifeCycle/event', $queryString, [], "<timestamp");
            if (count($events) > 0) {
                $newJournal->fromDate = reset($events)->timestamp;
            } else {
                $newJournal->fromDate = \laabs::newTimestamp('1970-01-01');
            }
        }

        $journalFilename = $tmpdir.DIRECTORY_SEPARATOR.(string) $newJournal->archiveId.".csv";
        $journalFile = fopen($journalFilename, "w");
        fprintf($journalFile, chr(0xEF).chr(0xBB).chr(0xBF));

        // Journal format information
        $format = [];
        $format['journalChainingEvent'] = ['journalId', 'eventType', 'journalStartingTimestamp', 'journalClosureTimestamp', '', '', '', '', 'previousJournalId', 'hashAlgorithm', 'previousJournalHash'];
        $format['events'] = ['journalId', 'eventType', 'timestamp', 'orgRegNumber', 'orgUnitRegNumber', 'accountId', 'objectClass', 'objectId', 'operationResult', 'description', 'eventInfo'];
        $format['eventInfo'] = $this->eventFormats;

        // First event : chain with previous journal
        $eventLine = array();
        $eventLine[0] = (string) $newJournal->archiveId;
        $eventLine[1] = "lifeCycle/chainJournal";

        $eventLine[2] = (string) $newJournal->fromDate;
        $eventLine[3] = (string) $newJournal->toDate;
        $eventLine[4] = $eventLine[5] = $eventLine[6] = $eventLine[7] = "";

        // Write previous journal informations
        if ($previousJournal) {
            $eventLine[8] = (string) $previousJournal->archiveId;

            $resources = $archiveController->getDigitalResources($previousJournal->archiveId);
            $journalResource = $digitalResourceController->retrieve($resources[0]->resId);

            $eventLine[9] = (string) $journalResource->hashAlgorithm;
            $eventLine[10] = (string) $journalResource->hash;
        }

        fputcsv($journalFile, $eventLine);

        // Write events
        foreach ($events as $event) {
            $eventLine = array();

            $eventLine[] = (string) $event->eventId;
            $eventLine[] = (string) $event->eventType;
            $eventLine[] = (string) $event->timestamp;
            $eventLine[] = (string) $event->accountId;
            $eventLine[] = (string) $event->objectClass;
            $eventLine[] = (string) $event->objectId;
            $eventLine[] = $event->operationResult ? '1' : '0';
            $eventLine[] = (string) $event->description;

            $event->eventInfo = json_decode($event->eventInfo);
            if (is_array($event->eventInfo)) {
                $eventLine = array_merge($eventLine, $event->eventInfo);
            }

            fputcsv($journalFile, $eventLine);
        }

        fclose($journalFile);

        // create timestamp file
        if (isset(\laabs::configuration('lifeCycle')['chainWithTimestamp']) && \laabs::configuration('lifeCycle')['chainWithTimestamp']==true) {
            try {
                $timestampServiceUri = \laabs::configuration('lifeCycle')['timestampService'];
                $timestampService = \laabs::newService($timestampServiceUri);
                $timestampFileName = $timestampService->getTimestamp($journalFilename);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $logController->archiveJournal($journalFilename, $newJournal, $timestampFileName);
    }

    /**
     * Decode events format object from an event
     * @param lifeCycle/event $event The event to decode
     *
     * @throws \Exception
     *
     * @return object The life cycle event object
     */
    public function decodeEventFormat($event)
    {
        if (isset($event->eventInfo)) {
            if (!isset($this->eventFormats[$event->eventType])) {
                throw \laabs::newException("lifeCycle/journalException", "Unknown event type.");
            }

            $eventFormat = $this->eventFormats[$event->eventType]->format;
            $i = 0;

            $event->eventInfo = json_decode($event->eventInfo);
            foreach ($eventFormat as $item) {
                if (isset($event->eventInfo[$i])) {
                    $event->{$item} = $event->eventInfo[$i];
                } else {
                    $event->{$item} = null;
                }
                $i++;
            }
        }
        unset($event->eventInfo);

        return $event;
    }

    /**
     * Get an event from a csv line
     * @param string $eventLine The scv line from the journal
     *
     * @return object The life cycle event object
     */
    private function getEventFromLine($eventLine)
    {
        $eventArray = str_getcsv($eventLine);

        if (count($eventArray) < 7) {
            return null;
        }

        $event = \laabs::newInstance('lifeCycle/event');

        $event->eventId = \laabs::newId($eventArray[0]);
        $event->eventType = $eventArray[1];
        $event->timestamp = \laabs::newTimestamp($eventArray[2]);
        $event->accountId = $eventArray[3];
        $event->objectClass = $eventArray[4];
        $event->objectId = $eventArray[5];
        $event->operationResult = $eventArray[6] === '0' ? false : true;
        $event->description = $eventArray[7];

        try {
            $i = 8;
            if (!isset($this->eventFormats[$event->eventType])) {
                throw \laabs::newException("lifeCycle/journalException", "Unknown event type.");
            }
            $eventFormat = $this->eventFormats[$event->eventType]->format;

            foreach ($eventFormat as $item) {
                if (isset($eventArray[$i])) {
                    $event->{$item} = $eventArray[$i];
                } else {
                    $event->{$item} = null;
                }
                $i++;
            }
        } catch (\Exception $e) {
        }

        return $event;
    }

    /**
     * Notify
     * @param lifeCycle/event $event The event
     */
    private function notify($event)
    {
        $subject = 'Life cycle error';
        $body = "Error on event '$event->eventId' of type '$event->eventType'. ";
        $body .= "The object '$event->objectId' of class '$event->objectClass'. ";
        $body .= "Description : $event->description ";

        $notificationController = \laabs::newController('batchProcessing/notification');
        $notificationController->create($subject, $body, array());
    }
}
