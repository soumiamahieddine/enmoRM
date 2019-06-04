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
     * @param \dependency\sdo\Factory $sdoFactory The dependency sdo factory service
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
     * @return organization/organization[] An array of organization
     */
    public function index($query = null)
    {
        return $this->sdoFactory->index("organization/organization", array("orgId", "displayName", "isOrgUnit", "registrationNumber", "parentOrgId", "ownerOrgId"), $query);
    }

    /**
     * List of organizations
     *
     * @return organization/organization[] An array of organization whith service
     */
    public function todisplay()
    {
        $currentOrg = \laabs::getToken("ORGANIZATION");

        if (!$currentOrg || (!empty($currentOrg->orgRoleCodes) && in_array('owner', $currentOrg->orgRoleCodes))) {
            $orgUnitList = $this->getOwnerOriginatorsOrgs();
        } else {
            $orgUnitList = $this->getOwnerOriginatorsOrgs($currentOrg);
        }

        foreach ($orgUnitList as $org) {
            $organization = \laabs::newInstance('organization/organization');
            $organization->displayName = $org->displayName;
            $organization->orgId = $org->orgId;
            $organization->parentOrgId = $org->parentOrgId;
            $orgList[] = $organization;

            foreach ($org->originators as $orgUnit) {
                if ($org->orgId == $orgUnit->ownerOrgId) {
                    $orgUnit->ownerOrgName = $org->displayName;
                    $orgList[] = $orgUnit;
                }
            }
        }

        if (isset($orgList)) {
            foreach ($orgList as $org) {
                foreach ($orgList as $orgParent) {
                    if (isset($org->parentOrgId)) {
                        if ($org->parentOrgId == $orgParent->orgId) {
                            $org->parentOrgName = $orgParent->displayName;
                        }
                    }
                }
            }

            return $orgList;
        }
    }

    /**
     * List of orgUnit
     *
     * @return object[] An array of orgUnit
     */
    public function todisplayOrgUnit()
    {
        $currentOrg = \laabs::getToken("ORGANIZATION");
        $orgList = [];

        if (isset($currentOrg)) {
            $organizations = $this->getOwnerOriginatorsOrgs($currentOrg);
        } else {
            $owner = $this->getOrgsByRole('owner')[0];
            $organizations = $this->getOwnerOriginatorsOrgs($owner);
        }

        foreach ($organizations as $org) {
            foreach ($org->originators as $orgUnit) {
                if ($org->orgId == $orgUnit->ownerOrgId) {
                    $orgUnit->ownerOrgName = $org->displayName;
                    $orgList[] = $orgUnit;
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

        $organizationList = $this->sdoFactory->find("organization/organization", null, [], 'orgName');
        $organizationList = \laabs::castMessageCollection($organizationList, "organization/organizationTree");

        // sort organization by parentOrgId
        $organizationByParent = array();
        foreach ($organizationList as $organization) {
            $parentOrgId = (string)$organization->parentOrgId;


            if (empty($parentOrgId) && $owner) {
                $tree[] = $organization;
                continue;
            } elseif (!$owner && (string)$organization->orgId == (string)$currentOrg->ownerOrgId) {
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
     * @return object[]
     */
    protected function buildTree($roots, $organizationList)
    {

        foreach ($roots as $organization) {
            $orgId = (string)$organization->orgId;

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
            $query = implode(" AND ", $queryParts);
        }

        return $this->sdoFactory->find("organization/organization", $query, $variables);
    }

    /**
     * Create an organization
     * @param organization/organization $organization The organization object to create
     *
     * @return string the new organization's Id
     */
    public function create($organization)
    {

        $authController = \laabs::newController("auth/userAccount");
        $user = $authController->get(\laabs::getToken('AUTH')->accountId);

        if (!$organization->parentOrgId && !in_array($user->accountName, \laabs::configuration("auth")["adminUsers"])) {
            if (\laabs::getToken("ORGANIZATION")) {
                if (!in_array('owner', \laabs::getToken("ORGANIZATION")->orgRoleCodes)) {
                    throw new \core\Exception("You're not allowed to create an organization");
                }
            } else {
                throw new \core\Exception("You're not allowed to create an organization");
            }
        }

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
        $organizations = $this->sdoFactory->find("organization/organization", "orgRoleCodes = '*$role*'");

        return \laabs::castMessageCollection($organizations, "organization/organization");
    }

    /**
     * List organisations
     * @param string $role The role of organizations
     *
     * @return organization/organization[] The organizations list
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
     * @return organization/organization[] The organizations list
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
            $user->displayName = $userAccountController->edit((string)$user->userAccountId)->displayName;
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
                if ($user->orgId == $org->orgId) {
                    foreach ($organizations as $orgName) {
                        if ($org->ownerOrgId == $orgName->orgId) {
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

        $userAccountController = \laabs::newController('auth/userAccount');

        foreach ($services as $service) {
            $service->displayName = $userAccountController->edit((string)$service->serviceAccountId)->displayName;
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

        if ($organization->beginDate > $organization->endDate) {
            throw new \core\Exception("The end date is lower than the begin date.");
        }


        if (isset($organization->orgRoleCodes) && $organization->orgRoleCodes->contains("owner")) {
            if ($this->sdoFactory->count("organization/organization", "registrationNumber!='$organization->registrationNumber' AND orgRoleCodes='*owner*'")) {
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

        if ($newParentOrgId) {

            $oldParentOrg = $this->readParentOrg($orgId);
            $newParentOrg = $this->readParentOrg($newParentOrgId);

            if (is_array($oldParentOrg)) {
                $oldParentOrg = end($oldParentOrg);
            }

            if (is_array($newParentOrg)) {
                $newParentOrg = end($newParentOrg);
            }

            if ($oldParentOrg->orgId === null || ($oldParentOrg->orgId !== null && $oldParentOrg->orgId != $newParentOrg->orgId && $oldParentOrg->orgId != $newParentOrgId)) {
                throw new \core\Exception("Organization can''t be moved to an other organization");
            }
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
                if ((string)$descendantOrg->orgId == $organization->orgId) {
                    throw new \bundle\organization\Exception\orgMoveException("Organization can't be moved to a descendent organization");
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

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            if ($this->isUsed($organization->registrationNumber)) {
                throw new \core\Exception\ForbiddenException("The organization %s is used in archives.", 403, null, [$organization->registrationNumber]);
            }

            $controlAuthorities = $this->sdoFactory->find('medona/controlAuthority', "originatorOrgUnitId = '$orgId' OR controlAuthorityOrgUnitId = '$orgId'");
            if (count($controlAuthorities) > 0) {
                throw new \core\Exception\ForbiddenException("The organization %s is used in control authority.", 403, null, [$organization->registrationNumber]);
            }
            
            $children = $this->sdoFactory->readChildren("organization/organization", $organization);
            $users = $this->sdoFactory->readChildren("organization/userPosition", $organization);
            $services = $this->sdoFactory->readChildren("organization/servicePosition", $organization);
            $contacts = $this->sdoFactory->readChildren("organization/orgContact", $organization);
            $this->sdoFactory->deleteChildren("organization/archivalProfileAccess", $organization);

            foreach ($contacts as $contact) {
                $this->deleteContactPosition($contact->contactId, (string)$organization->orgId);
            }

            foreach ($children as $child) {
                $controlAuthorities = $this->sdoFactory->find('medona/controlAuthority', "originatorOrgUnitId = '$child->orgId' OR controlAuthorityOrgUnitId = '$child->orgId'");
                if (count($controlAuthorities) > 0) {
                    throw new \core\Exception\ForbiddenException("The child organization is used in control authority.", 403, null);
                }
                $this->delete($child->orgId);
            }

            foreach ($users as $user) {
                $this->sdoFactory->delete($user);
            }
            foreach ($services as $service) {
                $this->sdoFactory->delete($service);
            }

            $this->sdoFactory->delete($organization);
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }



        return true;
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
     * @param string $userAccountId The user account identifier
     * @param string $orgId         The organization identifier
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

            if ($userPositions) {
                $this->sdoFactory->deleteCollection($userPositions, "organization/userPosition");
            }

            foreach ($orgId as $id) {
                $userPosition = \laabs::newInstance('organization/userPosition');
                $userPosition->userAccountId = $userAccountId;
                $userPosition->orgId = $id;

                if ($id == $default) {
                    $userPosition->default = true;
                } else {
                    $userPosition->default = false;
                }

                $userPosition = $this->sdoFactory->create($userPosition, 'organization/userPosition');
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
     * @return boolean The result of the operation
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
     * @return boolean The result of the operation
     */
    public function deleteContactPosition($contactId, $orgId)
    {
        $contactPosition = $this->sdoFactory->read("organization/orgContact", array("contactId" => $contactId, "orgId" => $orgId));

        $contactController = \laabs::newController('contact/contact');
        $this->sdoFactory->delete($contactPosition);

        return $contactController->delete($contactId);
    }

    /**
     * Remove a service's position
     * @param string $orgId            The organization account identifier
     * @param string $serviceAccountId The service account identifier
     *
     * @return boolean The result of the operation
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
     * @return contact/contact[] Array of  contact/contact object
     */
    public function getContacts($orgId)
    {
        $orgContacts = $this->sdoFactory->find('organization/orgContact', "orgId='$orgId'");

        $contacts = array();
        foreach ($orgContacts as $orgContact) {
            try {
                $contact = \laabs::callService('contact/contact/read_contactId_', $orgContact->contactId);
                $contacts[] = (object)array_merge((array)$contact, (array)$orgContact);
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
     * @return bool  The result of the operation
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
            $orgContact->isSelf = (bool)$isSelf;

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
     * @return contact/address[] Array of contact/address object
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
            throw $e;
        }

        return $address;
    }

    /**
     * Get organization communications
     * @param id $orgId
     *
     * @return contact/communication[] Array of contact/communication object
     */
    public function getCommunications($orgId)
    {
        $orgContacts = $this->sdoFactory->find('organization/orgContact', "orgId='$orgId' and isSelf=true");
        if (count($orgContacts) == 0) {
            return array();
        }

        $contact = \laabs::callService('contact/contact/read_contactId_', (string)$orgContacts[0]->contactId);

        return $contact->communication;
    }

    /**
     * Read parent orgs recursively
     * @param string $orgId Organisation identifier
     *
     * @return organization/organization[] The list of organization
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
     * Read descendant orgs recursively
     * @param string $orgId Organisation identifier
     *
     * @return organization/organization[] The list of organization
     */
    public function readDescendantOrg($orgId)
    {
        $org = $this->sdoFactory->read('organization/organization', $orgId);

        $descendantsOrg = [];
        $ids = [$org->orgId];

        $hasDescendants = true;

        while ($hasDescendants) {
            $idsString = \laabs\implode("','", $ids);
            $descendants = $this->sdoFactory->find('organization/organization', "parentOrgId=['$idsString'] AND isOrgUnit=false");

            if (empty($descendants)) {
                $hasDescendants = false;
            } else {
                $hasDescendants = true;
                $ids = [];

                foreach ($descendants as $descendant) {
                    $descendantsOrg[] = $descendant;
                    $ids[] = $descendant->orgId;
                }
            }
        }

        return $descendantsOrg;
    }

    /**
     * Read descendant services of an org
     * @param string $parentId The parent orgId
     *
     * @return object[] The list of services
     */
    public function readDescendantServices($parentId)
    {
        $childrenServices = $this->sdoFactory->find('organization/organization', "parentOrgId = '$parentId' AND isOrgUnit = true");

        foreach ($childrenServices as $childService) {
            $childrenServices = array_merge($this->readDescendantServices($childService->orgId), $childrenServices);
        }

        return $childrenServices;
    }

    /**
     * Create archivalProfileAccess entry
     *
     * @param  organization/archivalProfileAccess  $archivalProfileAccess
     *
     * @return organization/archivalProfileAccess  $archivalProfileAccess
     */
    public function createArchivalprofileaccess($archivalProfileAccess)
    {
        if (null ==! $this->getArchivalProfileAccess($archivalProfileAccess->orgId, $archivalProfileAccess->archivalProfileReference)) {
            throw new \core\Exception("Organization Archival Profile Access already exists.");
        }
        $org = $this->sdoFactory->read('organization/organization', $archivalProfileAccess->orgId);
        try {
            if (!$org->isOrgUnit) {
                throw new \core\Exception("Organization Archival Profile Access can't be update ");
            }
        } catch (\Exception $e) {
            throw $e;
        }
        $archivalProfileController = \laabs::newController("recordsManagement/archivalProfile");

        if ($archivalProfileAccess->archivalProfileReference === '*' && !$archivalProfileAccess->originatorAccess) {
            throw new \core\Exception("Organization Archival Profile Access cannot be created, archival profile without reference must have an originator access");
        }

        if ($archivalProfileAccess->archivalProfileReference !== '*' && !$archivalProfileController->getByReference($archivalProfileAccess->archivalProfileReference)) {
            throw new \core\Exception("Organization Archival Profile Access cannot be created, archival profile does not exists");
        }

        try {
            $archivalProfileAccess = $this->sdoFactory->create($archivalProfileAccess, 'organization/archivalProfileAccess');
        } catch (Exception $e) {
            throw $e;
        }

        return $archivalProfileAccess;
    }

    /**
     * Read parent orgs recursively
     * @param organization/archivalProfileAccess $archivalProfileAccess
     *
     * @return organization/archivalProfileAccess $archivalProfileAccess
     */
    public function updateArchivalprofileaccess($archivalProfileAccess)
    {
        if (!$this->sdoFactory->exists(
            'organization/archivalProfileAccess',
            [
                'orgId' => $archivalProfileAccess->orgId,
                'archivalProfileReference' => $archivalProfileAccess->archivalProfileReference
            ]
        )) {
            throw new \core\Exception("Organization Archival Profile Access can't be update ");
        }

        if ($archivalProfileAccess->archivalProfileReference === '*') {
            if (!$archivalProfileAccess->originatorAccess) {
                throw new \core\Exception("User cannot be associated with archival profile when archival without profiles is selected");
            }
        }

        $this->sdoFactory->update($archivalProfileAccess, "organization/archivalProfileAccess");

        return $archivalProfileAccess;
    }

    /**
     * Delete an archival pofile accesss from every organization
     *
     * @param organization/archivalProfileAccess $archivalProfileAccess
     *
     * @return bool Is archivalProfileAccess deleted
     */
    public function deleteArchivalProfileAccess($orgId, $archivalProfileReference)
    {
        if (!$this->sdoFactory->exists(
            'organization/archivalProfileAccess',
            [
                'orgId' => $orgId,
                'archivalProfileReference' => $archivalProfileReference
            ]
        )) {
            throw new \core\Exception("Organization Archival Profile Access can't be delete");
        }

        $archivalProfileAccess = $this->sdoFactory->read(
            "organization/archivalProfileAccess",
            [
                'orgId' => $orgId,
                'archivalProfileReference' => $archivalProfileReference
            ]
        );

        return $this->sdoFactory->delete($archivalProfileAccess);
    }

    /**
     * Get accesses to an archival profile for a given org and/or a given profile
     * @param string $orgId                    The organizational unit identifier
     * @param string $archivalProfileReference The archival profile reference
     *
     * @return organization/archivalProfileAccess[]
     */
    public function getArchivalProfileAccess($orgId=null, $archivalProfileReference=null)
    {
        $assert = $params = [];
        if (!empty($orgId)) {
            $assert[] = 'orgId=:orgId';
            $params['orgId'] = $orgId;
        }
        if (!empty($archivalProfileReference)) {
            $assert[] = 'archivalProfileReference=:archivalProfileReference';
            $params['archivalProfileReference'] = $archivalProfileReference;
        }

        $accesses = $this->sdoFactory->find('organization/archivalProfileAccess', implode(' AND ', $assert), $params);

        return $accesses;
    }

    /**
     * Get the archival profile descriptions for the given org unit
     * @param string $orgRegNumber
     *
     * @return recordsManagement/archivalProfile[] Array of recordsManagement/archivalProfile object
     */
    public function getOrgUnitArchivalProfiles($orgRegNumber, $originatorAccess = null)
    {
        $orgUnitArchivalProfiles = [];

        $organization = $this->sdoFactory->read("organization/organization", array('registrationNumber' => $orgRegNumber));

        if ($originatorAccess) {
            $archivalProfileAccesses = $this->sdoFactory->find('organization/archivalProfileAccess', "orgId='" . $organization->orgId . "' AND originatorAccess='" . $originatorAccess . "'");
        } else {
            $archivalProfileAccesses = $this->sdoFactory->find('organization/archivalProfileAccess', "orgId='" . $organization->orgId . "'");
        }
        $archivalProfileController = \laabs::newController("recordsManagement/archivalProfile");

        foreach ($archivalProfileAccesses as $archivalProfileAccess) {
            if ($archivalProfileAccess->archivalProfileReference == "*") {
                $orgUnitArchivalProfiles[] = '*';
                continue;
            }
            $orgUnitArchivalProfiles[] = $archivalProfileController->getByReference(
                $archivalProfileAccess->archivalProfileReference,
                true
            );
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
            $this->sdoFactory->read("organization/archivalProfileAccess", array('orgId' => $organization->orgId, 'archivalProfileReference' => $archivalProfileReference));
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
        $archiveController = \laabs::newController("recordsManagement/archive");
        $count = $archiveController->countByOrg($registrationNumber);

        if ($count > 0) {
            return true;
        }

        $messageController = \laabs::newController("medona/message");
        $count = $messageController->countByOrg($registrationNumber);

        if ($count > 0) {
            return true;
        }

        $archivalAgreementController = \laabs::newController("medona/archivalAgreement");
        $count = $archivalAgreementController->countByOrg($registrationNumber);

        return $count > 0 ? true : false;
    }

    protected function getOwnerOriginatorsOrgs($currentService = null)
    {
        $userPositionController = \laabs::newController('organization/userPosition');

        $owner = false;
        $userOrgs = [];
        $userOwnerOrgs = [];

        if (!$currentService) {
            $userPositions = $userPositionController->getMyPositions();

            foreach ($userPositions as $userPosition) {
                $userOrgs[] = $userPosition->organization;
            }
        } else {
            $userOrgs[] = $currentService;
        }

        if ($this->sdoFactory->find('auth/roleMember', "userAccountId = '" . \laabs::getToken('AUTH')->accountId . "'")[0]->roleId == "ADMIN") {
            unset($userOrgs);
            $userOrgs = $this->sdoFactory->find('organization/organization', "isOrgUnit = TRUE");
        }

        foreach ($userOrgs as $userPosition) {
            if (!in_array($userPosition->ownerOrgId, $userOwnerOrgs)) {
                $userOwnerOrgs[] = $userPosition->ownerOrgId;
            }

            if (is_string($userPosition->orgRoleCodes)) {
                $userPosition->orgRoleCodes = \laabs::newTokenList($userPosition->orgRoleCodes);
            }

            if (in_array('owner', (array) $userPosition->orgRoleCodes) !== false) {
                $owner = true;
                break;
            }
        }

        if ($owner) {
            $originators = \laabs::callService('organization/organization/readIndex', 'isOrgUnit=true');
        } else {
            $originators = \laabs::callService('organization/organization/readIndex', "isOrgUnit=true AND ownerOrgId=['" . \laabs\implode("','", $userOwnerOrgs) . "']");
        }

        $ownerOriginatorOrgs = [];

        foreach ($originators as $orgUnit) {
            if (!isset($ownerOriginatorOrgs[(string)$orgUnit->ownerOrgId])) {
                $orgObject = \laabs::callService('organization/organization/read_orgId_', (string)$orgUnit->ownerOrgId);
                $ownerOriginatorOrgs[(string)$orgObject->orgId] = new \stdClass();
                $ownerOriginatorOrgs[(string)$orgObject->orgId]->displayName = $orgObject->displayName;
                $ownerOriginatorOrgs[(string)$orgObject->orgId]->orgId = $orgObject->orgId;
                $ownerOriginatorOrgs[(string)$orgObject->orgId]->parentOrgId = $orgObject->parentOrgId;
                $ownerOriginatorOrgs[(string)$orgObject->orgId]->originators = [];
            }
            $ownerOriginatorOrgs[$orgUnit->ownerOrgId]->originators[] = $orgUnit;
        }

        return $ownerOriginatorOrgs;
    }

    /**
     * Get originator
     *
     * @return object[] List of originator
     */
    public function getOriginator()
    {
        $currentService = \laabs::getToken("ORGANIZATION");
        if (!$currentService) {
            $this->view->addContentFile("recordsManagement/welcome/noWorkingOrg.html");

            return $this->view->saveHtml();
        }
        $ownerOriginatorOrgs = $this->getOwnerOrgsByRole($currentService, 'originator');
        $originators = [];
        foreach ($ownerOriginatorOrgs as $org) {
            foreach ($org->originator as $originator) {
                $originator->ownerOrgName = $org->displayName;
                $originators[] = $originator;
            }
        }

        return $originators;
    }

    /**
     * Get the list of user accessible oranizations
     * @param object $currentService The user's current service
     * @param string $role           The org unit role to select
     *
     * @return The list of user accessible  orgs
     */
    protected function getOwnerOrgsByRole($currentService, $role)
    {
        $orgUnits = \laabs::callService('organization/organization/readByrole_role_', $role);

        $userPositionController = \laabs::newController('organization/userPosition');
        $orgController = \laabs::newController('organization/organization');

        $owner = false;
        $archiver = false;
        $userOrgUnits = [];
        $userOrgs = [];

        $userOrgUnitOrgRegNumbers = array_merge(array($currentService->registrationNumber), $userPositionController->readDescandantService((string)$currentService->orgId));
        foreach ($userOrgUnitOrgRegNumbers as $userOrgUnitOrgRegNumber) {
            $userOrgUnit = $orgController->getOrgByRegNumber($userOrgUnitOrgRegNumber);
            $userOrgUnits[] = $userOrgUnit;
            if (isset($userOrgUnit->orgRoleCodes)) {
                foreach ($userOrgUnit->orgRoleCodes as $orgRoleCode) {
                    if ($orgRoleCode == 'owner') {
                        $owner = true;
                    }
                    if ($orgRoleCode == 'archiver') {
                        $archiver = true;
                    }
                }
            }
        }

        foreach ($userOrgUnits as $userOrgUnit) {
            foreach ($orgUnits as $orgUnit) {
                if (
                // Owner = all originators
                $owner
                    // Archiver = all originators fo the same org
                || ($archiver && $orgUnit->ownerOrgId == $userOrgUnit->ownerOrgId)
                    // Originator = all originators at position and sub-services
                || ($role == 'originator' && $orgUnit->registrationNumber == $userOrgUnit->registrationNumber)
                    // Depositor = all
                || $role == 'depositor') {
                    if (!isset($userOrgs[(string)$orgUnit->ownerOrgId])) {
                        $orgObject = \laabs::callService('organization/organization/read_orgId_', (string)$orgUnit->ownerOrgId);

                        $userOrgs[(string)$orgObject->orgId] = new \stdClass();
                        $userOrgs[(string)$orgObject->orgId]->displayName = $orgObject->displayName;
                        $userOrgs[(string)$orgObject->orgId]->{$role} = [];
                    }
                    $userOrgs[(string)$orgObject->orgId]->{$role}[] = $orgUnit;
                }
            }
        }

        return $userOrgs;
    }
}
