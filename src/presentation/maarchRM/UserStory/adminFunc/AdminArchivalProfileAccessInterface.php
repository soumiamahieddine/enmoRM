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

/**
 * User story admin organization
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
interface AdminArchivalProfileAccessInterface
{
    /**
     * Get the organizations' index
     *
     * @return organization/orgTree/index
     * 
     * @uses organization/organization/readTree
     * @uses organization/orgType/readList
     * @uses organization/orgRole/readList
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
     * Edit an organization
     *
     * @return organization/orgTree/readOrg
     *
     * @uses organization/organization/read_orgId_
     */
    public function readOrganization_orgId_();

     /**
     * Add a user position to an organization
     * 
     * @return organization/orgTree/udapteArchivalProfileAccess
     *
     * @uses organization/organization/update_orgId_Archivalprofileaccess
     */
    public function updateOrganization_orgId_Archivalprofileaccess($archivalProfileAccess);
}