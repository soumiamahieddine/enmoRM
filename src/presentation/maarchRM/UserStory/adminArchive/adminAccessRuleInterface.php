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
namespace presentation\maarchRM\UserStory\adminArchive;

/**
 * User story admin access rule
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface adminAccessRuleInterface
{
    /**
     *  List the access rule's code
     *
     * @return recordsManagement/accessRule/index The list of access code
     * @uses recordsManagement/accessRule/readIndex
     */
    public function readAccessrules();

    /**
     * New empty accessRule
     *
     * @return recordsManagement/accessRule/newAccessRule
     * 
     */
    public function readAccessrule();

    /**
     * Create an access code
     * @param recordsManagement/accessRule $accessRule The access code
     *
     * @return recordsManagement/accessRule/create
     * @uses recordsManagement/accessRule/create
     */
    public function createAccessrule($accessRule);

    /**
     * Edit an access code
     * @param string $code
     *
     * @return recordsManagement/accessRule/edit
     * @uses recordsManagement/accessRule/read_code_
     */
    public function readAccessrule_code_($code);

    /**
     * update an access code
     * @param recordsManagement/accessRule $accessRule The access code
     *
     * @return recordsManagement/accessRule/update
     *
     * @uses recordsManagement/accessRule/update
     */
    public function updateAccessrule_code_($accessRule);

    /**
     * delete an access code
     *
     * @return recordsManagement/accessRule/delete
     *
     * @uses recordsManagement/accessRule/delete_code_
     */
    public function deleteAccessrule_code_();
}