<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle batchProcessing.
 *
 * Bundle batchProcessing is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle batchProcessing is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle batchProcessing.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\batchProcessing\Controller;

/**
 * Controller for the audit trail event
 *
 * @package BatchProcessing
 */
class logScheduling
{

    protected $sdoFactory;
    protected $separateInstance;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory
     * @param string                  $separateInstance Read only instance events
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $separateInstance = false)
    {
        $this->sdoFactory = $sdoFactory;

        $this->separateInstance = $separateInstance;
    }

    /**
     * Create a new log scheduling
     * @param string  $schedulingId The scheduling identifier
     * @param string  $executedBy   The service account
     * @param string  $launchedBy   The account
     * @param boolean $status       Status
     * @param string  $info         Info
     *
     * @return id The identifier of the newly added log
     */
    public function add($schedulingId, $executedBy, $launchedBy, $status, $info = null)
    {
        $logScheduling = \laabs::newInstance('batchProcessing/logScheduling');
        $logScheduling->logId = \laabs::newId();
        $logScheduling->logDate = \laabs::newDateTime();
        $logScheduling->schedulingId = $schedulingId;
        $logScheduling->executedBy = $executedBy;
        $logScheduling->launchedBy = $launchedBy;
        $logScheduling->status = $status;

        if ($info) {
            $info = is_string($info) ? $info : json_encode($info);
            $logScheduling->info = $info;
        }

        $this->sdoFactory->create($logScheduling);

        return $logScheduling->logId;
    }

    /**
     * Get result of search form
     * @param string        $name           The name of scheduling
     * @param string        $task           The task
     * @param string        $executedBy     The service account
     * @param string        $launchedBy       The userAccount
     * @param boolean       $status          Status
     * @param timestamp     $fromDate
     * @param timestamp     $toDate
     *
     * @return array Array of batchScheduling/logScheduling object
     */
    /*public function search($name = null, $task = null, $executedBy = null, $launchedBy = null, $status = null, $fromDate =null, $toDate =  null)
    {
        $logSchedulings = array();
        $queryParts = array();
        $queryParams = array();
        if ($fromDate) {
            $queryParams['fromDate'] = $fromDate;
            $queryParts['fromDate'] = "logDate >= :fromDate";
        }

        if ($toDate) {
            $queryParams['logDate'] = $toDate->add(new \DateInterval('PT23H59M59S'));
            $queryParts['logDate'] = "logDate <= :logDate";
        }
        
        if ($task) {
            $queryParams['task'] = $task;
            $queryParts['task'] = "task = :task";
        }
        
        if ($name) {
            $queryParams['name'] = $name;
            $queryParts['name'] = "name = :name";
        }
        
        if ($executedBy) {
            $queryParams['executedBy'] = $executedBy;
            $queryParts['executedBy'] = "executedBy = :executedBy";
        }
        
        if ($launchedBy) {
            $queryParams['launchedBy'] = $launchedBy;
            $queryParts['launchedBy'] = "launchedBy = :launchedBy";
        }
        
        if (!is_null($status)) {
            if ($status) {
                $queryParams['status'] = 1;
                
            } else {
                $queryParams['status'] = 0;
            }
            $queryParts['status'] = "status = :status";
        }
        
        $queryString = implode(' AND ', $queryParts );
        //$length = \laabs::getRequestMaxCount();
        $length = 400;
        $logSchedulings = $this->sdoFactory->find("batchProcessing/logScheduling", $queryString, $queryParams, ">logDate", 0, $length);
        
        return $logSchedulings;
    }*/

    /**
     * Get event
     * @param string $schedulingId the scheduling identifier
     *
     * @return array The list of log shceduling object
     */
    public function getLogBySchedulingId($schedulingId)
    {
        $logScheduling = $this->sdoFactory->find("batchProcessing/logScheduling", "schedulingId='$schedulingId'");

        return $logScheduling;
    }

    /**
     * Get event
     * @param string $logId
     *
     * @return batchProcessing/logScheduling Object
     */
    public function getLog($logId)
    {
        $logScheduling = $this->sdoFactory->read("batchProcessing/logScheduling", $logId);
        $schedulling = $this->sdoFactory->read("batchProcessing/scheduling", $logScheduling->schedulingId);
        $logScheduling->name = $schedulling->name;

        return $logScheduling;
    }
}
