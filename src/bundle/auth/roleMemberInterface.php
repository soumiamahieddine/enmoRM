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
 * Interface for roleMember
 */
interface roleMemberInterface
{
    /**
     * List the authorization's roleMembers
     *
     * @action auth/roleMember/index
     *
     */
    public function readIndex();

    /**
     * Prepares access control object for update or create
     *
     * @action auth/roleMember/editByRole
     *
     */
    public function readByrole_roleId_();

    /**
     * Prepares access control object for update or create
     *
     * @action auth/roleMember/readByUserAccount
     *
     */
    public function readByuseraccount_userAccountId_();

    /**
     * Create a new role
     * @param id $roleId
     * @param id $userAccountId
     *
     * @action auth/roleMember/create
     *
     */
    public function create($roleId, $userAccountId);

    /**
     * Delete a role
     *
     * @action auth/roleMember/delete
     *
     */
    public function delete_roleId__userAccountId_();
}
