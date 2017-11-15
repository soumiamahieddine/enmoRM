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
use core\Exception;

/**
 * Control of the organization
 *
 * @package Organization
 * @author  Prosper De Laure <prosper.delaure@maarch.org> 
 */
class organization
{
    protected $sdoFactory;

    /**
     * Constructor
     * @param object $sdoFactory The dependency sdo factory service
     *
     * @return void
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Index of organizations
     * @param string $query The query of the index
     *
     * @return array An array of organization
     */
    public function index($query = null)
    {
        return $this->sdoFactory->index("organization/organization", array("orgId", "displayName", "isOrgUnit", "registrationNumber", "ownerOrgId"), $query);
    }

    /**
     * List of organizations
     * @param string $query The query of the index
     *
     * @return array An array of organization whith service
     */
    public function todisplay($query = null)
    {
        $organizations = $this->sdoFactory->index("organization/organization", array("orgId", "displayName", "isOrgUnit", "registrationNumber", "ownerOrgId"), $query);
        $orgList = [];

        foreach ($organizations as $org) {
            if($org->ownerOrgId != null) {
                $orgList[] = $org;
            }
        }
        foreach ($orgList as $org) {

            foreach ($organizations as $organization) {
                if($organization->orgId == $org->ownerOrgId) {
                    $org->ownerOrgName =  $organization->displayName;
                }
            }
        }

        return $orgList;
    }

    /**
     * Get organizations tree
     *
     * @return organization/organizationTree[] The tree of organization
     */
    public function getTree()
    {
        $tree = array();

        $currentOrg = \laabs::getToken("ORGANIZATION");
        $owner = true;

        if (isset($currentOrg)) {
            if (!$currentOrg->orgRoleCodes) {
                $owner = false;
            } else if (is_array($currentOrg->orgRoleCodes) && !in_array('owner', $currentOrg->orgRoleCodes)) {
                $owner = false;
            }
        }

        $organizationList = $this->sdoFactory->find("organization/organization", null, null, 'orgName');
        $organizationList = \laabs::castMessageCollection($organizationList, "organization/organizationTree");

        // sort organization by parentOrgId
        $organizationByParent = array();
        foreach ($organizationList as $organization) {
            $parentOrgId = (string) $organization->parentOrgId;


            if (empty($parentOrgId) && $owner) {
                $tree[] = $organization;
                continue;
            } elseif (!$owner && (string) $organization->orgId == (string) $currentOrg->ownerOrgId) {
                $tree[] = $organization;
                continue;
            }

            if (!isset($organizationByParent[$parentOrgId])) {
                $organizationByParent[$parentOrgId] = array();
            }
            $organizationByParent[$parentOrgId][] = $organization;
        }

        return $this->buildTree($tree, $organizationByParent);
    }

    /**
     * Set positions for the organization tree
     * @param object $roots            The organization tree roots
     * @param array  $organizationList The list of organization sorted by parent organization
     * 
     * @return array
     */
    protected function buildTree($roots, $organizationList)
    {

        foreach ($roots as $organization) {
            $orgId = (string) $organization->orgId;

            if (isset($organizationList[$orgId])) {
                $organization->organization = $this->buildTree($organizationList[$orgId], $organizationList);
            }
        }

        return $roots;
    }

    /**
     * Search organizations
     * @param string $name
     * @param string $businessType
     * @param string $orgRoleCode
     * @param string $orgTypeCode
     * @param string $registrationNumber
     * @param string $taxIdentifier
     *
     * @return organization/organization[] An array of organizations
     */
    public function search($name = null, $businessType = null, $orgRoleCode = null, $orgTypeCode = null, $registrationNumber = null, $taxIdentifier = null)
    {
        $queryParts = array();
        $variables = array();
        $query = null;


        if ($name) {
            $variables['name'] = "*$name*";
            $queryParts[] = "(orgName~:name OR otherOrgName~:name OR displayName~:name)";
        }

        if ($businessType) {
            $variables['businessType'] = "*$businessType*";
            $queryParts[] = "(businessType~:businessType)";
        }

        if ($orgRoleCode) {
            $variables['orgRoleCodes'] = "*$orgRoleCode*";
            $queryParts[] = "(orgRoleCodes~:orgRoleCodes)";
        }

        if ($orgTypeCode) {
            $variables['orgTypeCode'] = "*$orgTypeCode*";
            $queryParts[] = "(orgTypeCode='$orgTypeCode')";
        }

        if ($registrationNumber) {
            $variables['registrationNumber'] = $registrationNumber;
            $queryParts[] = "(registrationNumber='$registrationNumber')";
        }
        if ($taxIdentifier) {
            $variables['taxIdentifier'] = $taxIdentifier;
            $queryParts[] = "(taxIdentifier='$taxIdentifier')";
        }

        if (count($queryParts)) {
            $query = array(implode(" AND ", $queryParts), $variables);
        }

        return $this->sdoFactory->find("organization/organization", $query);
    }

