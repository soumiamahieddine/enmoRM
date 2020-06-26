<?php

/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\Statistics\Controller;

/**
 * Managemet of the access rule of an archive
 *
 * @author Jérôme Boucher <jerome.boucher@maarch.org>
 */
class Statistics
{
    public $sdoFactory;
    protected $minDate;
    protected $maxDate;
    protected $sizeFilter;
    protected $sizeFilters;
    protected $translator;

    /**
     * Constructor of access control class
     *
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->sdoFactory = $sdoFactory;
        $this->pdo = $sdoFactory->das->pdo;
        $this->sizeFilters = ["Octets", "Ko", "Mo", "Go"];
        $this->translator = $translator;
        $this->translator->setCatalog("Statistics/Statistics");
    }

    /**
     * Retrieve default stats for screen
     *
     * @return array $statistics
     */
    public function index()
    {
        return $this->retrieve(null, null, null, null, 3);
    }

    /**
     * Get Counts
     *
     * @param  string $operation  Type of operation to count or sum
     * @param  string $startDate  Start date
     * @param  string $endDate    End date
     * @param  string $filter     Filtering parameter to group query by
     * @param  float  $sizeFilter power of 10 to divide by
     *
     * @return array              Associative array of properties
     */
    public function retrieve($operation = null, $startDate = null, $endDate = null, $filter = null, $sizeFilter = 0)
    {
        if (!empty($startDate)) {
            $startDate = \laabs::newDateTime($startDate);
        } else {
            $startDate = null;
        }

        if (!empty($startDate)) {
            $endDate = \laabs::newDateTime($endDate);
        } else {
            $endDate = null;
        }

        if (!is_null($startDate) && is_null($endDate)) {
            throw new \core\Exception\BadRequestException($this->translator->getText("End Date is mandatory if start date is filled"));
        } elseif (is_null($startDate) && !is_null($endDate)) {
            throw new \core\Exception\BadRequestException($this->translator->getText("Start Date is mandatory if end date is filled"));
        }

        if ($startDate == $endDate && !is_null($startDate)) {
            $endDate->setTime(23, 59, 59);
        } elseif ($startDate > $endDate) {
            throw new \core\Exception\BadRequestException($this->translator->getText("Start Date cannot be past end date"));
        }

        $this->sizeFilter = $sizeFilter;

        $statistics = ["unit" => $this->sizeFilters[$sizeFilter]];
        if (is_null($operation) || empty($operation)) {
            $statistics = $this->defaultStats($startDate, $endDate, $filter, $statistics);
        } elseif (!empty($operation) && !in_array($operation, ['deposit', 'deleted', 'conserved', 'restituted', 'transfered'])) {
            throw new \core\Exception\BadRequestException($this->translator->getText("Operation type not supported"));
        } elseif (!empty($operation) && is_null($filter)) {
            throw new \core\Exception\BadRequestException($this->translator->getText("Filter cannot be null if operation is not"));
        }

        if (!empty($operation)) {
            switch ($operation) {
                case 'deposit':
                    $statistics = $this->depositStats($startDate, $endDate, $filter, $statistics);
                    break;
                case 'deleted':
                    $statistics = $this->deletedStats($startDate, $endDate, $filter, $statistics);
                    break;
                case 'conserved':
                    $statistics = $this->conservedStats($endDate, $filter, $statistics);
                    break;
                case 'restituted':
                    $statistics = $this->restitutedStats($startDate, $endDate, $filter, $statistics);
                    break;
                case 'transfered':
                    $statistics = $this->transferedStats($startDate, $endDate, $filter, $statistics);
                    break;
            }
        }

        return $statistics;
    }

