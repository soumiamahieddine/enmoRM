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
     * @param string $profileReference
     * @param string $status
     * @param string $archiveName
     * @param string $agreementReference
     * @param string $archiveId
     * @param string $archiveExpired
     * @param string $finalDisposition
     * @param string $origniatorOrgRegNumber
     * @param string $archiveIdOriginator
     *
     * @action recordsManagement/archive/search
     *
     */
    public function read($profileReference = null, $status = null, $archiveName = null, $agreementReference = null, $archiveId = null, $archiveExpired = null, $finalDisposition = null, $origniatorOrgRegNumber = null, $archiveIdOriginator = null);

    /*
        MODIFY ARCHIVES
    */
    /**
     * Suspend archives
     * @param array  $archiveIds  Array of archive identifier
     * @param string $comment     The comment of modification
     * @param string $identifiant Message identifiant
     *
     * @action recordsManagement/archive/freeze
     *
     */
    public function updateFreeze($archiveIds, $comment = null, $identifiant = null);

    /**
     * Change the status of an archive
     * @param mixed  $archiveIds  Array of archive identifier
     * @param string $comment     The comment of modification
     * @param string $identifiant Message identifiant
     *
     * @action recordsManagement/archive/unfreeze
     */
    public function updateUnfreeze($archiveIds, $comment = null, $identifiant = null);

    /**
     * Read the retention rule of multiple archives
     * @param array $archiveIds Array of archive identifier or sigle archive identifier
     *
     * @action recordsManagement/archive/editArchiveRetentionRule
     *
     */
    public function readRetentionrule($archiveIds);

    /**
     * Update a retention rule
     * @param recordsManagement/archiveRetentionRule $retentionRule The retention rule object
     * @param array                                  $archiveIds    The archives ids
     *
     * @action recordsManagement/archive/modifyRetentionRule
     *
     */
    public function updateRetentionrule($retentionRule, $archiveIds);

    /**
     * Find archives
     * @param string $q     The query string
     * @param string $index The index name
     * @param int    $limit The result limit
     *
     * @action recordsManagement/archive/find
     *
     */
    public function readFind($q, $index = false, $limit = null);

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
     * @param recordsManagement/archiveAccessRule $accessRule  The retention rule object
     * @param array                               $archiveIds  The archives ids
     * @param string                              $comment     The comment of modification
     * @param string                              $identifiant Message identifiant
     *
     * @action recordsManagement/archive/modifyAccessRule
     *
     */
    public function updateAccessrule($accessRule, $archiveIds = null, $comment = null, $identifiant = null);

    /*
        RESTITUTION
    */
    /**
     * Flag archives for restitution
     * @param array  $archiveIds  Array of archive identifier
     * @param string $identifiant The message reference
     * @param string $comment     A comment
     *
     * @action recordsManagement/archive/setForRestitution
     *
     */
    public function updateSetforrestitution($archiveIds, $identifiant = null, $comment = null);

    /*
        DESTRUCTION
    */
    /**
     * Flag archives for disposal
     * @param array  $archiveIds  The archives ids
     * @param string $comment     The comment of modification
     * @param string $identifiant Message identifiant
     *
     * @return boolean
     *
     * @request UPDATE recordsManagement/dispose
     * @action recordsManagement/archive/dispose
     *
     */
    public function updateDisposearchives($archiveIds, $comment = null, $identifiant = null);

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

    /**
     * Flag archives for conversion
     *
     * @action recordsManagement/archive/convert
     */
    public function updateDocumentsconvert_documentId__resId_();

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
}
