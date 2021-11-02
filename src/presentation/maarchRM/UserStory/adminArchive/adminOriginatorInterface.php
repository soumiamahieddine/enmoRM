<?php
/*
 * Copyright (C) 2021 Maarch
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

namespace presentation\maarchRM\UserStory\adminArchive;

/**
 * Interface for management of description fields
 *
 * @package adminArchive
 * @author Jerome Boucher <alexis.ragot@maarch.org>
 */
interface adminOriginatorInterface
{
    /**
     *  List of description fields
     *
     * @return recordsManagement/descriptionField/index
     */
    public function readDescriptionfields();

    /**
     * create a description field
     * @param recordsManagement/descriptionField $descriptionField The description field object
     *
     * @return recordsManagement/descriptionField/create
     *
     * @uses recordsManagement/descriptionField/create
     */
    public function createDescriptionfield($descriptionField);

    /**
     * Edit a description field
     *
     * @return recordsManagement/descriptionField/edit The profile object
     * @uses recordsManagement/descriptionField/read_name_
     */
    public function readDescriptionfield_name_();

    /**
     * Update a description field
     * @param recordsManagement/descriptionField $descriptionField The description field object
     *
     * @return recordsManagement/descriptionField/update
     * @uses recordsManagement/descriptionField/update
     */
    public function updateDescriptionfield_name_($descriptionField);
    
    /**
     * Delete a description field
     *
     * @return recordsManagement/descriptionField/delete
     * @uses recordsManagement/descriptionField/delete_name_
     */
    public function deleteDescriptionfield_name_();

    /**
     * Update a description field
     * @param string $reffile The description field reference file
     *
     * @uses recordsManagement/descriptionField/update_name_Ref
     */
    public function updateDescriptionfield_name_Ref($reffile);

    /**
     * Read a description ref
     *
     * @return recordsManagement/descriptionField/readRef
     * @uses recordsManagement/descriptionField/read_name_Ref
     */
    public function readDescriptionfield_name_Ref();

    /**
     * @uses recordsManagement/archives/updateOriginator
     */
    public function updateRecordsmanagementArchiveOriginator($archiveIds, $orgId);
}
