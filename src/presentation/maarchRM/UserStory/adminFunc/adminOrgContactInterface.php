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
interface AdminOrgContactInterface
    extends contact
{
    /**
     * Add an orgnization contact
     * @param object $contact
     * @param bool   $isSelf
     *
     * @return organization/orgContact/create
     * @uses organization/organization/create_orgId_Contact
     */
    public function createOrganization_orgId_Contact($contact, $isSelf);

    /**
     * Remove a contact's position
     *
     * @return organization/orgTree/deleteContactPosition
     * @uses organization/organization/deleteContactposition_orgId__contactId_
     */
    public function deleteOrganization_orgId_Contact_contactId_();

}