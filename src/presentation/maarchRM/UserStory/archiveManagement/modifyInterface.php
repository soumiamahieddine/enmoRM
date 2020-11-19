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
    /**
     * Read organization by role
     *
     * @uses organization/organization/readByrole_role_
     * @return organization/organization/byRole
     */
    public function readOrganizationByrole_role_();

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
     * @param string                                 $comment       The comment of modification
     * @param string                                 $identifier    Message identifier
     * @param string                                 $format        Message format
     * 
     * @uses recordsManagement/archives/updateRetentionrule
     * @return recordsManagement/archive/modifyRetentionRule
     */
    public function updateRecordsmanagementArchiveRetentionrule($retentionRule, $archiveIds, $comment = null, $identifier = null, $format = null);

    // --------------------------------------------------------------------------
    // Access rule
    // --------------------------------------------------------------------------
    /**
     * Read the access rule of archive
     * 
     * @uses recordsManagement/archive/readAccessrule_archiveId_
     * @return recordsManagement/archive/editArchiveAccessRule
     */
    public function readRecordsmanagementArchiveAccessrule_archiveId_();

    /**
     * Read the access rule of multiple archives
     * @param array $archiveIds Array of archive identifier or sigle archive identifier
     * 
     * @uses recordsManagement/archives/readAccessrule
     * @return recordsManagement/archive/editArchiveAccessRule
     */
    public function readRecordsmanagementArchiveAccessrule($archiveIds);
    /**
     * Update a access rule
     * @param recordsManagement/archiveAccessRule $accessRule The access rule object
     * @param array                               $archiveIds The archives ids
     * @param string                              $comment    The comment of modification
     * @param string                              $identifier Message identifier
     * @param string                              $format     Message format
     *
     * @uses recordsManagement/archives/updateAccessrule
     * @return recordsManagement/archive/modifyAccessRule
     */
    public function updateRecordsmanagementArchiveAccessrule($accessRule, $archiveIds, $comment = null, $identifier = null, $format = null);

    // --------------------------------------------------------------------------
    // Freeze rules
    // --------------------------------------------------------------------------
    /**
     * Suspend archives
     * @param array $archiveIds Array of archive identifier
     * @param string                              $comment    The comment of modification
     * @param string                              $identifier Message identifier
     * @param string                              $format     Message format
     *
     * @uses recordsManagement/archives/updateFreeze
     * @return recordsManagement/archive/freeze
     */
    public function updateRecordsmanagementArchiveFreeze($archiveIds, $comment = null, $identifier = null, $format = null);

    /**
     * Change the status of an archive
     * @param mixed  $archiveIds
     * @param string                              $comment    The comment of modification
     * @param string                              $identifier Message identifier
     * @param string                              $format     Message format
     *
     * @uses recordsManagement/archives/updateUnfreeze
     * @return recordsManagement/archive/unfreeze
     */
    public function updateRecordsmanagementArchiveUnfreeze($archiveIds, $comment = null, $identifier = null, $format = null);
}