<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle audit.
 *
 * Bundle audit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle audit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle audit.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\audit\Controller;

/**
 * Controller for the audit trail event
 *
 * @package Audit
 */
class event
{

    protected $sdoFactory;
    protected $separateInstance;
    protected $notifications;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory
     * @param string                  $separateInstance Read only instance events
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $separateInstance = false, $notifications = null)
    {
        $this->sdoFactory = $sdoFactory;

        $this->separateInstance = $separateInstance;
        $this->notifications = $notifications;
    }

    /**
     * Create a new audit trail event
     * @param string $path      The path of called service
     * @param mixed  $variables The path variables
     * @param mixed  $input     The input data
     * @param string $output    The output data
     * @param bool   $status    The result of action: success or failure (business exception)
     * @param mixed  $info      The info on caller process/client/system
     *
     * @return id The identifier of the newly added event
     */
    public function add($path, array $variables = null, $input = null, $output = null, $status = false, $info = null)
    {
        // Event creation
        $event = \laabs::newInstance('audit/event');
        $event->eventId = \laabs::newId();
        $event->eventDate = \laabs::newTimestamp();

        if ($accountToken = \laabs::getToken('AUTH')) {
            $event->accountId = $accountToken->accountId;
        } else {
            $event->accountId = "__system__";
        }

        $event->path = $path;
        $event->status = $status;

        if (count($variables)) {
            $event->variables = \laabs::newJson($variables);
        }
        if (isset($input)) {
            $event->input = \laabs::newJson($input);
        }
        if (isset($output)) {
            $event->output = (string) $output;
        }

        if (!isset($info)) {
            $info = array();
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $info['remoteIp'] = $_SERVER['REMOTE_ADDR'];
            }

            if (count($info)) {
                $event->info = \laabs::newJson($info);
            }
        } else {
            $event->info = \laabs::newJson($info);
        }
        
        if ($currentOrganization = \laabs::getToken("ORGANIZATION")) {
            $organizationController = \laabs::newController('organization/organization');
            $organization = $organizationController->read($currentOrganization->ownerOrgId);

            $event->orgRegNumber = $organization->registrationNumber;
            $event->orgUnitRegNumber = $currentOrganization->registrationNumber;
        }
        $event->instanceName = \laabs::getInstanceName();

        if (!empty($this->notifications) && isset($this->notifications[$path])) {
            $rule = $this->notifications[$path];
            if (($rule["onResult"]) == $status) {
                $notificationController = \laabs::newController('batchProcessing/notification');
                $notificationController->create(
                    $rule["title"],
                    $rule["message"],
                    $rule["receivers"]
                );
            }
        }
        
        $this->sdoFactory->create($event);

        return $event->eventId;
    }

    /**
     * Find events for a given type
     * @param string $eventType The type of event
     *
     * @return audit/events[] The array of audit events for the object
     */
    public function byType($eventType)
    {
        $queryString = "path='$eventType'";
        if ($this->separateInstance) {
            $queryString .= "AND instanceName = '".\laabs::getInstanceName()."'";
        }

        $events = $this->sdoFactory->find('audit/event', $queryString);

        return $events;
    }

    /**
     * Find entries for a given type domain
     * @param string $domain The domain of event
     *
     * @return audit/eventInfo[] The array of audit entries for the object
     */
    public function byDomain($domain)
    {
        $queryString = "eventType='$domain/*'";
        if ($this->separateInstance) {
            $queryString .= "AND instanceName = '".\laabs::getInstanceName()."'";
        }

        $entries = $this->sdoFactory->find('audit/eventInfo', $queryString);

        return $entries;
    }

    /**
     * Find events for a identified user
     * @param string $accountId The type of object
     *
     * @return audit/event[] The array of audit events for the object
     */
    public function byAccount($accountId)
    {
        $queryString = "accountId='$accountId' OR serviceAccountId='$accountId'";

        if ($this->separateInstance) {
            $queryString = "instanceName = '".\laabs::getInstanceName()."' AND " . $queryString;
        }

        $events = $this->sdoFactory->find('audit/event', "accountId='$accountId'");
        
        if ($events) {
            return $events;
        }

        return null;
    }

    /**
     * Find events for a given type domain
     * @param timestamp $fromdate
     * @param timestamp $todate
     *
     * @return audit/eventInfo[] The array of audit evesnt for the object
     */
    public function byDate($fromdate = null, $todate = null)
    {
        $args = array();
        if ($fromdate) {
            $args[] = "eventDate>='$fromdate'";
        }
        if ($todate) {
            $args[] = "eventDate<='$todate'";
        }
        if ($this->separateInstance) {
            $args[] = "instanceName = '".\laabs::getInstanceName()."'";
        }

        $events = $this->sdoFactory->find('audit/event', implode(' and ', $args));

        return $events;
    }

    /**
     * Get result of search form
     *
     * @param timestamp $fromDate
     * @param timestamp $toDate
     * @param string    $event
     * @param string    $accountId
     * @param string    $status
     * @param string    $term       Term to search
     * @param integer   $maxResults Max results to display
     *
     * @return audit/event[] Array of audit/event object
     */
    public function search($fromDate = null, $toDate = null, $event = null, $accountId = null, $status = null, $term = null, $maxResults = null)
    {
        list($queryString, $queryParams) = $this->queryBuilder($fromDate, $toDate, $event, $accountId, $status, $term);

        $events = $this->sdoFactory->find("audit/event", $queryString, $queryParams, ">eventDate", 0, $maxResults);

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

            $event->origin = strtok($event->path, LAABS_URI_SEPARATOR);
            $event->typeCode = strtok(LAABS_URI_SEPARATOR);
        }

        return $events;
    }

    /**
     * Get count result of search form
     *
     * @param timestamp $fromDate
     * @param timestamp $toDate
     * @param string    $event
     * @param string    $accountId
     * @param string    $status
     * @param string    $term       Term to search
     *
     * @return integer Count of max results from query
     */
    public function count($fromDate = null, $toDate = null, $event = null, $accountId = null, $status = null, $term = null)
    {
        list($queryString, $queryParams) = $this->queryBuilder($fromDate, $toDate, $event, $accountId, $status, $term);

        $count = $this->sdoFactory->count("audit/event", $queryString, $queryParams, ">eventDate");

        return $count;
    }

    /**
     * Build query
     *
     * @param timestamp $fromDate
     * @param timestamp $toDate
     * @param string    $event
     * @param string    $accountId
     * @param string    $status
     * @param string    $term       Term to search
     * @param string    $wording    Wording to search
     */
    private function queryBuilder($fromDate = null, $toDate = null, $event = null, $accountId = null, $status = null, $term = null, $wording = null)
    {
        $queryParts = [];
        $queryParams = [];

        if ($fromDate) {
            $queryParams['fromDate'] = $fromDate;
            $queryParts['fromDate'] = "eventDate >= :fromDate";
        }
        if ($toDate) {
            $queryParams['eventDate'] = $toDate->add(new \DateInterval('PT23H59M59S'));
            $queryParts['eventDate'] = "eventDate <= :eventDate";
        }

        if ($event) {
            $queryParams['path'] = $event;
            $queryParts['path'] = "path=:path";
        }
        if ($accountId) {
            $queryParams['accountId'] = $accountId;
            $queryParts['accountId'] = "accountId = :accountId";
        }
        if ($status) {
            if ($status != 'all') {
                $queryParams['status'] = $status;
                $queryParts['status'] = "status = :status";
            }
        }
        if ($term) {
            $queryParts['term'] = "(info ='*".$term."*' OR input = '*".$term."*' OR variables = '*".$term."*')";
        }
        if ($this->separateInstance) {
            $queryParts['instanceName'] = "instanceName = '".\laabs::getInstanceName()."'";
        }

        $queryString = implode(' AND ', $queryParts);

        return [$queryString, $queryParams];
    }

    /**
     * Get event
     * @param string $eventId
     *
     * @return audit/event Object
     */
    public function getEvent($eventId)
    {
        $event = $this->sdoFactory->read("audit/event", $eventId);

        return $event;
    }
}