    /**
     * Create an organization
     * @param organization/organization $organization The organization object to create
     *
     * @return string the new organization's Id
     */
    public function create($organization)
    {
        try {
            if (empty($organization->displayName)) {
                $organization->displayName = $organization->orgName;
            }

            $organization->orgId = \laabs::newId();
            $this->sdoFactory->create($organization, 'organization/organization');
        } catch (\Exception $e) {
            throw new \core\Exception("Key already exists");

        }

        return $organization->orgId;
    }

    /**
     * Read an organization by his orgId
     * @param string $orgId The Identifier of the organization to read
     *
     * @return organization/organization the organization
     */
    public function read($orgId)
    {
        $organization = $this->sdoFactory->read("organization/organization", $orgId);
        $organization->users = $this->readUserPositions($orgId);
        $organization->services = $this->readServicePositions($orgId);
        $organization->contacts = $this->getContacts($orgId);
        $organization->archivalProfileAccess = $this->sdoFactory->find('organization/archivalProfileAccess', "orgId='$orgId'");

        return \laabs::castMessage($organization, "organization/organization");
    }

    /**
     * Get an organization by his regitration number
     * @param string $registrationNumber The registration number of the organization
     *
     * @return organization/organization the organization
     */
    public function getOrgByRegNumber($registrationNumber)
    {
        $organization = $this->sdoFactory->read("organization/organization", array('registrationNumber' => $registrationNumber));

        return \laabs::castMessage($organization, "organization/organization");
    }

    /**
     * Get organizations by their role
     * @param string $role The role of the organizations
     *
     * @return organization/organization[] the organizations
     */
    public function getOrgsByRole($role)
    {
        $organizations =  $this->sdoFactory->find("organization/organization", "orgRoleCodes = '*$role*'");

        return \laabs::castMessageCollection($organizations, "organization/organization");
    }

    /**
     * List organisations
     * @param string $role The role of organizations
     *
     * @return array The organizations list
     */
    public function orgList($role = null)
    {
        $queryString = "";

        if ($role) {
            $queryString .= "orgRoleCodes = '*$role*'";
        }

        return $this->sdoFactory->index("organization/organization", array("displayName", "registrationNumber"), $queryString, null, array('registrationNumber'));
    }

     /**
     * List organisations
     * @param string $role The role of organizations
     *
     * @return array The organizations list
     */
    public function orgUnitList($role = null)
    {
        $queryString = "";

        if ($role) {
            $queryString .= "orgRoleCodes = '*$role*'";
        }

        $queryString .= "isOrgUnit = 'true'";

        return $this->sdoFactory->index("organization/organization", array("displayName", "registrationNumber"), $queryString, null, array('registrationNumber'));
    }

    /**
     * Get organization's user positions
     * @param string $orgId The organization's identifier
     *
     * @return organization/userPositionTree[] The list of user position
     */
    public function readUserPositions($orgId)
    {
        $users = $this->sdoFactory->find("organization/userPosition", "orgId = '$orgId'");
        $users = \laabs::castMessageCollection($users, 'organization/userPositionTree');

        $userAccountController = \laabs::newController('auth/userAccount');

        foreach ($users as $user) {
            $user->displayName = $userAccountController->edit((string) $user->userAccountId)->displayName;
        }

        return $users;
    }

    /**
     * Get user positions
     * @param string $accountId The user identifier
     *
     * @return organization/userPositionTree[] The list of user position
     */
    public function readUserOrgs($accountId)
    {
        $users = $this->sdoFactory->find("organization/userPosition", "userAccountId = '$accountId'");
        $users = \laabs::castMessageCollection($users, 'organization/userPositionTree');
        $organizations = $this->sdoFactory->find("organization/organization");

        foreach ($users as $user) {
            $user->displayName = $this->sdoFactory->read("organization/organization", $user->orgId)->displayName;

            foreach ($organizations as $org) {
                if($user->orgId == $org->orgId) {
                    foreach ($organizations as $orgName) {
                        if($org->ownerOrgId == $orgName->orgId) {
                            $user->ownerOrgName = $orgName->displayName;
                        }
                    }
                }
            }
        }

        return $users;
    }

