<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\adminTech;
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
     * @return recordsManagement/serviceLevel/index An array of recordsManagement/serviceLevel objects
     *
     * @uses recordsManagement/serviceLevel/readIndex
     */
    public function readServicelevels();

    /**
     * Add a new service level
     * 
     * @return recordsManagement/serviceLevel/newServiceLevel The new service level object
     * 
     * @uses recordsManagement/serviceLevel/read
     */
    public function readServicelevel();

    /**
     * Create a service level
     * @param recordsManagement/serviceLevel $serviceLevel The service level to create
     * 
     * @return recordsManagement/serviceLevel/create
     * 
     * @uses recordsManagement/serviceLevel/create
     */
    public function createServicelevel($serviceLevel);

    /**
     * Retrieve the service level object by its id
     * 
     * @return recordsManagement/serviceLevel/read
     * 
     * @uses recordsManagement/serviceLevel/read_serviceLevelId_
     */
    public function readServicelevel_serviceLevelId_($serviceLevelId);

    /**
     * Update a service level
     * @param recordsManagement/serviceLevel $serviceLevel
     * 
     * @return recordsManagement/serviceLevel/update
     * 
     * @uses recordsManagement/serviceLevel/update
     */
    public function updateServicelevel_serviceLevelId_($serviceLevel);

    /**
     * Delete a service level
     * 
     * @return recordsManagement/serviceLevel/delete
     * 
     * @uses recordsManagement/serviceLevel/delete_serviceLevelId_
     */
    public function deleteServicelevel_serviceLevelId_($serviceLevelId);

    /**
     * Set the default status to a service level
     * 
     * @return recordsManagement/serviceLevel/setDefault
     * 
     * @uses recordsManagement/serviceLevel/updateSetdefault_serviceLevelId_
     */
    public function updateServicelevel_serviceLevelId_Setdefault($serviceLevelId);

}// END class 