    /**
     * Basics stats to return for homepage
     *
     * @param  datetime $startDate  Starting Date
     * @param  datetime $endDate    End date
     * @param  string   $filter     Filtering parameter to group query by
     * @param  array    $statistics Array of statistics
     *
     * @return array                Associative array of statistics
     */
    protected function defaultStats($startDate, $endDate, $filter, $statistics = [])
    {
        $statistics['depositMemorySize'] = $this->getSizeByEventType(['recordsManagement/deposit', 'recordsManagement/depositNewResource'], $jsonColumnNumber = 8, $startDate, $endDate);
        $statistics['depositMemoryCount'] = $this->getCountByEventType($filter, ['recordsManagement/deposit', 'recordsManagement/depositNewResource'], $startDate, $endDate);
        $statistics['deletedMemorySize'] = $this->getSizeByEventType(['recordsManagement/destruction', 'recordsManagement/elimination'], $jsonColumnNumber = 6, $startDate, $endDate);
        $statistics['deletedMemoryCount'] = $this->getCountByEventType($filter, ['recordsManagement/destruction', 'recordsManagement/elimination'], $startDate, $endDate);

        if (\laabs::configuration('medona')['transaction']) {
            $statistics['transferedMemorySize'] = $this->getSizeByEventType(['recordsManagement/outgoingTransfer'], $jsonColumnNumber = 6, $startDate, $endDate);
            $statistics['transferedMemoryCount'] = $this->getCountByEventType($filter, ['recordsManagement/outgoingTransfer'], $startDate, $endDate);
            $statistics['restitutionMemorySize'] = $this->getSizeByEventType(['recordsManagement/restitution'], $jsonColumnNumber = 6, $startDate, $endDate);
            $statistics['restitutionMemoryCount'] = $this->getCountByEventType($filter, ['recordsManagement/restitution'], $startDate, $endDate);
        }

        $statistics['currentMemorySize'] = $this->getArchiveSize($endDate);
        $statistics['currentMemoryCount'] = $this->getArchiveCount($endDate);

        $statistics['evolutionSize'] = $statistics['currentMemorySize'] - ($startDate ? $this->getArchiveSize($startDate) : 0);
        $statistics['evolutionCount'] = $statistics['currentMemoryCount'] - ($startDate ? $this->getArchiveCount($startDate) : 0);

        if ($statistics['currentMemorySize'] != (integer)$statistics['currentMemorySize']) {
            $statistics['currentMemorySize'] = number_format($statistics['currentMemorySize'], 3, ",", " ");
        }
        if ($statistics['evolutionSize'] != (integer)$statistics['evolutionSize']) {
            $statistics['evolutionSize'] = number_format($statistics['evolutionSize'], 3, ",", " ");
        }

        return $statistics;
    }

    /**
     * Statistics aggregator for deposit event
     *
     * @param  datetime $startDate starting date
     * @param  datetime $endDate   End date
     * @param  string   $filter    Group by argument
     *
     * @return array               Associative of statistics
     */
    protected function depositStats($startDate, $endDate, $filter, $statistics = [])
    {
        switch ($filter) {
            case 'archivalProfile':
                $jsonSizeColumnNumber = 8;
                $jsonOrderingColumnNumber = 10;
                break;
            case 'originatingOrg':
                $jsonSizeColumnNumber = 8;
                $jsonOrderingColumnNumber = 4;
                break;
        }

        $statistics['groupedDepositMemorySize'] = $this->getSizeByEventTypeOrdered($filter, ['recordsManagement/deposit', 'recordsManagement/depositNewResource'], $jsonSizeColumnNumber, $startDate, $endDate, $filter, $jsonOrderingColumnNumber);
        $statistics['groupedDepositMemoryCount'] = $this->getCountByEventTypeOrdered($filter, ['recordsManagement/deposit', 'recordsManagement/depositNewResource'], $startDate, $endDate, $filter, $jsonOrderingColumnNumber);
        return $statistics;
    }