    /**
     * Get organization's service positions
     * @param string $orgId The organization's identifier
     *
     * @return organization/servicePosition[] The list of service position
     */
    public function readServicePositions($orgId)
    {
        $services = $this->sdoFactory->find("organization/servicePosition", "orgId = '$orgId'");
        $services = \laabs::castMessageCollection($services, 'organization/servicePositionTree');

        $accountController = \laabs::newController('auth/userAccount');

        foreach ($services as $service) {
            $service->displayName = $accountController->edit((string) $service->serviceAccountId)->displayName;
        }

        return $services;
    }

    /**
     * Get organization's contacts
     * @param string $orgId The organization's identifier
     *
     * @return organization/orgContact[] The list of service position
     */
    public function readContact($orgId)
    {
        return $this->sdoFactory->find("organization/orgContact", "orgId = '$orgId'");
    }

    /**
     * Update an organization
     * @param string                    $orgId        The organization identifier
     * @param organization/organization $organization The organization object to update
     *
     * @return boolean The result of the operation
     */
    public function update($orgId, $organization)
    {
        $organization->orgId = $orgId;

        if ($organization->beginDate>$organization->endDate) {
            throw new \core\Exception("The end date is lower than the begin date.");
        }


        if (isset($organization->orgRoleCodes) && $organization->orgRoleCodes->contains("owner")) {
            if($this->sdoFactory->count("organization/organization", "registrationNumber!='$organization->registrationNumber' AND orgRoleCodes='*owner*'")) {
                throw new \core\Exception("An owner is already defined.");
            }
        }

        try {
            if ($this->isUsed($organization->registrationNumber)) {
                $originalOrganization = $this->read($orgId);
                $organization->registrationNumber = $originalOrganization->registrationNumber;
            }

            if (empty($organization->displayName)) {
                $organization->displayName = $organization->orgName;
            }
            $res = $this->sdoFactory->update($organization, 'organization/organization');
        } catch (\Exception $e) {
            throw new \core\Exception("Key already exists");
        }

        return $res;
    }

    /**
     * Move an organization to a new ownerOrg
     * @param string $orgId          The organization identifier
     * @param string $newParentOrgId The new parent organization identifier
     * @param string $newOwnerOrgId  The new owner organization identifier
     *
     * @return boolean The result of the operation
     */
    public function move($orgId, $newParentOrgId = null, $newOwnerOrgId = null)
    {
        if ($newParentOrgId == "") {
            $newParentOrgId = null;
        }
        if ($newOwnerOrgId == "") {
            $newOwnerOrgId = null;
        }

        $organization = $this->sdoFactory->read("organization/organization", $orgId);
        $descendants = $this->sdoFactory->readDescendants("organization/organization", $organization);

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $organization->parentOrgId = $newParentOrgId;
            $organization->ownerOrgId = $newOwnerOrgId;

            if (!$newOwnerOrgId) {
                $newOwnerOrgId = $orgId;
            }

            foreach ($descendants as $descendantOrg) {
                if ((string) $descendantOrg->orgId == $organization->orgId) {
                    throw new \bunble\organization\Exception\orgMoveException("Organization can't be moved to a descendent organization");
                }

                if ($descendantOrg->isOrgUnit && $descendantOrg->ownerOrgId != $newOwnerOrgId) {
                    $descendantOrg->ownerOrgId = $newOwnerOrgId;
                    $this->sdoFactory->update($descendantOrg, 'organization/organization');
                }
            }

            $this->sdoFactory->update($organization);
        } catch (\Exception $e) {
            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }

