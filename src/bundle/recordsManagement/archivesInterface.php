<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\recordsManagement;

/**
 * Interface for management of archives
 *
 * @package Recordsmanagement
 */
interface archivesInterface
{
    /*
        RETRIEVE ARCHIVES
    */
    /**
     * Search archives by profile / dates / agreement
     *
     * @param string  $archiveId
     * @param string  $profileReference
     * @param string  $status
     * @param string  $archiveName
     * @param string  $agreementReference
     * @param string  $archiveExpired
     * @param string  $finalDisposition
     * @param string  $originatorOrgRegNumber
     * @param string  $originatorOwnerOrgId
     * @param string  $originatorArchiveId
     * @param array   $originatingDate
     * @param string  $filePlanPosition
     * @param bool    $hasParent
     * @param string  $description
     * @param string  $text
     * @param bool    $partialRetentionRule
     * @param string  $retentionRuleCode
     * @param string  $depositStartDate
     * @param string  $depositEndDate
     * @param string  $originatingStartDate
     * @param string  $originatingEndDate
     * @param string  $archiverArchiveId
     * @param integer $maxResults
     *
     * @action recordsManagement/archive/search
     *
     */
    public function read(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $originatorOwnerOrgId = null,
        $originatorArchiveId = null,
        $originatingDate = null,
        $filePlanPosition = null,
        $hasParent = null,
        $description = null,
        $text = null,
        $partialRetentionRule = null,
        $retentionRuleCode = null,
        $depositStartDate = null,
        $depositEndDate = null,
        $originatingStartDate = null,
        $originatingEndDate = null,
        $archiverArchiveId = null,
        $maxResults = null
    );

    /**
     * Count archives by profile / dates / agreement
     *
     * @param string  $archiveId
     * @param string  $profileReference
     * @param string  $status
     * @param string  $archiveName
     * @param string  $agreementReference
     * @param string  $archiveExpired
     * @param string  $finalDisposition
     * @param string  $originatorOrgRegNumber
     * @param string  $originatorOwnerOrgId
     * @param string  $originatorArchiveId
     * @param array   $originatingDate
     * @param string  $filePlanPosition
     * @param bool    $hasParent
     * @param string  $description
     * @param string  $text
     * @param bool    $partialRetentionRule
     * @param string  $retentionRuleCode
     * @param string  $depositStartDate
     * @param string  $depositEndDate
     * @param string  $originatingStartDate
     * @param string  $originatingEndDate
     * @param string  $archiverArchiveId
     * @param integer $maxResults
     *
     * @action recordsManagement/archive/count
     *
     */
    public function readCount(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $originatorOwnerOrgId = null,
        $originatorArchiveId = null,
        $originatingDate = null,
        $filePlanPosition = null,
        $hasParent = null,
        $description = null,
        $text = null,
        $partialRetentionRule = null,
        $retentionRuleCode = null,
        $depositStartDate = null,
        $depositEndDate = null,
        $originatingStartDate = null,
        $originatingEndDate = null,
        $archiverArchiveId = null,
        $maxResults = null
    );

    /**
     * Search archives by profile / dates / agreement
     * @param string $archiveId
     * @param string $profileReference
     * @param string $status
     * @param string $archiveName
     * @param string $agreementReference
     * @param string $archiveExpired
     * @param string $finalDisposition
     * @param string $originatorOrgRegNumber
     * @param string $originatorOwnerOrgId
     * @param string $originatorArchiveId
     * @param array  $originatingDate
     * @param string $filePlanPosition
     * @param bool   $hasParent
     * @param string $description
     * @param string $text
     * @param bool   $partialRetentionRule
     * @param string $retentionRuleCode
     * @param string $depositStartDate
     * @param string $depositEndDate
     * @param string $originatingStartDate
     * @param string $originatingEndDate
     *
     * @action recordsManagement/archive/searchRegistry
     *
     */
    public function readRegistry(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $originatorOwnerOrgId = null,
        $originatorArchiveId = null,
        $originatingDate = null,
        $filePlanPosition = null,
        $hasParent = null,
        $description = null,
        $text = null,
        $partialRetentionRule = null,
        $retentionRuleCode = null,
        $depositStartDate = null,
        $depositEndDate = null,
        $originatingStartDate = null,
        $originatingEndDate = null
    );

