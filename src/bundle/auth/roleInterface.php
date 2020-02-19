<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle auth.
 *
 * Bundle auth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle auth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle auth.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\auth;

/**
 * Interface for role
 */
interface roleInterface
{
    /**
     * List the authorization's roles
     *
     * @action auth/role/index
     */
    public function readIndex();

    /**
     * Prepare an empty role object
     *
     * @action auth/role/newRole
     */
    public function readNewrole();

    /**
     * Create a csv file
     *
     * @param @param  integer $limit Max number of results to display
     *
     * @action auth/role/exportCsv
     *
     */
    public function readExport($limit = null);

    /**
     * @param resource  $data     Data base64 encoded or not
     * @param boolean   $isReset  Reset tables or not
     *
     * @action auth/role/import
     *
     * @return boolean        Import with reset of table data or not
     */
    public function createImport($data, $isReset);

    /**
     * Prepares access control object for update or create
     *
     * @return auth/role
     *
     * @action auth/role/edit
     */
    public function read_roleId_();

    /**
     * Get the role privileges on userStories
     *
     * @action auth/role/getPrivilege
     */
    public function read_roleId_Privileges();

    /**
     * Create a new role
     * @param auth/role $role
     *
     * @action auth/role/create
     */
    public function create($role);

    /**
     * Create a new privilege
     * @param string $userStory
     *
     * @action auth/role/addPrivilege
     */
    public function create_roleId_Privilege($userStory);

    /**
     * Updates a role
     * @param auth/role $role
     *
     * @action auth/role/update
     */
    public function update_roleId_($role);

    /**
     * Lock or unlock a role
     *
     * @action auth/role/changeStatus
     */
    public function update_roleId_Status_status_();

    /**
     * Delete a privilege
     * @param auth/privilege $privilege The privilege object
     *
     * @action auth/role/deletePrivilege
     */
    public function deletePrivilege($privilege);

    /**
     * Delete an authorization role
     *
     * @action auth/role/delete
     */
    public function delete_roleId_();

    /**
     * Get the list of available persons
     *
     * @action auth/role/queryRoles
     */
    public function readRoles_query_();
}
