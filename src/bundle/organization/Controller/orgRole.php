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
namespace bundle\organization\Controller;

/**
 * Control of the organization roles
 *
 * @package Organization
 * @author  Alexandre Morin <alexandre.morin@maarch.org> 
 */
class orgRole
{
    protected $sdoFactory;

    /**
     * Constructor
     * @param object $orgModel The model for organization
     *
     * @return void
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get the organization's role
     *
     * @return array The list of organization's roles
     */
    public function index()
    {
        return $this->sdoFactory->find('organization/orgRole');
    }

    /**
     * Create a new organization role object
     *
     * @return bundle\organization\Model\orgRole The orgRole object
     */
    public function newOrgRole()
    {
        return \laabs::newInstance('organization/orgRole');
    }

    /**
     * Add an organization role
     * @param object $orgRole the orgRole to add
     *
     * @return bool
     */
    public function addOrgRole($orgRole)
    {
        if ($this->sdoFactory->exists('organization/orgRole', $orgRole->code)) {
            throw \laabs::Bundle('organization')->newException('orgRoleException', 'The organization role code already exists.');
        }

        return $this->sdoFactory->create($orgRole, 'organization/orgRole');
    }

    /**
     * Edit an organization role
     * @param string $orgRoleId The id of the orgRole to edit
     *
     * @return object The orgRole object to add
     */
    public function editOrgRole($orgRoleId)
    {
        return $this->sdoFactory->read('organization/orgRole', $orgRoleId);
    }

    /**
     * Update an organization role
     * @param object $orgRole The orgRole to update
     *
     * @return bool
     */
    public function modifyOrgRole($orgRole)
    {
        return $this->sdoFactory->update($orgRole, 'organization/orgRole');
    }
    
}