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
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\archiveManagement;
/**
 * Interface for archive modification
 */
interface modifyInterface
{
    // --------------------------------------------------------------------------    
    // Preservation rule
    // --------------------------------------------------------------------------
    /**
     * Read the retention rule of archive
     * 
     * @uses recordsManagement/archive/read_archiveId_Retentionrule
     * @return recordsManagement/archive/editArchiveRetentionRule
     */
    public function readRecordsmanagementArchiveRetentionrule_archiveId_();

    /**
     * Read the retention rule of multiple archives
     * @param array $archiveIds Array of archive identifier or sigle archive identifier
     * 
     * @return recordsManagement/archive/editArchiveRetentionRule
     *  
     */
    public function readRecordsmanagementArchiveRetentionrule();
    /**
     * Update a retention rule
     * @param recordsManagement/archiveRetentionRule $retentionRule The retention rule object
     * @param array                                  $archiveIds    The archives ids
     * 
     * @uses recordsManagement/archives/updateRetentionrule
     * @return recordsManagement/archive/modifyRetentionRule
     * 
     */
    public function updateRecordsmanagementArchiveRetentionrule($retentionRule, $archiveIds);

    // --------------------------------------------------------------------------
    // Access rule
    // --------------------------------------------------------------------------
    /**
     * Read the access rule of archive
     * 
     * @return recordsManagement/archive/editArchiveAccessRule
     * 
     * @uses recordsManagement/archive/readAccessrule_archiveId_
     * 
     */
    public function readRecordsmanagementArchiveAccessrule_archiveId_();

    /**
     * Read the access rule of multiple archives
     * @param array $archiveIds Array of archive identifier or sigle archive identifier
     * 
     * @return recordsManagement/archive/editArchiveAccessRule
     * 
     * @uses recordsManagement/archives/readAccessrule
     * 
     */
    public function readRecordsmanagementArchiveAccessrule($archiveIds);
    /**
     * Update a access rule
     * @param recordsManagement/archiveAccessRule $accessRule The access rule object
     * @param array                               $archiveIds The archives ids
     * 
     * @return recordsManagement/archive/modifyAccessRule
     * 
     * @uses recordsManagement/archives/updateAccessrule
     */
    public function updateRecordsmanagementArchiveAccessrule($accessRule, $archiveIds);

    // --------------------------------------------------------------------------
    // Freeze rules
    // --------------------------------------------------------------------------
    /**
     * Suspend archives
     * @param array $archiveIds Array of archive identifier
     * @param string                              $comment     The comment of modification
     * @param string                              $identifiant Message identifiant
     * 
     * @return recordsManagement/archive/freeze
     * 
     * @uses recordsManagement/archives/updateFreeze
     * 
     */
    public function updateRecordsmanagementArchiveFreeze($archiveIds, $comment, $identifiant);

    /**
     * Change the status of an archive
     * @param mixed  $archiveIds
     * @param string                              $comment     The comment of modification
     * @param string                              $identifiant Message identifiant
     * 
     * @return recordsManagement/archive/unfreeze
     * 
     * @uses recordsManagement/archives/updateUnfreeze
     */
    public function updateRecordsmanagementArchiveUnfreeze($archiveIds, $comment, $identifiant);
}