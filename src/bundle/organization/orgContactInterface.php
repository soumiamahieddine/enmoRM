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
 * Interface for user positions
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface orgContactInterface
{

    /**
     * Add contact to the organization
     * @param organization/orgContact $orgContact The organisation contact to record
     *
     * @action organization/orgContact/create
     */
    public function createOrgcontact($orgContact);

    /**
     * Get all organization contacts by organization identifier
     *
     * @action organization/orgContact/getbyOrg
     */
    public function readOrgcontact_orgId_Org();

    /**
     * Get all organization contacts by contact identifier
     *
     * @action organization/orgContact/getbyContact
     */
    public function readOrgcontact_contactId_Contact();

    /**
     * Delete organization contact
     * @param organization/orgContact $orgContact The organisation contact to record
     *
     * @action organization/orgContact/create
     */
    public function deleteOrgcontact($orgContact);

    /**
     * Get countries codes
     *
     * @action organization/orgContact/loadCountriesCodes
     */
    public function readCountriesCodes();
}
