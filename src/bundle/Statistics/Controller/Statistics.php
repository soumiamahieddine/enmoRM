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
    protected $correspondingTables;

    /**
     * Constructor of access control class
     *
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $correspondingTables)
    {
        $this->sdoFactory = $sdoFactory;
        $this->pdo = $sdoFactory->das->pdo;
        $this->correspondingTables = $correspondingTables;
        $this->correspondingTables = [
            'deposit' => 'medona/message',
            'delete' => 'medona/message',
            'conserved' => 'recordsManagement/archive'
        ];
    }

    /**
     * Get Counts
     *
     * @param string  $operation           Type of operation to count
     * @param string  $startDate           Start date
     * @param string  $endDate             End date
     * @param boolean $filter     Archival Profile
     *
     * @return array Array of counts
     */
    public function retrieve($operation = null, $startDate = null, $endDate = null, $filter = null)
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
            throw new \core\Exception\BadRequestException("End Date is mandatory if start date is filled");
        } elseif (is_null($startDate) && !is_null($endDate)) {
            throw new \core\Exception\BadRequestException("Start Date is mandatory if end date is filled");
        }

        if ($startDate == $endDate && !is_null($startDate)) {
            $endDate->setTime(23, 59, 59);
        } elseif ($startDate > $endDate) {
            throw new \core\Exception\BadRequestException("Start Date cannot be past end date");
        }

        $statistics = [];
        if (is_null($operation)) {
            $statistics = $this->defaultStats($startDate, $endDate);
        } elseif (!is_null($operation) && !in_array($operation, ['deposit', 'delete', 'conserved'])) {
            throw new \core\Exception\BadRequestException("Operation type not supported");
        } elseif (!is_null($operation) && is_null($filter)) {
            throw new \core\Exception\BadRequestException("Filter cannot be null if operation is not");
        }

        if (!is_null($operation)) {
            switch ($operation) {
                case 'deposit':
                    $statistics = $this->depositStats($startDate, $endDate, $filter);
                    break;
                case 'delete':
                    $statistics = $this->deletedStats($startDate, $endDate, $filter);
                    break;
                case 'conserved':
                    $statistics = $this->conservedStats($endDate, $filter);
                    break;
            }
        }

        if (\laabs::configuration('medona')['transaction']) {
            $statistics['transferredMemorySize'] = $this->getSizeByEventType(['recordsManagement/outgoingTansfer'], $jsonColumnNumber = 6, $startDate, $endDate);
            $statistics['restitutionMemorySize'] = $this->getSizeByEventType(['recordsManagement/restitution'], $jsonColumnNumber = 6, $startDate, $endDate);
        }

        return $statistics;
    }

    protected function defaultStats($startDate, $endDate)
    {
        $defaultStats = [];
        $defaultStats['depositMemorySize'] = $this->getSizeByEventType(['recordsManagement/deposit', 'recordsManagement/depositNewResource'], $jsonColumnNumber = 8, $startDate, $endDate);
        $defaultStats['deletedMemorySize'] = $this->getSizeByEventType(['recordsManagement/destruction'], $jsonColumnNumber = 6, $startDate, $endDate);
        $defaultStats['currentMemorySize'] = $this->getArchiveSize($endDate);

        if (\laabs::configuration('medona')['transaction']) {
            $defaultStats['transferredMemorySize'] = $this->getSizeByEventType(['recordsManagement/outgoingTansfer'], $jsonColumnNumber = 6, $startDate, $endDate);
            $defaultStats['restitutionMemorySize'] = $this->getSizeByEventType(['recordsManagement/restitution'], $jsonColumnNumber = 6, $startDate, $endDate);
        }

        return $defaultStats;
    }

    protected function depositStats($startDate, $endDate, $filter)
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

        $statistics = [];
        $statistics['groupedDepositMemorySize'] = $this->getSizeByEventTypeOrdered(['recordsManagement/deposit', 'recordsManagement/depositNewResource'], $jsonSizeColumnNumber, $startDate, $endDate, $filter, $jsonOrderingColumnNumber);
        $statistics['groupedDepositMemoryCount'] = $this->getCountByEventTypeOrdered(['recordsManagement/deposit', 'recordsManagement/depositNewResource'], $jsonSizeColumnNumber, $startDate, $endDate, $filter, $jsonOrderingColumnNumber);
    }

    protected function deletedStats($startDate, $endDate, $filter)
    {
        switch ($filter) {
            case 'archivalProfile':
                $jsonSizeColumnNumber = 8;
                $jsonOrderingColumnNumber = 6;
                break;
            case 'originatingOrg':
                $jsonSizeColumnNumber = 4;
                $jsonOrderingColumnNumber = 6;
                break;
        }

        $statistics = [];
        $statistics['deletedGroupedMemorySize'] = $this->getSizeByEventTypeOrdered(['recordsManagement/destruction'], $jsonSizeColumnNumber, $startDate, $endDate, $filter, $jsonOrderingColumnNumber);
        $statistics['deletedGroupedMemoryCount'] = $this->getCountByEventTypeOrdered(['recordsManagement/destruction'], $jsonSizeColumnNumber, $startDate, $endDate, $filter, $jsonOrderingColumnNumber);

        return $statistics;
    }

    protected function conservedStats($endDate, $filter)
    {
        $statistics = [];
        $statistics['groupedArchiveSize'] = $this->getArchiveSizeOrdered($filter, $endDate);
        $statistics['groupedArchiveCount'] = $this->getArchiveCountOrdered($filter, $endDate);

        return $statistics;
    }

    protected function getSizeByEventType($eventTypes, $jsonColumnNumber, $startDate = null, $endDate = null)
    {
        $sum = 0;

        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];

        $query = <<<EOT
