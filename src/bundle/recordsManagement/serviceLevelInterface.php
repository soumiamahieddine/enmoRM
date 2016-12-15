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
namespace bundle\recordsManagement;
/**
 * Class to control archival service level
 *
 * @package RecordsManagement
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */
interface serviceLevelInterface
{
    /**
     * Get all service levels
     * 
     * @action recordsManagement/serviceLevel/index An array of recordsManagement/serviceLevel objects
     *
     */
    public function readIndex();

    /**
     * Add a new service level
     * 
     * @action recordsManagement/serviceLevel/newServiceLevel
     * 
     */
    public function read();

    /**
     * Create a service level
     * @param recordsManagement/serviceLevel $serviceLevel The service level to create
     * 
     * @action recordsManagement/serviceLevel/create
     * 
     */
    public function create($serviceLevel);

    /**
     * Retrieve the service level object by its id
     * 
     * @action recordsManagement/serviceLevel/read The service level
     * 
     */
    public function read_serviceLevelId_();

    /**
     * Retrieve the default service level
     * 
     * @action recordsManagement/serviceLevel/readDefault The service level
     * 
     */
    public function read_Default();

    /**
     * Update a service level
     * @param recordsManagement/serviceLevel $serviceLevel The service level to update
     * 
     * @action recordsManagement/serviceLevel/update
     * 
     */
    public function update($serviceLevel);

    /**
     * Delete a service level
     * 
     * @action recordsManagement/serviceLevel/delete
     */
    public function delete_serviceLevelId_();

    /**
     * Set the default status to a service level
     * 
     * @action recordsManagement/serviceLevel/setDefault Sets the service level as default
     * 
     */
    public function updateSetdefault_serviceLevelId_();

}// END class 
