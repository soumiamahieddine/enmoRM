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
 * Interface for management of archival profile
 * 
 * @package RecordsMangement
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */ 
interface archivalProfileInterface
{
    /**
     *  List the profiles
     * @param boolean $withRightsChecking
     * 
     * @action recordsManagement/archivalProfile/index The list of profile
     *
     */
    public function readIndex($withRightsChecking = false);
    
    /**
     *  New profile
     * 
     * @action recordsManagement/archivalProfile/newProfile 
     *
     */
    public function readNew();

    
    /**
     * Edit a archival profile
     * 
     * @action recordsManagement/archivalProfile/read The profile object
     */
    public function read_archivalProfileId_();

    /**
     * Edit a archival profile
     * 
     * @action recordsManagement/archivalProfile/getByReference 
     */
    public function readByreference_reference_();
    
    /**
     * Read a profile description
     * 
     * @action recordsManagement/archivalProfile/getByReference The profile object
     */
    public function readProfiledescription_archivalProfileReference_();

    /**
     * create a archival profile
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     * 
     * @action recordsManagement/archivalProfile/create
     *
     */
    public function create($archivalProfile);

    /**
     * update a archival profile
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     * 
     * @action recordsManagement/archivalProfile/update
     *
     */
    public function update($archivalProfile);

    /**
     * delete an archival profile
     * 
     * @action recordsManagement/archivalProfile/delete
     * 
     */
    public function delete_archivalProfileId_();

    /**
     * Get the profiles by orgRegNumber
     * @param string $orgRegNumber
     * @param string $originatorAccess
     * 
     * @return array
     * @action recordsManagement/archivalProfile/getOrgUnitArchivalProfiles
     */
    public function readOrgunitprofiles($orgRegNumber, $originatorAccess=false);

    /**
     * Get descendant profiles
     * 
     * @action recordsManagement/archivalProfile/getdescendantArchivalProfiles
     */
    public function readDescendantprofiles();
}
