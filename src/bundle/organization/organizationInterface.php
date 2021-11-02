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
 * Interface for organiaztion administration
 */
interface organizationInterface
{

    /**
     * Index of organizations
     *
     * @return organization/organization[] An array of organization
     *
     * @action organization/organization/index
     *
     */
    public function readIndex($limit = null, $query = null);

    /**
     * Tree of organizations
     *
     * @return organization/organizationTree[] An array of organization
     *
     * @action organization/organization/getTree
     */
    public function readTree();

    /**
     * Search organization
     * @param string $term
     * @param string $enabled
     *
     * @return organization/organization[] An array of organizations
     *
     * @action organization/organization/search
     */
    public function readSearch($term = null, $enabled = "all");

    /**
     * Create an organization
     * @param organization/organization $organization The organization object to create
     *
     * @return string the new organization's Id
     *
     * @action organization/organization/create
     * @example /public/tests/organizationCreate-max.json example-max
     * @example /public/tests/organizationCreate-min.json example-min
     */
    public function create($organization);

    /**
     * Get an organization by his regitration number
     *
     * @return organization/organization the organization
     *
     * @action organization/organization/getOrgByRegNumber
     */
    public function readByregnumber($registrationNumber);

    /**
     * Get organizations by role
     *
     * @return organization/organization[] the organizations
     *
     * @action organization/organization/getOrgsByRole
     */
    public function readByrole_role_();

    /**
     * List organisations
     * @param string $role The role of organizations
     *
     * @return array The organizations list
     *
     * @action organization/organization/orgList
     */
    public function readOrgList($role = null);

    /**
     * List organisations units
     * @param string $role The role of organizations
     *
     * @return array The organizations list
     *
     * @action organization/organization/orgUnitList
     */
    public function readOrgunitList($role = null);

    /**
     * Get organization's user positions
     *
     * @return organization/userPositionTree[] The list of user position
     *
     * @action organization/organization/readUserPositions
     */
    public function readUserpositions_orgId_();

    /**
     * Add a user position to an organization
     * @param string $function      The function of the user
     *
     * @return boolean The result of the operation
     *
     * @action organization/organization/addUserPosition
     */
    public function createUserposition_orgId__userAccountId_($function = null);

    /**
     * Add a service position to an organization
     *
     * @return boolean The result of the operation
     *
     * @action organization/organization/addServicePosition
     */
    public function createServiceposition_orgId__serviceAccountId_();

    /**
     * Set default user position for an user
     * @param string $orgId         The organization identifier
     * @param string $userAccountId The service account identifier
     *
     * @return boolean The result of the operation
     *
     * @action organization/organization/setDefaultUserPosition
     */
    public function updateSetdefaultposition_orgId__userAccountId_();

    /**
     * Add a user position to an organization
     *
     * @return boolean The result of the operation
     *
     * @action organization/organization/deleteUserPosition
     */
    public function deleteUserposition_orgId__userAccountId_();

    /**
     * Delete a service position to an organization
     *
     * @return boolean The result of the operation
     *
     * @action organization/organization/deleteServicePosition
     */
    public function deleteServiceposition_orgId__serviceAccountId_();

    /**
     * Delete a contact position to an organization
     *
     * @return boolean The result of the operation
     *
     * @action organization/organization/deleteContactPosition
     */
    public function deleteContactposition_orgId__contactId_();

    /**
     * Get organization's service positions
     *
     * @return organization/servicePosition[] The list of service position
     *
     * @action organization/organization/readServicepositions
     */
    public function readServicepositions_orgId_();

    /**
     * Update an organization
     * @param organization/organization $organization The organization object to update
     *
     * @return boolean The result of the operation
     *
     * @action organization/organization/update
     */
    public function update_orgId_($organization);

