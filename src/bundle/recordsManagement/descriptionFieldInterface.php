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
 * API for description fields of the data dictionnary
 */
interface descriptionFieldInterface
{
    /**
     *  List the description field's code
     *
     * @action recordsManagement/descriptionField/index
     */
    public function readIndex();

    /**
     * Create a description field
     * @param recordsManagement/descriptionField $descriptionField The description field
     *
     * @action recordsManagement/descriptionField/create
     *
     */
    public function create($descriptionField);

    /**
     * Create a csv file
     *
     * @param  integer $limit Max number of results to display
     *
     * @action recordsManagement/descriptionField/exportCsv
     *
     */
    public function readExport($limit = null);

    /**
     * Read a description field
     *
     * @action recordsManagement/descriptionField/read
     *
     */
    public function read_name_();

    /**
     *  Update a description field
     * @param recordsManagement/descriptionField $descriptionField The description field
     *
     * @action recordsManagement/descriptionField/update
     *
     */
    public function update($descriptionField);

    /**
     *  Delete a description field
     *
     * @action recordsManagement/descriptionField/delete
     *
     */
    public function delete_name_();

    /**
     * @param string  $data     Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @action recordsManagement/descriptionField/import
     *
     * @return boolean        Import with reset of table data or not
     */
    public function createImport($data, $isReset);
}