    /**
     * Get archives list
     * @param string  $originatorOrgRegNumber The organization registration number
     * @param string  $filePlanPosition       The file plan position
     * @param boolean $archiveUnit            List the archive unit
     *
     * @action recordsManagement/archive/index
     */
    public function readList($originatorOrgRegNumber, $filePlanPosition = null, $archiveUnit = false);

     /**
     * Get archives Count without limit
     *
     * @param string  $originatorOrgRegNumber The organization registration number
     * @param string  $filePlanPosition       The file plan position
     * @param boolean $archiveUnit            List the archive unit
     *
     * @action recordsManagement/archive/countList
     */
    public function readCountList($originatorOrgRegNumber, $filePlanPosition = null, $archiveUnit = false);

    /*
        MODIFY ARCHIVES
    */
    /**
     * Suspend archives
     * @param array  $archiveIds Array of archive identifier
     * @param string $comment    The comment of modification
     * @param string $identifier Message identifier
     * @param string $format     Message format
     *
     * @action recordsManagement/archive/freeze
     *
     */
    public function updateFreeze($archiveIds, $comment = null, $identifier = null, $format = null);

    /**
     * Change the status of an archive
     * @param mixed  $archiveIds Array of archive identifier
     * @param string $comment    The comment of modification
     * @param string $identifier Message identifier
     * @param string $format     Message format
     *
     * @action recordsManagement/archive/unfreeze
     */
    public function updateUnfreeze($archiveIds, $comment = null, $identifier = null, $format = null);

    /**
     * Read the retention rule of multiple archives
     * @param mixed $archiveIds Array of archive identifier or sigle archive identifier
     *
     * @action recordsManagement/archive/editArchiveRetentionRule
     *
     */
    public function readRetentionrule($archiveIds);

    /**
     * Update a retention rule
     * @param recordsManagement/archiveRetentionRule $retentionRule The retention rule object
     * @param array                                  $archiveIds    The archives ids
     * @param string                                 $comment       The comment of modification
     * @param string                                 $identifier    Message identifier
     * @param string                                 $format        Message format
     *
     * @action recordsManagement/archive/modifyRetentionRule
     * @example /public/tests/updateArchivesRetentionRule-standard.json example-standard
     *
     */
    public function updateRetentionrule($retentionRule, $archiveIds, $comment = null, $identifier = null, $format = null);

    /**
     * Find archives
     * @param string $description The query string with arguments
     * @param string $text        The query string for text search
     * @param string $profile     The profile name
     * @param int    $limit       The result limit
     *
     * @action recordsManagement/archive/find
     *
     */
    public function readFind($description = '', $text = '', $profile = '', $limit = null);

    /**
     * Read the access rule of multiple archives
     * @param array $archiveIds Array of archive identifier or sigle archive identifier
     *
     * @action recordsManagement/archive/editArchiveAccessRule
     *
     */
    public function readAccessrule($archiveIds);

    /**
     * Update a access rule
     * @param recordsManagement/archiveAccessRule $accessRule The access rule object
     * @param array                               $archiveIds The archives ids
     * @param string                              $comment    The comment of modification
     * @param string                              $identifier Message identifier
     * @param string                              $format     Message format
     *
     * @action recordsManagement/archive/modifyAccessRule
     *
     */
    public function updateAccessrule($accessRule, $archiveIds = null, $comment = null, $identifier = null, $format = null);

    /*
        RESTITUTION
    */
    /**
     * Flag archives for restitution
     * @param array  $archiveIds Array of archive identifier
     * @param string $identifier The message reference
     * @param string $comment    A comment
     * @param string $format     The message format
     *
     * @action recordsManagement/archive/setForRestitution
     *
     */
    public function updateSetforrestitution($archiveIds, $identifier = null, $comment = null, $format = null);