    /**
     * Delete an organization
     * @param string $orgId The organization id
     *
     * @return boolean The restult of the operation
     */
    public function delete($orgId)
    {
        $organization = $this->sdoFactory->read("organization/organization", $orgId);
        
        if ($this->isUsed($organization->registrationNumber)) {
            throw new \core\Exception\ForbiddenException("The organization %s is used in archives.", 403, null, [$organization->registrationNumber]);
        }

        $children = $this->sdoFactory->readChildren("organization/organization", $organization);
        $users = $this->sdoFactory->readChildren("organization/userPosition", $organization);
        $services = $this->sdoFactory->readChildren("organization/servicePosition", $organization);
        $contacts = $this->sdoFactory->readChildren("organization/orgContact", $organization);
        $archivalProfilesAccess = $this->sdoFactory->readChildren("organization/archivalProfileAccess", $organization);
        $this->sdoFactory->deleteChildren("organization/archivalProfileAccess", $organization);

        foreach ($children as $child) {
            $this->delete($child);
        }
        foreach ($users as $user) {
            $result = $this->sdoFactory->delete($user);
        }
        foreach ($services as $service) {
            $result = $this->sdoFactory->delete($service);
        }
        foreach ($contacts as $contact) {
            $result = $this->deleteContactPosition($contact->contactId, (string) $organization->orgId);
        }

        $result = $this->sdoFactory->delete($organization);

        return $result;
    }

    /**
     * Add a user position to an organization
     * @param string $userAccountId The user account identifier
     * @param string $orgId         The organization identifier
     * @param string $function      The function of the user
     *
     * @return boolean The result of the operation
     */
    public function addUserPosition($userAccountId, $orgId, $function = null)
    {
        $userPosition = \laabs::newInstance('organization/userPosition');
        $userPosition->userAccountId = $userAccountId;
        $userPosition->orgId = $orgId;
        $userPosition->function = $function;
        $userPosition->default = false;
        $userDefaultPosition = $this->sdoFactory->find('organization/userPosition', "userAccountId = '$userAccountId' AND default = true");

        if (empty($userDefaultPosition)) {
            $userPosition->default = true;
        }
        
        return $this->sdoFactory->create($userPosition, 'organization/userPosition');
    }

    /**
     * Delete an archival pofile accesss from every organization
     * @param array  $archivalProfileReference The archival profile reference
     *
     * @return bool The result of the operation
     */
    public function updateUserPosition($userAccountId, $orgId = null)
    {
        $userPositions = $this->sdoFactory->find("organization/userPosition", "userAccountId='$userAccountId'");
        $default = null;

        foreach ($userPositions as $userPosition) {
            if ($userPosition->default) {
                $default = $userPosition->orgId;
            }
        }

        try {

            if($userPositions) {
                $this->sdoFactory->deleteCollection($userPositions, "organization/userPosition");
            }

            foreach ($orgId as $id){
                $userPosition = \laabs::newInstance('organization/userPosition');
                $userPosition->userAccountId = $userAccountId;
                $userPosition->orgId = $id;

                if($id== $default){
                    $userPosition->default = true;
                }
                else {
                    $userPosition->default = false;
                }

                $userPosition=$this->sdoFactory->create($userPosition, 'organization/userPosition');
            }
        } catch (Exception $e) {
            $this->sdoFactory->rollback();
            throw $e;
        }

        return $userPosition;
    }

    /**
     * Add a service position to an organization
     * @param string $orgId            The organization identifier
     * @param string $serviceAccountId The service account identifier
     *
     * @return boolean The result of the operation
     */
    public function addServicePosition($orgId, $serviceAccountId)
    {
        $servicePosition = \laabs::newInstance('organization/servicePosition');
        $servicePosition->serviceAccountId = $serviceAccountId;
        $servicePosition->orgId = $orgId;

        return $this->sdoFactory->create($servicePosition);
    }

    /**
     * Set default user position for an user
     * @param string $orgId         The organization identifier
     * @param string $userAccountId The service account identifier
     *
     * @return boolean The result of the operation
     */
    public function setDefaultUserPosition($orgId, $userAccountId)
    {
        $previousDefaultUserPosition = $this->sdoFactory->find('organization/userPosition', "userAccountId='$userAccountId' AND default=true");

        if (!empty($previousDefaultUserPosition)) {
            $previousDefaultUserPosition = $previousDefaultUserPosition[0];
            $previousDefaultUserPosition->default = false;
            $this->sdoFactory->update($previousDefaultUserPosition, 'organization/userPosition');
        }

        $userPosition = $this->sdoFactory->read("organization/userPosition", array("userAccountId" => $userAccountId, "orgId" => $orgId));
        $userPosition->default = true;

        return $this->sdoFactory->update($userPosition, 'organization/userPosition');
    }

