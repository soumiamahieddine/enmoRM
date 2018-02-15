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
 * Control of the organization contacts
 *
 * @package Organization
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class orgContact
{
    protected $sdoFactory;

    /**
     * Constructor
     * @param object $sdoFactory The model for organization
     *
     * @return void
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Create a organization contact
     * @param organization/contact $orgContact The organization contact to record
     *
     * @return organization/orgContact The organization contact
     */
    public function create($orgContact)
    {
        $this->sdoFactory->create($orgContact, "organization/orgContact");

        return $orgContact;
    }

    /**
     * Get all organization contact by organization identifier
     * @param id $orgId The organization identifier
     *
     * @return organization/orgContact[] List of organization contact
     */
    public function getbyOrg($orgId)
    {
        $orgContacts = $this->sdoFactory->find("organization/contact", "orgId=:id", array("id"=>$orgId));

        $orgContacts = \laabs::castMessageCollection($orgContacts, "organization/orgContact");

        return $orgContacts;
    }

    /**
     * Get all organization contact by contact identifier
     * @param id $contactId The contact identifier
     *
     * @return organization/orgContact[] List of organization contact identified
     */
    public function getbyContact($contactId)
    {
        $orgContacts = $this->sdoFactory->find("organization/contact", "contactId=:id", array("id"=>$contactId));

        $orgContacts = \laabs::castMessageCollection($orgContacts, "organization/orgContact");

        return $orgContacts;
    }

    /**
     * Delete an organization contact
     * @param organization/contact $orgContact The organization contact to delete
     *
     * @return bool The result of the operation
     */
    public function delete($orgContact)
    {
        $this->sdoFactory->delete($orgContact, "organization/orgContact");

        return true;
    }

}
