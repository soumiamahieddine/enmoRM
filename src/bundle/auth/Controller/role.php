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
 * Controler for the role
 *
 * @package Auth
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class role
{

    public $sdoFactory;

    /**
     * Constructor of adminRole class
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * List roles
     *
     * @return array Array of auth/role object
     */
    public function index()
    {
        return $this->sdoFactory->find("auth/role");
    }

    /**
     * Prepare an empty role object
     *
     * @return auth/role The empty role object
     */
    public function newRole()
    {
        return \laabs::newInstance('auth/role');
    }

    /**
     * Prepares a role object for update
     * @param string $roleId The identifier of the role
     *
     * @return auth/role The requested role
     */
    public function edit($roleId)
    {
        $role = $this->sdoFactory->read("auth/role", $roleId);
        $role->privileges = array();

        foreach ($this->sdoFactory->readChildren("auth/privilege", $role) as $privilege) {
            $role->privileges[] = $privilege->userStory;
        }

        $role->roleMembers = array();

        foreach ($this->sdoFactory->readChildren("auth/roleMember", $role) as $roleMember) {
            $role->roleMembers[] = $roleMember->userAccountId;
        }

        return \laabs::castMessage($role, 'auth/role');
    }

    /**
     * Recorde a new role
     * @param auth/role $role The role object to create
     *
     * @return string The new role id
     */
    public function create($role)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $roleInstance = \laabs::newInstance("auth/role");
            $roleInstance->roleId = \laabs::newId();
            $roleInstance->roleName = $role->roleName;
            $roleInstance->description = $role->description;
            $roleInstance->enabled = $role->enabled;

            $this->sdoFactory->create($roleInstance);

            if (!empty($role->privileges)) {
                foreach ($role->privileges as $userStory) {
                    $this->addPrivilege($roleInstance->roleId, $userStory);
                }
            }

            if (!empty($role->roleMembers)) {
                foreach ($role->roleMembers as $userAccountId) {
                    \laabs::callService("auth/roleMember/create", $roleInstance->roleId, $userAccountId);
                }
            }

        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw \laabs::newException("auth/adminRoleException", "Role not created");
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $roleInstance->roleId;
    }

    /**
     * Updates a role
     * @param id        $roleId The role identifier
     * @param auth/role $role   The role info to update
     *
     * @return boolean The status of the query
     */
    public function update($roleId, $role)
    {
        $this->sdoFactory->beginTransaction();

        try {
            $this->sdoFactory->update($role, 'auth/role');

            $this->sdoFactory->deleteChildren("auth/privilege", $role, "auth/role");
            if (!empty($role->privileges)) {
                foreach ($role->privileges as $userStory) {
                    $this->addPrivilege($roleId, $userStory);
                }
            }

            $this->sdoFactory->deleteChildren("auth/roleMember", $role, "auth/role");

            if (!empty($role->roleMembers)) {
                foreach ($role->roleMembers as $member) {
                    \laabs::callService("auth/roleMember/create", $roleId, $member);
                }
            }

        } catch (\Exception $exception) {
            $this->sdoFactory->rollback();
            throw \laabs::newException("auth/adminRoleException", "Role not updated");
        }

        $this->sdoFactory->commit();

        return true;
    }

    /**
     * Lock or unlock a role
     * @param auth/role $roleId The role object to update
     * @param boolean   $status The new status of the role
     *
     * @return boolean The status of the query
     */
    public function changeStatus($roleId, $status)
    {
        $role = $this->sdoFactory->read("auth/role", $roleId);
        if ($status == "true") {
            $role->enabled = 1;
        } else {
            $role->enabled = 0;
        }
        $result = $this->sdoFactory->update($role, 'auth/role');

        return $result;
    }

    /**
     * Delete an auth role
     * @param string $roleId The role identifier to delete
     *
     * @return boolean The status of the query
     */
    public function delete($roleId)
    {
        $res = false;
        $this->sdoFactory->beginTransaction();
        try {
            $role = $this->sdoFactory->read("auth/role", $roleId);
            $this->sdoFactory->deleteChildren("auth/privilege", $role);
            $this->sdoFactory->deleteChildren("auth/roleMember", $role);
            $this->sdoFactory->delete($role);
            $this->sdoFactory->commit();

            $res = true;
        } catch (\Exception $exception) {
            $this->sdoFactory->rollback();

            throw \laabs::newException("auth/adminRoleException", "Role not deleted");
        }

        return $res;
    }

    /**
     * Get the list of available authorization groups
     * @param string $query A query string of tokens
     *
     * @return array The list of groups
     */
    public function queryRoles($query)
    {
        $queryTokens = \laabs\explode(" ", $query);
        $queryTokens = array_unique($queryTokens);

        $queryProperties = array("roleName");
        $queryPredicats = array();
        foreach ($queryProperties as $queryProperty) {
            foreach ($queryTokens as $queryToken) {
                $queryPredicats[] = $queryProperty."="."'*".$queryToken."*'";
            }
        }
        $queryString = implode(" OR ", $queryPredicats);

        $result = $this->sdoFactory->find('auth/role', $queryString);

        return $result;
    }

    /**
     * Get the list of user story
     * @param string $roleId The identifier of the role
     *
     * @return array The list of privileges
     */
    public function getPrivilege($roleId)
    {
        $role = $this->sdoFactory->read("auth/role", $roleId);
        $userStories = array();
        $privileges = $this->sdoFactory->readChildren("auth/privilege", $role);
        foreach ($privileges as $privilege) {
            $userStories[] = $privilege->userStory;
        }

        return $userStories;
    }

    /**
     * Create privileges
     * @param id     $roleId    The roel to add privilege
     * @param string $userStory Privilege userStory
     *
     * @return boolean The operation result
     */
    public function addPrivilege($roleId, $userStory)
    {
        $privilege = \laabs::newInstance("auth/privilege");
        $privilege->userStory = $userStory;
        $privilege->roleId = $roleId;

        return $this->sdoFactory->create($privilege);
    }

    /**
     * Delete privileges
     * @param auth/privilege $privilege Privilege object
     *
     * @throws Exception
     *
     * @return boolean The result of the operation
     */
    public function deletePrivilege($privilege)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        $privilege = \laabs::castObject($privilege, "auth/privilege");

        try {
            $this->sdoFactory->delete($privilege);
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }
            throw \laabs::newException("auth/sdoException");
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }
}
