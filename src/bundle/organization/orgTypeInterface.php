<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\organization;
/**
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface orgTypeInterface 
{
    /**
     * Get the organization types
     *
     * @action organization/orgType/index
     */
    public function readList();

    /**
     * Add an org type
     * @param organization/orgType $orgType the orgType to create
     *
     * @action organization/orgType/create
     */
    public function create($orgType);
    
    /**
     * Edit an org type 
     *
     * @action organization/orgType/read
     */
    public function read_code_();
    
    /**
     * Update an org type
     * @param organization/orgType $orgType The orgType to update
     *
     * @action organization/orgType/update
     */
    public function update_code_($orgType);

    /**
     * Delete an org type
     *
     * @return boolean The result of the operation
     *
     * @action organization/orgType/delete
     */
    public function delete_code_();
}