SELECT SUM (CAST(NULLIF("eventInfo"::json->>$jsonColumnNumber, '') AS INTEGER)) FROM "lifeCycle"."event" WHERE "eventType" IN ($in)
EOT;

        $sum = $this->executeQuery($query, $eventTypes, $inParams, $startDate, $endDate)[0]['sum'];

        return $sum;
    }

    protected function getCountByEventTypeOrdered($eventTypes, $jsonColumnNumber, $startDate = null, $endDate = null, $groupBy = null, $jsonColumnNumberOrder = 0)
    {
        $sum = 0;

        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];

        if (is_null($startDate)) {
            $query = <<<EOT
SELECT ("eventInfo"::jsonb->>$jsonColumnNumberOrder) AS $groupBy, COUNT ("eventInfo"::json->>$jsonColumnNumber)
FROM "lifeCycle"."event"
WHERE "eventType" IN ($in)
GROUP BY "eventInfo"::jsonb->>$jsonColumnNumberOrder
EOT;
        } else {
            $startDate = (string) $startDate->format('Y-m-d H:i:s');
            $endDate = (string) $endDate->format('Y-m-d H:i:s');

            $query = <<<EOT
SELECT ("eventInfo"::jsonb->>$jsonColumnNumberOrder) AS $groupBy, COUNT ("eventInfo"::json->>$jsonColumnNumber)
FROM "lifeCycle"."event"
WHERE "eventType" IN ($in)
AND timestamp BETWEEN '$startDate'::timestamp AND '$endDate'::timestamp
GROUP BY "eventInfo"::jsonb->>$jsonColumnNumberOrder
EOT;
        }
        $sum = $this->executeQuery($query, $eventTypes, $inParams);

        return $sum;
    }

    protected function getSizeByEventTypeOrdered($eventTypes, $jsonColumnNumber, $startDate = null, $endDate = null, $groupBy = null, $jsonColumnNumberOrder = 0)
    {
        $sum = 0;

        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];

        if (is_null($startDate)) {
            $query = <<<EOT
SELECT COALESCE("eventInfo"::jsonb->>$jsonColumnNumberOrder) AS $groupBy, SUM (CAST(NULLIF("eventInfo"::json->>$jsonColumnNumber, '') AS INTEGER))
FROM "lifeCycle"."event"
WHERE "eventType" IN ($in)
GROUP BY "eventInfo"::jsonb->>$jsonColumnNumberOrder
EOT;
        } else {
            $startDate = (string) $startDate->format('Y-m-d H:i:s');
            $endDate = (string) $endDate->format('Y-m-d H:i:s');

            $query = <<<EOT
SELECT COALESCE("eventInfo"::jsonb->>$jsonColumnNumberOrder) AS $groupBy, SUM (CAST(NULLIF("eventInfo"::json->>$jsonColumnNumber, '') AS INTEGER))
FROM "lifeCycle"."event"
WHERE "eventType" IN ($in)
AND timestamp BETWEEN '$startDate'::timestamp AND '$endDate'::timestamp
GROUP BY "eventInfo"::jsonb->>$jsonColumnNumberOrder
EOT;
        }
        $sum = $this->executeQuery($query, $eventTypes, $inParams);

        return $sum;
    }

    protected function getArchiveSize($endDate = null)
    {
        $sum = 0;
        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        } else {
            $endDate = (string) $endDate->format('Y-m-d H:i:s');
        }

        $query = <<<EOT
