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
     * @param boolean $originatingOrg      Originating organization
     * @param boolean $archivalProfile     Archival Profile
     *
     * @return array Array of counts
     */
    public function retrieve($operation = null, $startDate = null, $endDate = null, $originatingOrg = null, $archivalProfile = null)
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

        // if (is_null($operation) || empty($operation)) {
        //     throw new \core\Exception\BadRequestException("Operation is mandatory");
        // }

        if (!is_null($operation) && !in_array($operation, ['deposit', 'delete', 'conserved'])) {
            throw new \core\Exception\BadRequestException("Operation type not supported");
        }

        if ($this->sdoFactory->exists('organization/organization', ['registrationNumber' => $originatingOrg])) {
            throw new \core\Exception\BadRequestException("Originating Organization does not exists");
        }

        if ($this->sdoFactory->exists('recordsManagement/archivalProfile', ['reference' => $archivalProfile])) {
            throw new \core\Exception\BadRequestException("Archival Profile does not exists");
        }

        $depositMemorySize = $this->getMessageSize(['recordsManagement/deposit', 'recordsManagement/depositNewResource'], $operation, $jsonColumnNumber = 8, $startDate, $endDate, $originatingOrg, $archivalProfile);
        $deletedMemorySize = $this->getMessageSize(['recordsManagement/destruction'], $operation, $jsonColumnNumber = 6, $startDate, $endDate, $originatingOrg, $archivalProfile);
        $currentMemorySize = $this->getArchiveSize($endDate);

        if (\laabs::configuration('medona')['transaction']) {
            $transferredMemoryize = ;
            $restitutionMemorySize = ;
        }

        return [
            'depositMemorySize' => $depositMemorySize,
            'deletedMemorySize' => $deletedMemorySize,
            'currentMemorySize' => $currentMemorySize
        ];
    }

    protected function getMessageSize($eventTypes, $operation, $jsonColumnNumber, $startDate = null, $endDate = null, $originatingOrg = null, $archivalProfile = null)
    {
        $sum = 0;

        $explodingEventTypes = $this->stringifyEventTypes($eventTypes);
        $in = $explodingEventTypes['in'];
        $inParams = $explodingEventTypes['inParams'];

        // $query = 'SELECT SUM (CAST (NULLIF("eventInfo"::json->>8,'') AS INTEGER)) from "lifeCycle"."event" WHERE "eventType IN (' . $in . ')';

        $query = <<<EOT
SELECT SUM (CAST(NULLIF("eventInfo"::json->>$jsonColumnNumber, '') AS INTEGER)) FROM "lifeCycle"."event" WHERE "eventType" IN ($in)
EOT;
        $sum += $this->executeQuery($query, $eventTypes, $inParams, $operation, $startDate, $endDate, $isIncludingChildren = null);

        return $sum;
    }

    protected function getArchiveSize($endDate = null)
    {
        $sum = 0;
        if (is_null($endDate)) {
            $endDate = (string) \laabs::newDateTime()->format('Y-m-d H:i:s');
        }
        // var_dump($endDate);
        // exit;
        $query = <<<EOT
SELECT SUM ("size") FROM "digitalResource"."digitalResource" WHERE "created"<'$endDate'::timestamp;
EOT;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        $sum = $stmt->fetch()['sum'];

        return (integer) $sum;
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
     * @param string   $brigade                         OrgName
     * @param DateTime $startDate                       Start Date
     * @param DateTime $endDate                         End date
     * @param Boolean  $isIncludingChildren             Include children services
     * @param Boolean  $isOrderingByProfile             Ordering by profile
     * @param Boolean  $isGroupByArchiveNumber          Ordering by archiveNumber
     *
     * @return Count
     */
    public function executeQuery($query, $eventTypes, $secondary_parameters, $brigade = null, $startDate = null, $endDate = null, $isIncludingChildren = false, $isOrderingByProfile = false)
    {
        $params = [];
        // if ($brigade) {
        //     $params = [
        //         ':brigade'=> $brigade
        //     ];
        // }


        if (!is_null($startDate)) {
            $params[':startDate'] = (string) $startDate->format('Y-m-d H:i:s');
            $params[':endDate'] = (string) $endDate->format('Y-m-d H:i:s');
            $query .= " AND timestamp BETWEEN :startDate::timestamp AND :endDate::timestamp";
        }

        // if ($isOrderingByProfile) {
        //     $query .= ' GROUP BY "eventInfo"::jsonb->>2';
        // }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute(array_merge($params, $secondary_parameters));
        $count = 0;


        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $count += $result['sum'];
        }


        return $count;
    }
}
