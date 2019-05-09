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

namespace presentation\maarchRM\UserStory\adminArchive;

/**
 * Interface for management of description fields
 *
 * @package RecordsMangement
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface descriptionFieldInterface
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
     * Update a description field
     * @param string $reffile The description field reference file
     *
     * @return recordsManagement/descriptionRef/create
     * @uses recordsManagement/descriptionRef/create_name_
     */
    public function createDescriptionref_name_($reffile);

    /**
     * Read a description ref
     *
     * @return recordsManagement/descriptionRef/read
     * @uses recordsManagement/descriptionRef/read_name_
     */
    public function readDescriptionref_name_();
}