SELECT SUM ("size") FROM "digitalResource"."digitalResource" WHERE "created"<'$endDate'::timestamp;
EOT;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $sum = $stmt->fetch()['sum'];

        return (integer) $sum;
    }

    protected function getArchiveSizeOrdered($groupBy, $endDate = null)
    {
        switch ($groupBy) {
            case 'archivalProfile':
                $tableProperty = "archivalProfileReference";
                break;
            case 'originatingOrg':
                $tableProperty = "originatorOrgRegNumber";
                break;
            default:
                # code...
                break;
        }

        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        } else {
            $endDate = (string) $endDate->format('Y-m-d H:i:s');
        }

        $query = <<<EOT
SELECT "recordsManagement"."archive"."$tableProperty" AS $groupBy, SUM ("digitalResource"."digitalResource"."size")
FROM "digitalResource"."digitalResource"
JOIN "recordsManagement"."archive"
ON "digitalResource"."digitalResource"."archiveId" = "recordsManagement"."archive"."archiveId"
WHERE "created"<'$endDate'::timestamp
GROUP BY "recordsManagement"."archive"."$tableProperty";
EOT;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $results = [];
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = $result;
        }

        return $results;
    }

    protected function getArchiveCountOrdered($groupBy, $endDate = null)
    {
        switch ($groupBy) {
            case 'archivalProfile':
                $tableProperty = "archivalProfileReference";
                break;
            case 'originatingOrg':
                $tableProperty = "originatorOrgRegNumber";
                break;
            default:
                # code...
                break;
        }

        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        } else {
            $endDate = (string) $endDate->format('Y-m-d H:i:s');
        }

        $query = <<<EOT
SELECT "recordsManagement"."archive"."$tableProperty" AS $groupBy, COUNT ("digitalResource"."digitalResource"."size")
FROM "digitalResource"."digitalResource"
JOIN "recordsManagement"."archive"
ON "digitalResource"."digitalResource"."archiveId" = "recordsManagement"."archive"."archiveId"
WHERE "created"<'$endDate'::timestamp
GROUP BY "recordsManagement"."archive"."$tableProperty";
EOT;

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
     * @param string   $eventTypes                      Types of event
     * @param string   $secondary_parameters            Secondary parameters
     * @param DateTime $startDate                       Start Date
     * @param DateTime $endDate                         End date
     * @param Boolean  $isIncludingChildren             Include children services
     * @param Boolean  $isOrderingByProfile             Ordering by profile
     * @param Boolean  $isGroupByArchiveNumber          Ordering by archiveNumber
     *
     * @return Count
     */
    public function executeQuery($query, $eventTypes, $secondary_parameters, $startDate = null, $endDate = null, $groupBy = null)
    {
        $params = [];


        if (!is_null($startDate)) {
            $params[':startDate'] = (string) $startDate->format('Y-m-d H:i:s');
            $params[':endDate'] = (string) $endDate->format('Y-m-d H:i:s');
            $query .= " AND timestamp BETWEEN :startDate::timestamp AND :endDate::timestamp";
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array_merge($params, $secondary_parameters));
        $results = [];


        // var_dump($stmt->fetch(\PDO::FETCH_ASSOC));
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $results[] = $result;
        }


        return $results;
    }
}