    /**
     * Statistics aggregator for deleted event
     *
     * @param  datetime $startDate starting date
     * @param  datetime $endDate   End date
     * @param  string   $filter    Group by argument
     *
     * @return array               Associative of statistics
     */
    protected function deletedStats($startDate, $endDate, $filter, $statistics = [])
    {
        switch ($filter) {
            case 'archivalProfile':
                $jsonSizeColumnNumber = 6;
                $jsonOrderingColumnNumber = 8;
                break;
            case 'originatingOrg':
                $jsonSizeColumnNumber = 6;
                $jsonOrderingColumnNumber = 4;
                break;
        }

        $statistics['deletedGroupedMemorySize'] = $this->getSizeByEventTypeOrdered($filter, ['recordsManagement/destruction', 'recordsManagement/elimination'], $jsonSizeColumnNumber, $startDate, $endDate, $filter, $jsonOrderingColumnNumber);
        $statistics['deletedGroupedMemoryCount'] = $this->getCountByEventTypeOrdered($filter, ['recordsManagement/destruction', 'recordsManagement/elimination'], $startDate, $endDate, $filter, $jsonOrderingColumnNumber);

        return $statistics;
    }

    /**
     * Statistics aggregator for conserved archive
     *
     * @param  datetime $endDate   End date
     * @param  string   $filter    Group by argument
     *
     * @return array               Associative of statistics
     */
    protected function conservedStats($endDate, $filter, $statistics = [])
    {
        $statistics['groupedArchiveSize'] = $this->getArchiveSizeOrdered($filter, $endDate);
        $statistics['groupedArchiveCount'] = $this->getArchiveCountOrdered($filter, $endDate);

        return $statistics;
    }

    /**
     * Statistics aggregator for restituted event
     *
     * @param  datetime $startDate starting date
     * @param  datetime $endDate   End date
     * @param  string   $filter    Group by argument
     *
     * @return array               Associative of statistics
     */
    protected function restitutedStats($startDate, $endDate, $filter, $statistics = [])
    {
        switch ($filter) {
            case 'archivalProfile':
                $jsonSizeColumnNumber = 6;
                $jsonOrderingColumnNumber = 8;
                break;
            case 'originatingOrg':
                $jsonSizeColumnNumber = 6;
                $jsonOrderingColumnNumber = 4;
                break;
        }

        $statistics['restitutedGroupedMemorySize'] = $this->getSizeByEventTypeOrdered($filter, ['recordsManagement/restitution'], $jsonSizeColumnNumber, $startDate, $endDate, $filter, $jsonOrderingColumnNumber);
        $statistics['restitutedGroupedMemoryCount'] = $this->getCountByEventTypeOrdered($filter, ['recordsManagement/restitution'], $startDate, $endDate, $filter, $jsonOrderingColumnNumber);

        return $statistics;
    }

    /**
     * Statistics aggregator for transfered event
     *
     * @param  datetime $startDate starting date
     * @param  datetime $endDate   End date
     * @param  string   $filter    Group by argument
     *
     * @return array               Associative of statistics
     */
    protected function transferedStats($startDate, $endDate, $filter, $statistics = [])
    {
        switch ($filter) {
            case 'archivalProfile':
                $jsonSizeColumnNumber = 6;
                $jsonOrderingColumnNumber = 8;
                break;
            case 'originatingOrg':
                $jsonSizeColumnNumber = 6;
                $jsonOrderingColumnNumber = 4;
                break;
        }

        $statistics['transferedGroupedMemorySize'] = $this->getSizeByEventTypeOrdered($filter, ['recordsManagement/outgoingTransfer'], $jsonSizeColumnNumber, $startDate, $endDate, $filter, $jsonOrderingColumnNumber);
        $statistics['transferedGroupedMemoryCount'] = $this->getCountByEventTypeOrdered($filter, ['recordsManagement/outgoingTransfer'], $startDate, $endDate, $filter, $jsonOrderingColumnNumber);

        return $statistics;
    }

