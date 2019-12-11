<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\adminFunc;

use bundle\organization\Model\organization;

/**
 * User story admin organization
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface AdminOrganizationInterface
{
        /**
     * Get the organizations' index
     *
     * @return organization/orgTree/index
     * 
     * @uses organization/organization/readTree
     * @uses organization/orgType/readList
     */
    public function readOrganizations();

    /**
     * Get the organizations' index
     *
     * @return organization/orgTree/getTree
     * 
     * @uses organization/organization/readTree
     */
    public function readOrganizationtree();

    /**
     * Add an organization
     * @param organization/organization $organization The organization object to add
     *
     * @return organization/orgTree/addOrganization
     * 
     * @uses organization/organization/create
     */
    public function createOrganization($organization);

    /**
     * Edit an organization
     * 
     * @uses organization/organization/read_orgId_
     * @return organization/orgTree/readOrg
     */
    public function readOrganization_orgId_();

    /**
     * Update an organization
     * @param object $organization The organization object to update
     *
     * @return organization/orgTree/modifyOrganization
     * 
     * @uses organization/organization/update_orgId_
     */
    public function updateOrganization_orgId_($organization);

    /**
     * Update an organization
     * @param string $newParentOrgId The new parent organization identifier
     * @param string $newOwnerOrgId  The new owner organization identifier
     *
     * @return organization/orgTree/modifyOrganization
     * 
     * @uses organization/organization/updateMove_orgId_
     */
    public function updateOrganization_orgId_Parent($newParentOrgId, $newOwnerOrgId);

    /**
     * Delete an organization
     *
     * @return organization/orgTree/deleteOrganization
     * 
     * @uses organization/organization/delete_orgId_
     */
    public function deleteOrganization_orgId_($orgId);

    /**
     * ORGANIZATION CONTACT
     */

    /**
     * Get the organization types
     *
     * @return organization/orgType/index
     * @uses organization/orgType/readList
     */
    public function readOrganizationtypeIndex();

    /**
     * Add an org type
     * @param organization/orgType $orgType the orgType to create
     *
     * @return organization/orgType/createOrganizationtype
     * @uses organization/orgType/create
     */
    public function createOrganizationtype($orgType);
    
    /**
     * Edit an org type 
     *
     * @uses organization/orgType/read_code_
     */
    public function readOrganizationtype_code_();
    
    /**
     * Update an org type
     * @param organization/orgType $orgType The orgType to update
     *
     * @return organization/orgType/updateOrganizationtype
     * @uses organization/orgType/update_code_
     */
    public function updateOrganizationtype_code_($orgType);

    /**
     * Update an org type
     *
     * @return organization/orgType/deleteOrganizationtype
     * @uses organization/orgType/delete_code_
     */
    public function deleteOrganizationtype_code_();

    /**
     * Export file plan
     *
     * @return organization/orgTree/exportFilePlan
     * @uses organization/organization/readTree
     */
    public function readExportfileplan();


    /**
     * Change status of organization
     *
     * @return organization/orgTree/changeStatus
     * @uses organization/organization/read_orgId_ChangeStatus_status_
     */
    public function updateOrganization_orgId_Status_status_();
}