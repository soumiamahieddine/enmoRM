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
 * Control of the organization types
 *
 * @package Organization
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org> 
 */
class orgType
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
     * Get the organization's types
     *
     * @return organization/orgType[] The list of organization's types
     */
    public function index()
    {
        return $this->sdoFactory->find('organization/orgType');
    }

    /**
     * Add an organization type
     * @param organization/orgType $orgType the orgType to create
     *
     * @return bool The result of the operation
     */
    public function create($orgType)
    {
        if ($this->sdoFactory->exists('organization/orgType', $orgType->code)) {
            throw \laabs::Bundle('organization')->newException('orgTypeException', 'The organization type code already exists.');
        }

        return $this->sdoFactory->create($orgType, 'organization/orgType');
    }

    /**
     * Edit an organization type
     * @param string $code The id of the orgType to edit
     *
     * @return organization/orgType The orgType object to add
     */
    public function read($code)
    {
        return $this->sdoFactory->read('organization/orgType', $code);
    }

    /**
     * Update an organization type
     * @param string               $code    The orgType code
     * @param organization/orgType $orgType The orgType to update
     *
     * @return bool The result of the operation
     */
    public function update($code, $orgType)
    {
        $orgType->code = $code;

        return $this->sdoFactory->update($orgType, 'organization/orgType');
    }


    /**
     * Delete an organization type
     * @param string $code The id of the orgType to delete
     *
     * @return bool The resumt of the operation
     */
    public function delete($code)
    {
        if(!empty($this->sdoFactory->find('organization/organization', "orgTypeCode='$code'"))){
            throw new \core\Exception( "The organization type is used.");
        }

        $orgType  =  $this->sdoFactory->read('organization/orgType', $code);

        return $this->sdoFactory->delete($orgType);
    }
}