    /**
     * Remove a user's position
     * @param string $userAccountId The user account identifier
     * @param string $orgId         The organization account identifier
     *
     * @return boolean
     */
    public function deleteUserPosition($userAccountId, $orgId)
    {
        $userPosition = $this->sdoFactory->read("organization/userPosition", array("userAccountId" => $userAccountId, "orgId" => $orgId));

        if ($userPosition->default) {
            $newDefaultUserPosition = $this->sdoFactory->find('organization/userPosition', "userAccountId='$userAccountId'");

            if (!empty($newDefaultUserPosition)) {
                $newDefaultUserPosition = $newDefaultUserPosition[0];
                $newDefaultUserPosition->default = true;

                $this->sdoFactory->update($newDefaultUserPosition, 'organization/userPosition');
            }
        }

        return $this->sdoFactory->delete($userPosition);
    }

    /**
     * Remove a contact's position
     * @param string $contactId The user account identifier
     * @param string $orgId     The organization account identifier
     *
     * @return boolean
     */
    public function deleteContactPosition($contactId, $orgId)
    {
        $contactPosition = $this->sdoFactory->read("organization/orgContact", array("contactId" => $contactId, "orgId" => $orgId));

        $contactController = \laabs::newController('contact/contact');
        $contactController->delete($contactId);

        return $this->sdoFactory->delete($contactPosition);
    }

    /**
     * Remove a service's position
     * @param string $orgId            The organization account identifier
     * @param string $serviceAccountId The service account identifier
     *
     * @return boolean
     */
    public function deleteServicePosition($orgId, $serviceAccountId)
    {
        $servicePosition = $this->sdoFactory->read("organization/servicePosition", array("serviceAccountId" => $serviceAccountId, "orgId" => $orgId));

        return $this->sdoFactory->delete($servicePosition);
    }

    /**
     * Get organization contacts
     * @param id $orgId
     *
     * @return contact/contact[]
     */
    public function getContacts($orgId)
    {
        $orgContacts = $this->sdoFactory->find('organization/orgContact', "orgId='$orgId'");

        $contacts = array();
        foreach ($orgContacts as $orgContact) {
            try {
                $contact = \laabs::callService('contact/contact/read_contactId_', $orgContact->contactId);
                $contacts[] = (object) array_merge((array) $contact, (array) $orgContact);
            } catch (\Exception $e) {

            }
        }

        return $contacts;
    }