    /**
     * Sum all event info for a particular event
     *
     * @param  array    $eventTypes       Array of event types
     * @param  integer  $jsonColumnNumber json Column number for size parameter in lifeCycle event table
     * @param  datetime $startDate        Starting Date
     * @param  datetime $endDate          End date
     *
     * @return integer                    Sum of size for events
     */
    protected function getSizeByEventType($eventTypes, $jsonColumnNumber, $startDate = null, $endDate = null)
    {
        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];

        $query = <<<EOT
SELECT SUM (CAST(NULLIF("eventInfo"::json->>$jsonColumnNumber, '') AS INTEGER)) FROM "lifeCycle"."event" WHERE "eventType" IN ($in)
EOT;

        $sum = $this->executeQuery($query, $inParams, $eventTypes[0] == 'recordsManagement/deposit', $startDate, $endDate)[0]['sum'];

        return $sum;
    }

    /**
     * Get the query to get archives recursively
     *
     * @param  string   $in         Serialized array of variables in the query
     * @param  string   $startDate  Starting Date
     * @param  string   $endDate    End date
     *
     * @return string               The query
     */
    protected function getQueryArchiveRecursive($in = "", $startDate = null, $endDate = null)
    {
        $query = 'WITH RECURSIVE include_parent_archives(archive_id, parent_id) as (
            SELECT "archive"."archiveId", "archive"."parentArchiveId"
            FROM "recordsManagement"."archive" "archive"
            INNER JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive"."archiveId"
            WHERE "event"."eventType" IN ('.$in.') '.(!empty($startDate) ? "AND timestamp BETWEEN '$startDate'::timestamp AND '$endDate'::timestamp" : "").
            ' UNION ALL
            SELECT "archive"."archiveId", "archive"."parentArchiveId"
            FROM "recordsManagement"."archive" "archive", include_parent_archives "archive_recursive"
            WHERE "archive"."archiveId" = "archive_recursive"."parent_id"
            )';
        return $query;
    }

    /**
     * Count all event info for particular event(s)
     *
     * @param  array    $eventTypes            Array of event types
     * @param  integer  $jsonColumnNumber      json Column number for size parameter in lifeCycle event table
     * @param  datetime $startDate             Starting Date
     * @param  datetime $endDate               End date
     *
     * @return integer                        Count of size for events
     */
    protected function getCountByEventType($filter, $eventTypes, $startDate = null, $endDate = null)
    {
        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];
        $isArchivalProfile = $filter == "archivalProfile";

        if (!empty($startDate)) {
            $startDate = (string) $startDate->format('Y-m-d 00:00:00');
            $endDate = (string) $endDate->format('Y-m-d 23:59:59');
        }

        $query = $this->getQueryArchiveRecursive($in, $startDate, $endDate);
        $query .= 'SELECT COUNT(DISTINCT "archive_recursive"."archive_id")
            FROM include_parent_archives "archive_recursive"
            INNER JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive_recursive"."archive_id" AND "event"."eventType" IN ('.$in.')'.
            ' WHERE "archive_recursive"."parent_id" IS NULL';

        $count = $this->executeQuery($query, $inParams)[0]['count'];
        return $count;
    }

    /**
     * Count all event info for particular event(s) ordered by another event
     *
     * @param  array    $eventTypes            Array of event types
     * @param  integer  $jsonColumnNumber      json Column number for size parameter in lifeCycle event table
     * @param  datetime $startDate             Starting Date
     * @param  datetime $endDate               End date
     * @param  string   $groupBy               Name of Group By
     * @param  integer  $jsonColumnNumberOrder Json column number to group event by
     *
     * @return integer                        Count of size for events
     */
    protected function getCountByEventTypeOrdered($filter, $eventTypes, $startDate = null, $endDate = null, $groupBy = null, $jsonColumnNumberOrder = 0)
    {
        $sum = 0;

        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];
        $isArchivalProfile = $filter == "archivalProfile";

        if (!empty($startDate)) {
            $startDate = (string) $startDate->format('Y-m-d 00:00:00');
            $endDate = (string) $endDate->format('Y-m-d 23:59:59');
        }

        $query = $this->getQueryArchiveRecursive($in, $startDate, $endDate);
        $query .= 'SELECT '.($isArchivalProfile ? 'COALESCE("archivalProfile"."name", \'Without profile\')' : '"org"."displayName"').' AS '.$groupBy.', COUNT(DISTINCT "archive_recursive"."archive_id")
            FROM include_parent_archives "archive_recursive"
            INNER JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive_recursive"."archive_id" AND "event"."eventType" IN ('.$in.')'.
            (!$isArchivalProfile
                ? ' INNER JOIN "organization"."organization" "org" ON "org"."registrationNumber" = "event"."eventInfo"::jsonb->>'.$jsonColumnNumberOrder
                : ' LEFT JOIN "recordsManagement"."archivalProfile" "archivalProfile" ON "archivalProfile"."reference" = "event"."eventInfo"::jsonb->>'.$jsonColumnNumberOrder).
            ' WHERE "archive_recursive"."parent_id" IS NULL
            GROUP BY '.($isArchivalProfile ? 'COALESCE("archivalProfile"."name", \'Without profile\')' : '"org"."displayName"');

        $sum = $this->executeQuery($query, $inParams);
        return $sum;
    }

    /**
     * Sum all events info for particular event(s) ordered by another event
     *
     * @param  array    $eventTypes            Array of event types
     * @param  integer  $jsonColumnNumber      json Column number for size parameter in lifeCycle event table
     * @param  datetime $startDate             Starting Date
     * @param  datetime $endDate               End date
     * @param  string   $groupBy               Name of Group By
     * @param  integer  $jsonColumnNumberOrder Json column number to group event by
     *
     * @return integer                        Sum of size for events
     */
    protected function getSizeByEventTypeOrdered($filter, $eventTypes, $jsonColumnNumber, $startDate = null, $endDate = null, $groupBy = null, $jsonColumnNumberOrder = 0)
    {
        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];
        $isArchivalProfile = $filter == "archivalProfile";

        if (!empty($startDate)) {
            $startDate = (string) $startDate->format('Y-m-d 00:00:00');
            $endDate = (string) $endDate->format('Y-m-d 23:59:59');
        }

        $query = $this->getQueryArchiveRecursive($in, $startDate, $endDate);
        $query .= ', get_children_size(archive_id, volume'.($isArchivalProfile ? ', archival_profile' : '').') AS (
            SELECT DISTINCT "archive_recursive"."archive_id", "event"."eventInfo"::json->>'.$jsonColumnNumber.($isArchivalProfile ? ', "event"."eventInfo"::json->>'.$jsonColumnNumberOrder : '').
            ' FROM include_parent_archives "archive_recursive"
            LEFT JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive_recursive"."archive_id" AND "event"."eventType" IN ('.$in.')
            WHERE "archive_recursive"."parent_id" IS NULL
          UNION ALL
              SELECT "archive"."archiveId", "event"."eventInfo"::json->>'.$jsonColumnNumber.($isArchivalProfile ? ', "archive_size"."archival_profile"' : '').
              ' FROM "recordsManagement"."archive" "archive"
              JOIN get_children_size "archive_size" ON 1=1
              LEFT JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive"."archiveId" AND "event"."eventType" IN ('.$in.')
              WHERE "archive"."parentArchiveId" = "archive_size"."archive_id"
        )
        SELECT '.($isArchivalProfile ? 'COALESCE("archivalProfile"."name", \'Without profile\')' : '"org"."displayName"') . ' as '.$groupBy.', SUM(CAST("archive_size"."volume" AS INTEGER))
        FROM get_children_size "archive_size"'.
        (!$isArchivalProfile
            ? ' INNER JOIN "lifeCycle"."event" "event" ON "event"."objectId" = "archive_size"."archive_id" AND "event"."eventType" IN ('.$in.')
            INNER JOIN "organization"."organization" "org" ON "org"."registrationNumber" = "event"."eventInfo"::jsonb->>'.$jsonColumnNumberOrder
            : ' LEFT JOIN "recordsManagement"."archivalProfile" "archivalProfile" ON "archivalProfile"."reference" = "archive_size"."archival_profile"').
        ' WHERE "archive_size"."volume" != \'\'
        GROUP BY '.($isArchivalProfile ? 'COALESCE("archivalProfile"."name", \'Without profile\')' : '"org"."displayName"');

        $sum = $this->executeQuery($query, $inParams);

        return $sum;
    }

    /**
     * Retrieve count of archives unto a specific date
     *
     * @param  datetime $endDate End date
     *
     * @return integer           Size of archive
     */
    protected function getArchiveCount($endDate = null)
    {
        $sum = 0;
        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        } else {
            $endDate = (string) $endDate->format('Y-m-d H:i:s');
        }

        $query = <<<EOT
