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
namespace presentation\maarchRM\UserStory\adminArchive;
/**
 * Interface for management of archival profile
 * 
 * @package RecordsMangement
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */ 
interface archivalProfileInterface
{
    /**
     *  List archival profiles
     * 
     * @return recordsManagement/archivalProfile/index
     *
     */
    public function readArchivalprofiles();

    /**
     * Get the archivalProfiles list
     *
     * @return recordsManagement/archivalProfile/archivalProfileList
     * @uses recordsManagement/archivalProfile/readIndex
     */
    public function readArchivalprofilesTodisplay();

    /**
     * New empty archival profile with default values
     * 
     * @return recordsManagement/archivalProfile/edit The archival profile edition view
     * @uses recordsManagement/archivalProfile/readNew
     * 
     */
    public function readArchivalprofile();

    /**
     * create a archival profile
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     * 
     * @return recordsManagement/archivalProfile/create
     * @uses recordsManagement/archivalProfile/create
     *
     */
    public function createArchivalprofile($archivalProfile);

    /**
     * Edit a archival profile
     * 
     * @return recordsManagement/archivalProfile/edit The profile object
     * @uses recordsManagement/archivalProfile/read_archivalProfileId_
     */
    public function readArchivalprofile_archivalProfileId_();    

    /**
     * update a archival profile
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     * 
     * @return recordsManagement/archivalProfile/update
     * @uses recordsManagement/archivalProfile/update
     */
    public function updateArchivalprofile_archivalProfileId_($archivalProfile);

    /**
     * delete an archival profile
     * 
     * @return recordsManagement/archivalProfile/delete
     * @uses recordsManagement/archivalProfile/delete_archivalProfileId_
     */
    public function deleteArchivalprofile_archivalProfileId_();

    /**
     * Get an archival profile barcode
     * 
     * @param string $data  The data of codes
     * @param string $label The label
     *
     * @return recordsManagement/archivalProfile/barcode
     *
     * @uses recordsManagement/code/createGenerate
     */
    public function readArchivalprofilebarcode($data, $label);

    /**
     * Upload profile reference
     * 
     * @return recordsManagement/archivalProfile/uploadArchivalProfile 
     * @uses recordsManagement/archivalProfile/createArchivalprofileUpload_profileReference_
     * 
     */
    public function createArchivalprofileUpload_profileReference_();
    
    /**
     * Export profile file
     * 
     * @return recordsManagement/archivalProfile/export 
     * @uses recordsManagement/archivalProfile/readArchivalprofileExport_profileReference_
     * 
     */
    public function readArchivalprofileExport_profileReference_();
}