    /**
     * Bind a contact to an org
     * @param id     $orgId
     * @param object $contact
     * @param bool   $isSelf
     *
     * @return bool
     */
    public function addContact($orgId, $contact, $isSelf = false)
    {
        $contactController = \laabs::newController('contact/contact');

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $contactId = $contactController->add($contact);

            $orgContact = \laabs::newInstance('organization/orgContact');
            $orgContact->orgId = $orgId;
            $orgContact->contactId = $contactId;
            $orgContact->isSelf = (bool) $isSelf;

            $this->sdoFactory->create($orgContact);

        } catch (\Exception $e) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }
            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }

    /**
     * Get organization adresses
     * @param id $orgId
     *
     * @return contact/address[]
     */
    public function getAddresses($orgId)
    {
        $orgContacts = $this->sdoFactory->find('organization/orgContact', "orgId='$orgId' and isSelf=true");
        if (count($orgContacts) == 0) {
            return array();
        }

        $address = null;

        try {
            $contact = \laabs::callService('contact/contact/read_contactId_', $orgContacts[0]->contactId);
            $address = $contact->address;

        } catch (\Exception $e) {


        }

        return $address;
    }

    /**
     * Get organization communications
     * @param id $orgId
     *
     * @return contact/communication[]
     */
    public function getCommunications($orgId)
    {
        $orgContacts = $this->sdoFactory->find('organization/orgContact', "orgId='$orgId' and isSelf=true");
        if (count($orgContacts) == 0) {
            return array();
        }

        $contact = \laabs::callService('contact/contact/read_contactId_', (string) $orgContacts[0]->contactId);

        return $contact->communication;
    }
    
    /**
     * Read parent orgs recursively
     * @param string $orgId Organisation identifier
     *
     * @return array The list of organization
     */
    public function readParentOrg($orgId)
    {
        $parentsOrg = array();
        $org = $this->sdoFactory->read('organization/organization', $orgId);

        $i = false;
        while ($i == false) {
            if ($org->parentOrgId) {
                $org = $this->sdoFactory->find('organization/organization', "orgId = '$org->parentOrgId'")[0];
                $parentsOrg[] = $org;
            } else {
                $i = true;
            }
        }
        
        return $parentsOrg;
    }

    /**
     * Read parent orgs recursively
     * @param string $orgId                 Organisation identifier
     * @param array  $archivalProfileAccess The archival profile access array
     *
     * @return bool The result of the operation
     */
    public function updateArchivalProfileAccess($orgId, $archivalProfileAccess)
    {
        $this->sdoFactory->deleteChildren("organization/archivalProfileAccess", array("orgId" => $orgId), 'organization/organization');

        $org = $this->sdoFactory->read('organization/organization', $orgId);
        
        try {
            if (!$org->isOrgUnit) {
                throw new \core\Exception("Organization Archival Profile Access can't be update ");
            }

        } catch (\Exception $e) {
            throw $e;
        }

        foreach ($archivalProfileAccess as $access) {
            $access = (object) $access;
            $access->orgId = $orgId;

            $this->sdoFactory->create($access, "organization/archivalProfileAccess");
        }

        return true;
    }

    /**
     * Delete an archival pofile accesss from every organization
     * @param array  $archivalProfileReference The archival profile reference
     *
     * @return bool The result of the operation
     */
    public function deleteArchivalProfileAccess($archivalProfileReference)
    {
        $archivalProfileAccess = $this->sdoFactory->find("organization/archivalProfileAccess", "archivalProfileReference='$archivalProfileReference'");

        if($archivalProfileAccess) {
            return $this->sdoFactory->deleteCollection($archivalProfileAccess, "organization/archivalProfileAccess");
        }

        return false;
    }

    /**
     * Get the archival profile descriptions for the given org unit
     * @param string $orgRegNumber
     *
     * @return array
     */
    public function getOrgUnitArchivalProfiles($orgRegNumber)
    {
        $orgUnitArchivalProfiles = [];

        $organization = $this->sdoFactory->read("organization/organization", array('registrationNumber' => $orgRegNumber));
        
        $archivalProfileAccesses = $this->sdoFactory->find('organization/archivalProfileAccess', "orgId='".$organization->orgId."'");
        $archivalProfileController = \laabs::newController("recordsManagement/archivalProfile");
        
        foreach ($archivalProfileAccesses as $archivalProfileAccess) {
            if ($archivalProfileAccess->archivalProfileReference == "*") {
                $orgUnitArchivalProfiles[]  ='*';
                continue;
            }
            $orgUnitArchivalProfiles[] = $archivalProfileController->getByReference($archivalProfileAccess->archivalProfileReference, $withRelatedProfile = true);
        }

        return $orgUnitArchivalProfiles;
    }

    /**
     * Get an archival profile access or null value if doesn't exists
     * @param $orgId                    The organization identifier
     * @param $archivalProfileReference The archival profile reference
     *
     * @return The archival profile access object or null value
     */
    public function getOrgUnitArchivalProfile($orgId, $archivalProfileReference)
    {
        $queryString = "orgId=:orgId AND archivalProfileReference=:archivalProfileReference";
        $queryParam = [];
        $queryParam["orgId"] = $orgId;
        $queryParam["archivalProfileReference"] = $archivalProfileReference;

        $archivalProfileAccess = $this->sdoFactory->find('organization/archivalProfileAccess', $queryString, $queryParam);

        if (empty($archivalProfileAccess)) {
            return null;
        }

        return $archivalProfileAccess[0];
    }

    /**
     * Check if profile is in an organization access list
     * @param string $archivalProfileReference
     * @param string $registrationNumber
     * 
     * @return bool the result of the operation
     */
    public function checkProfileInOrgAccess($archivalProfileReference, $registrationNumber)
    {
        $organization = $this->sdoFactory->read("organization/organization", array('registrationNumber' => $registrationNumber));

        if (!$archivalProfileReference) {
            $archivalProfileReference = "*";
        }

        try {
            $this->sdoFactory->read("organization/archivalProfileAccess", array('orgId' => $organization->orgId ,'archivalProfileReference' => $archivalProfileReference));
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Check if the registration number is use to edit it or not
     * @param string $registrationNumber The registration number
     *
     * @return boolean Boolean to define if the registration number is editable
     */
    public function isUsed($registrationNumber)
    {
        $recordsManagementController = \laabs::newController("recordsManagement/archive");
        $count = $recordsManagementController->countByOrg($registrationNumber);

        return $count > 0 ? true : false;
    }
}