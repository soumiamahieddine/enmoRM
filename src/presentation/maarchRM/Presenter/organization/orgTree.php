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

namespace presentation\maarchRM\Presenter\organization;

/**
 * Bundle registeredMail html serializer
 *
 * @package Organization
 */
class orgTree
{

    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $json;
    public $sdoFactory;

    /**
     * __construct
     *
     * @param \dependency\html\Document   $view           A new ready-to-use empty view
     * @param \dependency\json\JsonObject $jsonObject     The json base object
     * @param \dependency\sdo\Factory     $sdoFactory     The Sdo Factory for data access
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $jsonObject, \dependency\sdo\Factory $sdoFactory)
    {
        $this->view = $view;

        $this->json = $jsonObject;
        $this->json->status = true;

        $this->sdoFactory = $sdoFactory;

        $this->translator = $this->view->translator;
        $this->translator->setCatalog('organization/messages');
    }

    /**
     * index
     * @param array $organizations Array of organization
     * @param array $orgType       Array of organization type
     *
     * @return view View with the list of organizations
     */
    public function index($organizations, $orgType)
    {
        $orgRole = \laabs::configuration('organization')['orgUnitRoles'];
        $isOrgTree = isset(\laabs::configuration('organization')['isOrgTree']) ? (bool) \laabs::configuration('organization')['isOrgTree'] : true;
        $hasSecurityLevel = isset(\laabs::configuration('auth')['useSecurityLevel']) ? (bool) \laabs::configuration('auth')['useSecurityLevel'] : false;

        $this->view->addContentFile("organization/organizationIndex.html");
        $communicationMeans = \laabs::callService("contact/communicationMean/readIndex");
        $countriesCodes = \laabs::callService("organization/orgContact/readCountriesCodes");
        $archivalProfile = \laabs::callService('recordsManagement/archivalProfile/readIndex');
        $serviceLevel = \laabs::callService('recordsManagement/serviceLevel/readIndex');

        // Sort archival profile by reference
        usort($archivalProfile, function ($a, $b) {
            return strcmp($a->reference, $b->reference);
        });

        $authController = \laabs::newController("auth/userAccount");
        $user = $authController->get(\laabs::getToken('AUTH')->accountId);

        $manageUserInOrg = true;

        if ($hasSecurityLevel && $user->getSecurityLevel() == $user::SECLEVEL_USER) {
            $manageUserInOrg = false;
        }

        if (\laabs::getToken("ORGANIZATION") && \laabs::getToken("ORGANIZATION")->orgRoleCodes) {
            $addOrganizationRight = in_array('owner', \laabs::getToken("ORGANIZATION")->orgRoleCodes);
        } elseif (in_array($user->accountName, \laabs::configuration("auth")["adminUsers"])) {
            $addOrganizationRight = true;
        } else {
            $addOrganizationRight = false;
        }

        $adminOrg = \laabs::callService('auth/userAccount/readHasprivilege', "adminFunc/adminOrganization");
        $adminUser = \laabs::callService('auth/userAccount/readHasprivilege', "adminFunc/adminOrgUser");
        $adminContact = \laabs::callService('auth/userAccount/readHasprivilege', "adminFunc/adminOrgContact");


        $commonJsAccesses = $this->view->createDocumentFragment();
        $commonJsAccesses->appendHtmlFile("organization/commonJsAccesses.html");
        $this->view->getElementById('profile_accordion')->appendChild($commonJsAccesses);

        $this->view->setSource("adminOrg", $adminOrg);
        $this->view->setSource("adminUser", $adminUser);
        $this->view->setSource("adminContact", $adminContact);
        $this->view->setSource("orgType", $orgType);
        $this->view->setSource("orgRole", $orgRole);
        $this->view->setSource("isOrgTree", $isOrgTree);
        $this->view->setSource("communicationMeans", $communicationMeans);
        $this->view->setSource("countriesCodes", $countriesCodes);
        $this->view->setSource("archivalProfile", $archivalProfile);
        $this->view->setSource("serviceLevel", $serviceLevel);
        $this->view->setSource("addOrganizationRight", $addOrganizationRight);
        $this->view->setSource("manageUserInOrg", $manageUserInOrg);

        $profileType = false;
        if (isset(\laabs::configuration('recordsManagement')['archivalProfileType'])) {
            $profileType = \laabs::configuration('recordsManagement')['archivalProfileType'];
        }
        $publicArchives = isset(\laabs::configuration('presentation.maarchRM')['publicArchives']) && \laabs::configuration('presentation.maarchRM')['publicArchives'];

        $this->view->setSource("hideAccess", $publicArchives || ($profileType == 1));
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * getTree
     * @param array $organizations Tree of organisations
     *
     * @return view View with the tree of organizations
     */
    public function getTree($organizations)
    {
        $adminOrg = \laabs::callService('auth/userAccount/readHasprivilege', "adminFunc/adminOrganization");

        $this->view->addContentFile("organization/orgTree/orgTree.html");
        $this->view->setSource("adminOrg", $adminOrg);
        $this->view->setSource("organizations", $organizations);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }

    protected function mergeOrgUnits($parent, $container, $orgFragmentTemplate, $orgUnitFragmentTemplate, $personFragmentTemplate, $serviceFragmentTemplate, $contactFragmentTemplate)
    {
        $orgs = array();
        $orgUnits = array();

        foreach ($parent->organization as $orgUnit) {
            if ($orgUnit->isOrgUnit) {
                $orgUnits[] = $orgUnit;
            } else {
                $orgs[] = $orgUnit;
            }
        }

        $parent->organization = array_merge($orgs, $orgUnits);

        foreach ($parent->organization as $orgUnit) {
            if ($orgUnit->isOrgUnit) {
                $orgUnitFragment = $orgUnitFragmentTemplate->cloneNode(true);
            } else {
                $orgUnitFragment = $orgFragmentTemplate->cloneNode(true);
            }

            $this->view->merge($orgUnitFragment, $orgUnit);
            $orgUnitItem = $container->appendChild($orgUnitFragment);

            if (!empty($orgUnit->organization) || isset($orgUnit->userPosition) && !empty($orgUnit->userPosition) || isset($orgUnit->servicePosition) && !empty($orgUnit->orgContact)) {
                $childrenContainer = $this->view->createElement('ul');
                $orgUnitItem->appendChild($childrenContainer);

                if (!empty($orgUnit->organization)) {
                    $this->mergeOrgUnits($orgUnit, $childrenContainer, $orgFragmentTemplate, $orgUnitFragmentTemplate, $personFragmentTemplate, $serviceFragmentTemplate, $contactFragmentTemplate);
                }

                if (!empty($orgUnit->userPosition)) {
                    $this->mergeUserPosition($orgUnit, $childrenContainer, $personFragmentTemplate);
                }

                if (!empty($orgUnit->servicePosition)) {
                    $this->mergeServicePosition($orgUnit, $childrenContainer, $serviceFragmentTemplate);
                }

                if (!empty($orgUnit->orgContact)) {
                    $this->mergeContactPosition($orgUnit, $childrenContainer, $contactFragmentTemplate);
                }
            }
        }
    }

    protected function mergeUserPosition($parent, $container, $personFragmentTemplate)
    {
        foreach ($parent->userPosition as $userPosition) {
            $userPositionFragment = $personFragmentTemplate->cloneNode(true);
            $this->view->merge($userPositionFragment, $userPosition);
            $container->appendChild($userPositionFragment);
        }
    }

    protected function mergeServicePosition($parent, $container, $serviceFragmentTemplate)
    {
        foreach ($parent->servicePosition as $servicePosition) {
            $servicePositionFragment = $serviceFragmentTemplate->cloneNode(true);
            $this->view->merge($servicePositionFragment, $servicePosition);
            $container->appendChild($servicePositionFragment);
        }
    }

    protected function mergeContactPosition($parent, $container, $contactFragmentTemplate)
    {
        foreach ($parent->orgContact as $contactPosition) {
            $contactPositionFragment = $contactFragmentTemplate->cloneNode(true);
            $this->view->merge($contactPositionFragment, $contactPosition);
            $container->appendChild($contactPositionFragment);
        }
    }

    /**
     * Export the file plan
     * @param array $organizations Array of organization
     *
     * @return xml The file plan
     */
    public function exportFilePlan($organizations)
    {
        $document = new \DomDocument("1.0", "ISO-8859-1");

        $filePlan = $document->createElement('FilePlan');
        $this->addOrganizatonToFilePlan($organizations, $filePlan, $document);
        $document->appendChild($filePlan);

        \laabs::setResponseType("application/xml");
        $response = \laabs::kernel()->response;
        $response->setHeader("Content-Disposition", "inline; filename=filePlanExport.xml");

        return $document->saveXML();
    }

    /**
     * Export the file plan organization
     * @param array       $organizations Array of organization
     * @param domNode     $parentNode    The parent node
     * @param domDocument $document      The document
     */
    protected function addOrganizatonToFilePlan($organizations, $parentNode, $document)
    {
        foreach ($organizations as $organization) {
            if (!$organization->isOrgUnit) {
                $orgNode = $document->createElement('Organization', (string) $organization->displayName);
            } else {
                $orgNode = $document->createElement('Activity');
            }
            $orgNode->setAttribute('registrationNumber', $organization->registrationNumber);

            if ($organization->organization) {
                $this->addOrganizatonToFilePlan($organization->organization, $orgNode, $document);
            }

            $profiles = \laabs::callService("organization/organization/readOrgunitprofiles", $organization->registrationNumber);

            if ($profiles) {
                $this->addProfileToFilePlan($profiles, $orgNode, $document);
            }

            $parentNode->appendChild($orgNode);
        }
    }

    /**
     * Export the file plan profiles
     * @param array       $profiles   Array of profile
     * @param domNode     $parentNode The parent node
     * @param domDocument $document   The document
     */
    protected function addProfileToFilePlan($profiles, $parentNode, $document)
    {
        foreach ($profiles as $profile) {
            if ($profile == "*") {
                continue;
            }

            $profileNode = $document->createElement('DocumentProfile', (string) $profile->name);
            $profileNode->setAttribute('reference', (string) $profile->reference);
            $profileNode->setAttribute('retentionRuleCode', (string) $profile->retentionRuleCode);

            if ($profile->containedProfiles) {
                $this->addProfileToFilePlan($profile->containedProfiles, $profileNode, $document);
            }

            $parentNode->appendChild($profileNode);
        }

    }

    // JSON
    /**
     * Serializer JSON for create method
     * @param string $orgId The organization identifier
     *
     * @return object JSON object with a status and message parameters
     */
    public function addOrganization($orgId)
    {
        $this->json->message = "Organization added";
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->orgId = $orgId;

        return $this->json->save();
    }

    /**
     * Serializer JSON for delete method
     *
     * @return object JSON object with a status and message parameters
     */
    public function deleteOrganization()
    {
        $this->json->message = "Organization and his childrens are deleted";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for update method
     *
     * @return object JSON object with a status and message parameters
     */
    public function modifyOrganization()
    {
        $this->json->message = "Organization updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for orgTypeException method
     *
     * @return object JSON object with a status and message parameters
     */
    public function orgTypeException()
    {
        $this->json->status = false;
        $this->json->message = "Missing display name";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for seting default person position method
     *
     * @return object JSON object with a status and message parameters
     */
    public function setDefaultPosition()
    {
        $this->json->message = "Position set to default";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for set default person position method
     *
     * @return object JSON object with a status and message parameters
     */
    public function addUserPosition()
    {
        $this->json->message = "User added to the organization";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for adding person position method
     *
     * @return object JSON object with a status and message parameters
     */
    public function deleteUserPosition()
    {
        $this->json->message = "User removed";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for adding contact position method
     *
     * @return object JSON object with a status and message parameters
     */
    public function deleteContactPosition()
    {
        $this->json->message = "Contact removed";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for orgTypeException method
     *
     * @return object JSON object with a status and message parameters
     */
    public function personTypeException()
    {
        $this->json->status = false;
        $this->json->message = "Missing display name";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for createArchivalProfileAccess method
     *
     * @return object JSON object with a status and message parameters
     */
    public function createArchivalProfileAccess()
    {
        $this->json->status = true;
        $this->json->message = "Archival profiles access created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for updateArchivalProfileAccess method
     *
     * @return object JSON object with a status and message parameters
     */
    public function updateArchivalProfileAccess()
    {
        $this->json->status = true;
        $this->json->message = "Archival profiles access updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON deleteArchivalProfileAccess method
     *
     * @return object JSON object with a status and message parameters
     */
    public function deleteArchivalProfileAccess()
    {
        $this->json->status = true;
        $this->json->message = "Archival profiles access deleted";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for read method
     *
     * @return object JSON object with a status and message parameters
     */
    public function readOrg($organization)
    {
        $organizationController = \laabs::newController("organization/organization");
        $organization->isUsed = $organizationController->isUsed($organization->registrationNumber);

        return json_encode($organization);
    }

    public function orgList($organizations)
    {
        $orgs = [];
        foreach ($organizations as $org) {
            $orgs[] = $org;
        }

        return json_encode($orgs, JSON_UNESCAPED_UNICODE);
    }

    public function changeStatus() {
        $this->json->message = "Status changed";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
}
