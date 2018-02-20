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
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\adminFunc;

/**
 * User story admin authorization
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface adminAuthorizationInterface
{
    /**
     * List the authorization's roles
     *
     * @uses auth/role/readIndex
     * @return auth/adminRole/index
     */
    public function readAuthRoles();

    /**
     * Prepare an empty role object
     *
     * @uses auth/role/readNewrole
     * @return auth/adminRole/newRole
     */
    public function readAuthRole();

    /**
     * Add a new role
     * @param auth/role $role The role object to record
     *
     * @uses auth/role/create
     * @return auth/adminRole/create
     */
    public function createAuthRole($role);

    /**
     * Prepares access control object for update or create
     *
     * @uses auth/role/read_roleId_
     * @uses auth/publicUserStory/read
     *
     * @return auth/adminRole/edit
     */
    public function readAuthRole_roleId_();

    /**
     * Updates a role
     * @param auth/role $role The role object
     *
     * @uses auth/role/update_roleId_
     * @return auth/adminRole/update
     */
    public function updateAuthRole_roleId_($role);

    /**
     * Lock or unlock a role
     *
     * @uses auth/role/update_roleId_Status_status_
     *
     * @return auth/adminRole/changeStatus
     */
    public function updateAuthRole_roleId_Status_status_();

    /**
     * Delete an authorization role
     *
     * @uses auth/role/delete_roleId_
     *
     * @return auth/adminRole/delete
     */
    public function deleteAuth_roleId_();

    /**
     * List all users to display
     *
     * @uses auth/userAccount/readIndex
     */
    public function readUserTodisplay();
}