    /**
     * Move an organization to a new ownerOrg
     * @param string $newParentOrgId The new parent organization identifier
     * @param string $newOwnerOrgId  The new owner organization identifier
     *
     * @return boolean The result of the operation
     *
     * @action organization/organization/move
     */
    public function updateMove_orgId_($newParentOrgId = null, $newOwnerOrgId = null);

    /**
     * Delete an organization
     *
     * @return boolean The result of the operation
     *
     * @action organization/organization/delete
     */
    public function delete_orgId_();

    /**
     * Get an organization addresses
     *
     * @return contact/address[]
     *
     * @action organization/organization/getAddresses
     */
    public function read_orgId_Addresses();

    /**
     * Get an organization communications
     *
     * @return contact/communication[]
     *
     * @action organization/organization/getCommunications
     */
    public function read_orgId_Communications();

    /**
     * Get an organization contacts
     *
     * @return contact/contact[]
     *
     * @action organization/organization/getContacts
     */
    public function read_orgId_Contacts();

    /**
     * Add an organization contact
     * @param object $contact
     * @param string $isSelf
     *
     * @return bool
     *
     * @action organization/organization/addContact
     */
    public function create_orgId_Contact($contact, $isSelf);

    /**
     * Create an organization archival profile access
     *
     * @param organization/archivalProfileAccess $archivalProfileAccess
     *
     * @action organization/organization/createArchivalprofileaccess
     *
     * @return  organization/archivalProfileAccess
     */
    public function createArchivalprofileaccess($archivalProfileAccess);

    /**
     * Add an organization archival profile access
     *
     * @param organization/archivalProfileAccess $archivalProfileAccess
     *
     * @action organization/organization/updateArchivalprofileaccess
     *
     * @return organization/archivalProfileAccess
     */
    public function updateArchivalprofileaccess($archivalProfileAccess);

    /**
     * Add an organization archival profile access
     *
     * @param string $orgId                    id of organization of archivalProfileAccess to delete
     * @param string $archivalProfileReference archival profile reference of archivalProfileAccess
     *
     * @action organization/organization/deleteArchivalProfileAccess
     *
     * @return organization/archivalProfileAccess
     */
    public function deleteArchivalprofileaccess($orgId, $archivalProfileReference);

    /**
     * Get the profiles by orgRegNumber
     *
     * @param string $orgRegNumber
     * @param string $originatorAccess
     *
     * @return array
     * @action organization/organization/getOrgUnitArchivalProfiles
     */
    public function readOrgunitprofiles($orgRegNumber, $originatorAccess=null);

    /**
     * Get the user postions by accountId
     *
     * @return array
     * @action organization/organization/readUserOrgs
     */
    public function readAccountpositions_accountId_();

    /**
     * List of organizations
     *
     * @param bool $ownerOrg
     * @param bool $orgUnit
     *
     * @return array An array of organization and service
     *
     * @action organization/organization/todisplay
     */
    public function readTodisplay($ownerOrg = false, $orgUnit = false, $term = "");

    /**
     * Get originator
     *
     * @action organization/organization/getOriginator
     */
    public function readOriginator();

    /**
     * Create a csv file
     *
     * @param  integer $limit Max number of results to display
     *
     * @action organization/organization/exportCsv
     *
     */
    public function readExport($limit = null);

    /**
     * Read an organization by his orgId
     *
     * @return organization/organization the organization
     *
     * @action organization/organization/read
     */
    public function read_orgId_();

    /**
     * Change status of organization
     *
     * @action organization/organization/changeStatus
     */
    public function read_orgId_ChangeStatus_status_();

    /**
     * @param resource  $data     Data base64 encoded or not
     * @param boolean   $isReset  Reset tables or not
     *
     * @action organization/organization/import
     *
     * @return boolean        Import with reset of table data or not
     */
    public function createImport($data, $isReset);

    /**
     * List descendant services and org from a parent org
     *
     * @param  string $orgId Organization Identifier
     *
     * @action organization/organization/readDescendantServices
     *
     * @return array         Array of descendant services
     */
    public function readDescendantServices($orgId);
}