SELECT COUNT(*) FROM "recordsManagement"."archive" WHERE "depositDate"<'$endDate'::timestamp AND "status" = 'preserved' AND "parentArchiveId" IS NULL;
EOT;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch()['count'];

        return $result;
    }

    /**
     * Retrieve size of digital resources unto a specific date
     *
     * @param  datetime $endDate End date
     *
     * @return integer           Size of archive
     */
    protected function getArchiveSize($endDate = null)
    {
        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        } else {
            $endDate = (string) $endDate->format('Y-m-d H:i:s');
        }

        $query = <<<EOT
SELECT SUM("digitalResource"."size")
FROM "digitalResource"."digitalResource"
INNER JOIN "recordsManagement"."archive" ON "archive"."archiveId" = "digitalResource"."archiveId"
WHERE "archive"."depositDate"<'$endDate'::timestamp AND "archive"."status" = 'preserved';
EOT;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch()['sum'];
        $sum = (integer)$result / pow(1000, $this->sizeFilter);

        return $sum;
    }

    /**
     * Retrieve size of digital resources unto a specific date group by a parameter
     *
     * @param  string   $groupBy Ordering parameter
     * @param  datetime $endDate End date
     *
     * @return array             Size of archive ordered by parameter
     */
    protected function getArchiveSizeOrdered($groupBy, $endDate = null)
    {
        $isArchivalProfile = false;
        switch ($groupBy) {
            case 'archivalProfile':
                $tableProperty = "archivalProfileReference";
                $isArchivalProfile = true;
                break;
            case 'originatingOrg':
                $tableProperty = "originatorOrgRegNumber";
                break;
        }

        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        } else {
            $endDate = (string) $endDate->format('Y-m-d H:i:s');
        }

        $query = 'WITH RECURSIVE get_children_size(archive_id, volume, group_by) AS (
            SELECT "archive"."archiveId", "digitalResource"."size", "archive"."'.$tableProperty.'"
            FROM "recordsManagement"."archive" "archive"
            LEFT JOIN "digitalResource"."digitalResource" "digitalResource" ON "digitalResource"."archiveId" = "archive"."archiveId"
            WHERE "archive"."parentArchiveId" IS NULL AND "archive"."depositDate" < \''.$endDate.'\'::timestamp AND "status" = \'preserved\'
          UNION ALL
            SELECT "archive"."archiveId", "digitalResource"."size", "archive_size"."group_by"
            FROM "recordsManagement"."archive" "archive"
            JOIN get_children_size "archive_size" ON 1=1
            LEFT JOIN "digitalResource"."digitalResource" "digitalResource" ON "digitalResource"."archiveId" = "archive"."archiveId"
            WHERE "archive"."parentArchiveId" = "archive_size"."archive_id"
        )
        SELECT '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"').' AS '.$groupBy.', SUM(CAST("archive_size"."volume" AS INTEGER))
        FROM get_children_size "archive_size"'.(
            $isArchivalProfile
            ? ' INNER JOIN "recordsManagement"."archivalProfile" "archivalProfile" ON "archivalProfile"."reference" = "archive_size"."group_by"'
            : ' INNER JOIN "organization"."organization" "organization" ON "organization"."registrationNumber" = "archive_size"."group_by"'
        ).
        ' GROUP BY '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"');
        
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $results = [];
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $result['sum'] /= pow(1000, $this->sizeFilter);
            if ($result['sum'] != (integer)$result['sum']) {
                $result['sum'] = number_format($result['sum'], 3, ",", " ");
            }
            $results[] = $result;
        }

        return $results;
    }

    /**
     * Retrieve count of digital resources unto a specific date group by a parameter
     *
     * @param  string   $groupBy Ordering parameter
     * @param  datetime $endDate End date
     *
     * @return array             Count of archive ordered by parameter
     */
    protected function getArchiveCountOrdered($groupBy, $endDate = null)
    {
        $isArchivalProfile = false;
        switch ($groupBy) {
            case 'archivalProfile':
                $isArchivalProfile = true;
                $tableProperty = "archivalProfileReference";
                break;
            case 'originatingOrg':
                $tableProperty = "originatorOrgRegNumber";
                break;
        }

        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        } else {
            $endDate = (string) $endDate->format('Y-m-d H:i:s');
        }

        $query = 'SELECT '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"').' AS '.$groupBy.', COUNT ("archive".*)
                FROM "recordsManagement"."archive" "archive"'.
                (
                    $isArchivalProfile
                    ? ' INNER JOIN "recordsManagement"."archivalProfile" "archivalProfile" ON "archivalProfile"."reference" = "archive"."'.$tableProperty.'"'
                    : ' INNER JOIN "organization"."organization" "organization" ON "organization"."registrationNumber" = "archive"."'.$tableProperty.'"'
                ).
                ' WHERE "depositDate" < \''.$endDate.'\'::timestamp AND "status" = \'preserved\' AND "archive"."parentArchiveId" IS NULL
                GROUP BY '.($isArchivalProfile ? '"archivalProfile"."name"' : '"organization"."displayName"');

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $results = [];
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = $result;
        }

        return $results;
    }

    /**
     * Stringify array of type of events for better sql query
     *
     * @param string $eventTypes Types of event
     *
     * @return Array
     */
    protected function stringifyEventTypes($eventTypes)
    {
        $in = "";
        foreach ($eventTypes as $key => $eventType) {
            $k = ":eventType" . $key;
            $in .= "$k,";
            $inParams[$k] = $eventType;
        }
        $in = rtrim($in, ",");

        return [
            'in' => $in,
            'inParams' => $inParams
        ];
    }

    /**
     * Execute query
     *
     * @param string   $query                           Query to send
     * @param string   $secondary_parameters            Secondary parameters
     * @param DateTime $startDate                       Start Date
     * @param DateTime $endDate                         End date
     *
     * @return array                                    Results of query
     */
    public function executeQuery($query, $secondary_parameters, $addToEvolution = false, $startDate = null, $endDate = null)
    {
        $params = [];

        if (!is_null($startDate)) {
            $params[':startDate'] = (string) $startDate->format('Y-m-d 00:00:00');
            $params[':endDate'] = (string) $endDate->format('Y-m-d 23:59:59');
            $query .= " AND timestamp BETWEEN :startDate::timestamp AND :endDate::timestamp";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array_merge($params, $secondary_parameters));
        $results = [];

        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            if (isset($result['sum'])) {
                $result['sum'] /= pow(1000, $this->sizeFilter);
                if ($result['sum'] != (integer)$result['sum']) {
                    $result['sum'] = number_format($result['sum'], 3, ",", " ");
                }
            } else {
                $result['sum'] = '0,000';
            }
            $results[] = $result;
        }

        return $results;
    }
}
