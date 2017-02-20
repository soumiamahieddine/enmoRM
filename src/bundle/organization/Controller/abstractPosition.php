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
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org> 
 */
abstract class abstractPosition
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
     * Get user postions list
     *
     * @return array The list of positions
     */
    abstract protected function listPositions();

    /**
     * Get my all positions
     *
     * @return array The list of my position's
     */
    public function getMyPositions()
    {
        $positions = $this->listPositions();
        $currentOrg = \laabs::getToken("ORGANIZATION");

        foreach ($positions as $position) {
            $organization = $this->sdoFactory->read('organization/organization', $position->orgId);

            $position->organization = $organization;
            $position->organization->orgName = $organization->displayName;

            if ($position->default && !$currentOrg) {
                \laabs::setToken("ORGANIZATION", $organization, 86400);
            }
        }

        return $positions;
    }

    /**
     * Set my working positions
     * @param organization/organization $orgId The organization identifier 
     * 
     * @return bool The resutl of the operation
     */
    public function setCurrentPosition($orgId)
    {
        if ($organization = $this->sdoFactory->read('organization/organization', $orgId)) {
            \laabs::setToken("ORGANIZATION", $organization, 86400);

            return true;
        }

        throw \laabs::Bundle('organization')->newException('workingPositionException', 'This position is not defined for this user');

        return false;
    }

    /**
     * List user owner org and 
     *
     * @return array The list of organization ids
     */
    public function listMyOrgs()
    {
        $positions = $this->getMyPositions();
        $services = array();
        $organizations = array();

        foreach ($positions as $position) {
            $service = $this->sdoFactory->read('organization/organization', $position->orgId);
            $services[(string) $service->orgId] = $service;
        }

        foreach ($services as $service) {
            if (!isset($organizations[(string) $service->ownerOrgId])) {
                $organization = $this->sdoFactory->read('organization/organization', $service->ownerOrgId);
                $organizations[(string) $service->ownerOrgId] = (string) $organization->registrationNumber;
            }
        }

        return array_merge($this->readDescandantOrg($organizations), $organizations);
    }

    /**
     * List user positions organization ids
     *
     * @return array The list of organization ids
     */
    public function listMyServices()
    {
        $positions = $this->getMyPositions();
        $services = array();

        foreach ($positions as $position) {
            $service = $this->sdoFactory->read('organization/organization', $position->orgId);

            $services[(string) $service->orgId] = (string) $service->registrationNumber;
            $services = array_merge($services, $this->readDescandantService((string) $service->orgId));

            $childrenOrgIds = $this->sdoFactory->index('organization/organization', 'orgId', "parentOrgId = '$service->ownerOrgId' AND isOrgUnit = false");

            foreach ($childrenOrgIds as $orgId) {
                $services = array_merge($services, $this->readDescandantService((string) $orgId));
            }

        }

        return $services;
    }

    /**
     * List user descendents service ids of his current position
     *
     * @return array The list of service orgRegNumber
     */
    public function listMyCurrentDescendantServices()
    {
        $currentOrg = \laabs::getToken("ORGANIZATION");

        if (!$currentOrg) {
            return null;
        }

        $orgId = (string) $currentOrg->orgId;
        $userServices = array($orgId => $currentOrg->registrationNumber);


        $ownerOrg = $this->sdoFactory->read('organization/organization', (string) $currentOrg->parentOrgId);
        if (!$ownerOrg->isOrgUnit) {
            $childrenOrg = $this->sdoFactory->index('organization/organization', 'registrationNumber', "parentOrgId = '$currentOrg->parentOrgId' AND isOrgUnit = false");

            foreach ($childrenOrg as $childId => $childRegNumber) {
                $userServices = array_merge($userServices, $this->readDescandantService($childId));
            }
        }

        $userServices = array_merge($userServices, $this->readDescandantService($orgId));

        return array_merge($userServices, $this->readDescandantService($orgId));
    }

    /**
     * List user descendents orgs ids of his current position
     *
     * @return array The list of organization orgRegNumber
     */
    public function listMyCurrentDescendantOrgs()
    {
        $currentOrg = \laabs::getToken("ORGANIZATION");

        if (!$currentOrg) {
            return array();
        }

        $orgId = (string) $currentOrg->ownerOrgId;
        $ownerOrg = $this->sdoFactory->read('organization/organization', $orgId);

        $userOrg = array($orgId => $ownerOrg->registrationNumber);
        $descendantOrg = $this->readDescandantOrg(array( (string) $ownerOrg->orgId => $ownerOrg->registrationNumber));

        return array_merge($descendantOrg, $userOrg);;
    }

    /**
     * Read children orgs recursively
     * @param array $orgs List of orgRegNumber with orgId as key
     *
     * @return array The list of organization
     */
    protected function readDescandantOrg(array $orgs)
    {
        $descandantOrg = array();

        foreach ($orgs as $orgId => $orgRegNumber) {
            $childrenOrgs = $this->sdoFactory->index('organization/organization', 'registrationNumber', "parentOrgId = '$orgId' AND isOrgUnit = false");

            foreach ($childrenOrgs as $child) {
                $childrenOrgs = array_merge($this->readDescandantOrg($childrenOrgs), $childrenOrgs);
            }

            $descandantOrg = array_merge($descandantOrg, $childrenOrgs);
        }

        return $descandantOrg;
    }

    /**
     * Read descandant services of an org
     * @param string $parentId The parent orgId
     *
     * @return array The list of services
     */
    public function readDescandantService($parentId)
    {
        $childrenService = $this->sdoFactory->index('organization/organization', 'registrationNumber', "parentOrgId = '$parentId' AND isOrgUnit = true");
        $childrenOrg = $this->sdoFactory->index('organization/organization', 'registrationNumber', "parentOrgId = '$parentId' AND isOrgUnit = false");

        foreach ($childrenService as $orgId => $orgRegNumber) {
            $childrenService = array_merge($this->readDescandantService($orgId), $childrenService);
        }

        foreach ($childrenOrg as $orgId => $orgRegNumber) {
            $childrenService = array_merge($this->readDescandantService($orgId), $childrenService);
        }

        return $childrenService;
    }
}
