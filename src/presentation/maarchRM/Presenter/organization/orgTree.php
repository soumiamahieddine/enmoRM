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
    public $publicArchives;

    /**
     * __construct
     *
     * @param \dependency\html\Document   $view       A new ready-to-use empty view
     * @param \dependency\json\JsonObject $jsonObject The json base object
     * @param \dependency\sdo\Factory     $sdoFactory The Sdo Factory for data access
     */
    public function __construct(
    \dependency\html\Document $view, \dependency\json\JsonObject $jsonObject, \dependency\sdo\Factory $sdoFactory, $publicArchives=false)
    {
        $this->view = $view;

        $this->json = $jsonObject;
        $this->json->status = true;

        $this->sdoFactory = $sdoFactory;

        $this->translator = $this->view->translator;
        $this->translator->setCatalog('organization/messages');

        $this->publicArchives = $publicArchives;
    }

    /**
     * index
     * @param array $organizations Array of organization
     * @param array $orgType       Array of organization type
     * @param array $orgRole       Array of organization role
     *
     * @return view View with the list of organizations
     */
    public function index($organizations, $orgType, $orgRole)
    {
        $this->view->addContentFile("organization/organizationIndex.html");
        $communicationMeans = \laabs::callService("contact/communicationMean/readIndex");

        $this->view->setSource("publicArchives", $this->publicArchives);
        $this->view->setSource("orgType", $orgType);
        $this->view->setSource("orgRole", $orgRole);
        $this->view->setSource("communicationMeans", $communicationMeans);
        $this->view->merge();
        $this->view->translate();

        /*if (sizeof($organizations) != 0) {
            $tree = $this->contructTree($organizations);

            if ($tree != null) {
                $orgList = $this->view->getElementsByClass('dataTree')->item(0);
                $orgList->appendChild($tree);
            }
        }*/

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
        /*
        $html = '';
        if (sizeof($organizations) > 0) {
            $tree = $this->contructTree($organizations);
            //$this->view->appendChild($tree);
            foreach ($tree->childNodes as $branch) {
                $html .= $this->view->saveHtml($branch);
            }
        }

        return $html;

        */
        $this->view->addContentFile("organization/orgTree.html");
        $this->view->setSource("organizations", $organizations);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
        
    }

    /**
     * contructTree
     * @param array $organizations The tree representing the orgs
     *
     * @return view View with the tree
     */
    protected function contructTree($organizations)
    {
        $orgTree = $this->view->createDocumentFragment();

        // Organization fragment
        $orgFragmentTemplate = $this->view->createDocumentFragment();
        $orgFragmentTemplate->appendHtmlFile("organization/organizationItem.html");
        $this->view->translate($orgFragmentTemplate);

        // OrgUnit fragment
        $orgUnitFragmentTemplate = $this->view->createDocumentFragment();
        $orgUnitFragmentTemplate->appendHtmlFile("organization/orgUnitItem.html");
        $this->view->translate($orgUnitFragmentTemplate);

        // Person fragment
        $personFragmentTemplate = $this->view->createDocumentFragment();
        $personFragmentTemplate->appendHtmlFile("organization/personItem.html");
        $this->view->translate($personFragmentTemplate);

        // Service fragment
        $serviceFragmentTemplate = $this->view->createDocumentFragment();
        $serviceFragmentTemplate->appendHtmlFile("organization/serviceItem.html");
        $this->view->translate($serviceFragmentTemplate);

        // Contact fragment
        $contactFragmentTemplate = $this->view->createDocumentFragment();
        $contactFragmentTemplate->appendHtmlFile("organization/contactItem.html");
        $this->view->translate($contactFragmentTemplate);

        //Organization
        foreach ($organizations as $organization) {
            $treeNode = $this->view->createElement('ul');
            $orgFragment = $orgFragmentTemplate->cloneNode(true);
            $orgItem = $orgTree->appendChild($treeNode);
            $treeNode->appendChild($orgFragment);

            $this->view->merge($orgItem, $organization);

            // Creation of the children container
            if (!empty($organization->organization) || !empty($organization->userPosition) || !empty($organization->servicePosition) || !empty($organization->orgContact)) {
                $orgElement = $orgItem->getElementsByTagName('li')->item(0);
                $childrenContainer = $this->view->createElement('ul');
                $orgElement->appendChild($childrenContainer);

                if (!empty($organization->organization)) {
                     $this->mergeOrgUnits($organization, $childrenContainer, $orgFragmentTemplate, $orgUnitFragmentTemplate, $personFragmentTemplate, $serviceFragmentTemplate, $contactFragmentTemplate);
                }

                if (!empty($organization->userPosition)) {
                    $this->mergeUserPosition($organization, $childrenContainer, $personFragmentTemplate);
                }

                if (!empty($organization->servicePosition)) {
                    $this->mergeServicePosition($organization, $childrenContainer, $serviceFragmentTemplate);
                }

                if (!empty($organization->orgContact)) {
                    $this->mergeContactPosition($organization, $childrenContainer, $contactFragmentTemplate);
                }
            }
        }

        return $orgTree;
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

    // JSON
    /**
     * Serializer JSON for create method
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

    /*
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

    /*
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

}