    /*
        DESTRUCTION
    */
    /**
     * Flag archives for disposal
     * @param array  $archiveIds The archives ids
     * @param string $comment    The comment of modification
     * @param string $identifier Message identifier
     * @param string $format     The message format
     *
     * @return boolean
     *
     * @request UPDATE recordsManagement/dispose
     * @action recordsManagement/archive/dispose
     *
     */
    public function updateDisposearchives($archiveIds, $comment = null, $identifier = null, $format = null);

    /**
     * Delete disposable archives
     *
     * @return boolean
     *
     * @action recordsManagement/archive/destructDisposableArchives
     */
    public function deleteDisposablearchives();

    /**
     * Delete disposable archives
     * @param array $archiveIds The archives ids
     *
     * @return boolean
     *
     * @action recordsManagement/archive/destruct
     */
    public function deleteDisposearchives($archiveIds);

    /**
     * Cancel archives destruction
     * @param array $archiveIds Array of archive identifier
     *
     * @return boolean
     *
     * @request UPDATE recordsManagement/cancelDestruction
     * @action recordsManagement/archive/cancelDestruction
     *
     */
    public function updateCancelDestruction($archiveIds);

    /*
        Conversion
    */
    /**
     * Flag archives for conversion
     * @param array $documentIds Array of document identifier
     *
     * @action recordsManagement/archive/conversion
     */
    public function updateDocumentsconversion($documentIds);

    /*
        PRESERVATION
    */
    /**
     * Verify archives integrity
     * @param array $archiveIds Array of archive identifier
     *
     * @action recordsManagement/archive/verifyIntegrity
     *
     */
    public function readIntegritycheck($archiveIds);

    /*
     *  METADATA
     */
    /**
     * Update metadata of archive
     * @param string $archiveId
     * @param string $originatorArchiveId
     * @param string $archiverArchiveId
     * @param string $archiveName
     * @param date   $originatingDate
     * @param mixed  $description
     *
     * @action recordsManagement/archive/modifyMetadata
     */
    public function updateMetadata(
        $archiveId,
        $originatorArchiveId = null,
        $archiverArchiveId = null,
        $archiveName = null,
        $originatingDate = null,
        $description = null
    );

    /**
     * List an archive resources and children archives
     *
     * @action recordsManagement/archiveFilePlanPosition/listArchiveContents
     */
    public function readArchivecontents_archive_();


    /**
     * Move an archive into a folder
     * @param string $archiveId the archive identifier
     * @param string $folderId  The folder identifier
     *
     * @action recordsManagement/archiveFilePlanPosition/moveArchiveToFolder
     */
    public function udpateMovearchivetofolder($archiveId, $folderId = null);

    /**
     * Move an archive into a folder
     * @param array  $archiveIds   The archive identifier list
     * @param string $fromFolderId The originating folder identifier
     * @param string $toFolderId   The destination folder identifier
     *
     * @action recordsManagement/archiveFilePlanPosition/moveArchivesToFolder
     */
    public function udpateMovearchivestofolder($archiveIds, $fromFolderId = null, $toFolderId = null);

    /**
     * Index full text
     * @param int $limit The maximum number of archive to index
     *
     * @action recordsManagement/archive/indexFullText
     */
    public function updateIndexfulltext($limit = 200);


    /**
     * Update archive with changed retention rule
     * @param int $limit The maximum number of archive to update
     *
     * @action recordsManagement/archive/updateArchiveRetentionRule
     */
    public function updateArchivesretentionrule($limit = 500);

    /**
     * Retieve multiple archive from an array of archive Ids
     *
     * @param  array $archiveIds Array of archive Identifiers
     *
     * @action recordsManagement/archive/readFromIdentifiers
     */
    public function readArchives(array $archiveIds);

    /**
     * Extract full text from resources of flagged archives
     **
     * @action recordsManagement/archive/extractFulltext
     */
    public function readExtractfulltext();
}
