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
 * Interface for management of retention rules
 *
 * @package Recordsmanagement
 */
interface retentionRuleInterface
{
    /**
     * List the retention rules
     *
     * @action recordsManagement/retentionRule/index
     */
    public function readIndex();

    /**
     * Create a retention orule
     * @param recordsManagement/retentionRule $retentionRule The retention rule
     *
     * @action recordsManagement/retentionRule/create
     */
    public function create($retentionRule);

    /**
     * Create a csv file
     *
     * @param integer $limit Max number of results to display
     *
     * @action recordsManagement/retentionRule/exportCsv
     *
     */
    public function readExport($limit = null);

    /**
     * @param string  $data      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @action recordsManagement/retentionRule/import
     *
     * @return boolean        Import with reset of table data or not
     */
    public function createImport($data, $isReset);

    /**
     * Read a retention rule
     *
     * @action recordsManagement/retentionRule/read
     */
    public function read_code_();

    /**
     * Update a retention rule
     * @param recordsManagement/retentionRule $retentionRule The retention rule
     *
     * @action recordsManagement/retentionRule/update
     */
    public function update($retentionRule);

    /**
     * Delete an retention rule
     *
     * @action recordsManagement/retentionRule/delete
     */
    public function delete_code_();
}
