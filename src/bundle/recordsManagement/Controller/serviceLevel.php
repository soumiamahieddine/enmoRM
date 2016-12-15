<?php

/*
 * Copyright (C) 2015 Maarch
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

namespace bundle\recordsManagement\Controller;

/**
 * Class to control archival service level
 *
 * @package RecordsManagement
 * @author  Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class serviceLevel
{

    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get all service levels
     *
     * @return recordsManagement/serviceLevel[] An array of recordsManagement/serviceLevel objects
     */
    public function index()
    {
        $serviceLevels = $this->sdoFactory->find('recordsManagement/serviceLevel');

        foreach ($serviceLevels as $level) {
            $cluster = $this->sdoFactory->read('digitalResource/cluster', $level->digitalResourceClusterId);
            $level->clusterName = $cluster->clusterName;
        }

        return $serviceLevels;
    }

    /**
     * Add a new service level
     *
     * @return recordsManagement The new service level object
     */
    public function newServiceLevel()
    {
        return \laabs::newInstance('recordsManagement/serviceLevel');
    }

    /**
     * Create a service level
     * @param recordsManagement/serviceLevel $serviceLevel The service level to create
     *
     * @return string The id of the new service level
     */
    public function create($serviceLevel)
    {
        $serviceLevel = \laabs::cast($serviceLevel, 'recordsManagement/serviceLevel');
        $defaultServiceLevel = $this->sdoFactory->find("recordsManagement/serviceLevel", "default=true");
        if (!count($defaultServiceLevel)) {
            $serviceLevel->default = true;
        }

        $serviceLevel->serviceLevelId = \laabs::newId();
        try{
        $this->sdoFactory->create($serviceLevel, "recordsManagement/serviceLevel");

        }  catch (\Exception $e){
            throw new \bundle\recordsManagement\Exception\serviceLevelException("Service level not created.");
        }

        return $serviceLevel->serviceLevelId;
    }

    /**
     * Retrieve the service level object by its id
     * @param string $serviceLevelId The service level identifier
     *
     * @return recordsManagement/serviceLevel The service level
     */
    public function read($serviceLevelId)
    {
        $serviceLevel = $this->sdoFactory->read("recordsManagement/serviceLevel", $serviceLevelId);

        return $serviceLevel;
    }

    /**
     * Retrieve the default service level
     *
     * @return recordsManagement/serviceLevel The service level
     */
    public function readDefault()
    {
        $serviceLevel = $this->sdoFactory->find("recordsManagement/serviceLevel", "default = true");

        if (count($serviceLevel) == 0) {
            throw new \bundle\recordsManagement\Exception\serviceLevelException("No default service level found.");
        }

        return $serviceLevel[0];
    }

    /**
     * Update a service level
     * @param recordsManagement/serviceLevel $serviceLevel The service level to update
     *
     * @return boolean The result of the request
     */
    public function update($serviceLevel)
    {
        $serviceLevel = \laabs::cast($serviceLevel, 'recordsManagement/serviceLevel');
        try{
            $result = $this->sdoFactory->update($serviceLevel, "recordsManagement/serviceLevel");

        }  catch (\Exception $e){
            throw new \bundle\recordsManagement\Exception\serviceLevelException("Service level not updated.");
        }

        return $result;
    }

    /**
     * Delete a service level
     * @param string $serviceLevelId The service level identifier to delete
     *
     * @return boolean The result of the request
     */
    public function delete($serviceLevelId)
    {
        $serviceLevel = $this->sdoFactory->read("recordsManagement/serviceLevel", $serviceLevelId);
        
        if ($serviceLevel->default) {
            return false;
        }
    
        try{
            $result = $this->sdoFactory->delete($serviceLevel, "recordsManagement/serviceLevel");

        }  catch (\Exception $e){
            throw new \bundle\recordsManagement\Exception\serviceLevelException("Service level not deleted.");
        }
        return $result;
    }

    /**
     * Retrieve the service level object by its id
     * @param string $serviceLevelRef The service level's name
     *
     * @return recordsManagement/serviceLevel The service level
     */
    public function getByReference($serviceLevelRef)
    {
        $serviceLevel = $this->sdoFactory->read("recordsManagement/serviceLevel", array("reference" => $serviceLevelRef));

        return $serviceLevel;
    }

    /**
     * Set the default status to a service level
     * @param string $serviceLevelId The service level's identifier
     *
     * @return recordsManagement/serviceLevel The service level
     */
    public function setDefault($serviceLevelId)
    {
        $defaultServiceLevel = $this->sdoFactory->find("recordsManagement/serviceLevelStatus", "default = true");

        //$this->sdoFactory->beginTransaction();
        //try {
        if ($defaultServiceLevel) {
            $defaultServiceLevel = $defaultServiceLevel[0];
            $defaultServiceLevel->default = false;
            $this->sdoFactory->update($defaultServiceLevel, "recordsManagement/serviceLevelStatus");
        }

        $serviceLevelStatus = \laabs::newInstance('recordsManagement/serviceLevelStatus');
        $serviceLevelStatus->serviceLevelId = $serviceLevelId;
        $serviceLevelStatus->default = true;
        $this->sdoFactory->update($serviceLevelStatus, "recordsManagement/serviceLevelStatus");

        return true;
    }

    /**
     * Get default service level
     *
     * @return recordsManagement/serviceLevel
     */
    public function getDefault()
    {
        $serviceLevel = $this->sdoFactory->find("recordsManagement/serviceLevel", "default=true")[0];

        return $serviceLevel;
    }

}

// END class
