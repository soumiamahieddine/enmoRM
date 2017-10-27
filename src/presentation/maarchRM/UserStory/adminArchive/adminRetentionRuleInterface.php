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
 * User story admin retention rule
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface adminRetentionRuleInterface
{
    /**
     *  Retention rules index
     * 
     * @return recordsManagement/retentionRule/index
     *
     * @uses recordsManagement/retentionRule/readIndex
     * 
     */
    public function readRetentionrules();

    /**
     *  List the retention rules
     * 
     * @return recordsManagement/retentionRule/listRules
     *
     * @uses recordsManagement/retentionRule/readIndex
     * 
     */
    public function readRetentionrulestable();

    /**
     *  Create an retention rule
     * @param recordsManagement/retentionRule $retentionRule The retention rule
     * 
     * @return recordsManagement/retentionRule/create
     *
     * @uses recordsManagement/retentionRule/create
     *
     */
    public function createRetentionrule($retentionRule);

    /**
     *  Read an retention rule
     * 
     * @return recordsManagement/retentionRule/edit The retention rule
     *
     * @uses recordsManagement/retentionRule/read_code_
     *
     */
    public function readRetentionrule_code_($code);

    /**
     *  Update an retention rule
     * @param recordsManagement/retentionRule $retentionRule The retention rule
     * 
     * @return recordsManagement/retentionRule/update
     *
     * @uses recordsManagement/retentionRule/update
     *
     */
    public function updateRetentionrule_code_($retentionRule);

    /**
     *  Delete an access rule
     * @param string $code The access rule code
     * 
     * @return recordsManagement/retentionRule/delete
     *
     * @uses recordsManagement/retentionRule/delete_code_
     *
     */
    public function deleteRetentionrule_code_($code);
}