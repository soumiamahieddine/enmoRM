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
interface retentionRuleInterface
{
    /**
     *  List the access rule's code
     * 
     * @action recordsManagement/retentionRule/index 
     */
    public function readIndex();

    /**
     * Create an access rule
     * @param recordsManagement/retentionRule $retentionRule The preservation rule
     * 
     * @action recordsManagement/retentionRule/create
     *
     */
    public function create($retentionRule);

    /**
     * Read an access rule
     * 
     * @action recordsManagement/retentionRule/read
     *
     */
    public function read_code_();

    /**
     *  Update an access rule
     * @param recordsManagement/retentionRule $retentionRule The access rule
     * 
     * @action recordsManagement/retentionRule/update
     *
     */
    public function update($retentionRule);

    /**
     *  Delete an access rule
     * 
     * @action recordsManagement/retentionRule/delete
     * 
     */
    public function delete_code_();

}