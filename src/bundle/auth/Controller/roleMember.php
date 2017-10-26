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

namespace bundle\auth\Controller;

/**
 * Controler for the roleMember
 *
 * @package Auth
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
class roleMember
{

    public $sdoFactory;

    /**
     * Constructor of roleMember class
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Prepares a roleMember object by roleId
     * @param string $roleId The identifier of the role
     *
     * @return auth/roleMember The requested role
     */
    public function editByRole($roleId)
    {
        $role = $this->sdoFactory->read("auth/role", $roleId);

        $roleMembers = $this->sdoFactory->readChildren("auth/roleMember", $role);

        return $roleMembers;
    }

    /**
     * Prepares a roleMember object by userAccountId
     * @param string $userAccountId The identifier of the user account
     *
     * @return auth/roleMember The requested role
     */
    public function readByUserAccount($userAccountId)
    {
        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);

        $roleController = \laabs::newController("auth/role");
        $roles = $roleController->index();

        $rolesId = [];
        foreach ($roles as $role) {
            if ($role->enabled) {
                $rolesId[] = $role->roleId;
            }
        }

        $queryString = [];
        $queryString[] = "userAccountId='$userAccount->accountId'";

        if (!empty($rolesId)) {
            $rolesId = \laabs\implode("','", $rolesId);
            $queryString[] = "roleId=['$rolesId']";
        }

        $roleMembers = $this->sdoFactory->find("auth/roleMember", \laabs\implode(" AND ", $queryString));

        return $roleMembers;
    }

    /**
     * Record a roleMember
     * @param id $roleId
     * @param id $userAccountId
     *
     * @return boolean The status of the query
     */
    public function create($roleId, $userAccountId)
    {

        $roleMember = \laabs::newInstance("auth/roleMember");
        $roleMember->userAccountId = $userAccountId;
        $roleMember->roleId = $roleId;

        try {
            $this->sdoFactory->create($roleMember);
        } catch (\Exception $e) {
            throw \laabs::newException("auth/sdoException");
        }

        return true;
    }

    /**
     * Delete a roleMember
     * @param auth/roleMember $roleMember Object of roleMember
     *
     * @return boolean The status of the query
     */
    public function delete($roleMember)
    {
        $roleMember = \laabs::cast($roleMember, "auth/roleMember");
        $param = $this->sdoFactory->delete($roleMember);

        return $param;
    }
}
