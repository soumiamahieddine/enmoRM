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

namespace presentation\maarchRM\UserStory\archiveRetrieval;

/**
 * Interface for archive retrieval
 */
interface retrieveInterface
{
    /**
     * Search form
     *
     * @return recordsManagement/archive/searchForm
     *
     * @uses recordsManagement/archivalProfile/readIndex
     *
     * @throws records/exception/orgException organization/organization/noOriginatorException
     * @throws records/exception/defaultJson Exception
     */
    public function readRecordsmanagementArchivesSearchform();

    /**
     * Search form
     *
     * @uses recordsManagement/archives/readFind
     *
     * @return recordsManagement/archive/fulltextSearchResult
     */
    public function readRecordsmanagementArchivesIndexresult();

    /**
     * get form to update index
     *
     * @return recordsManagement/archive/fulltextModificationForm
     */
    public function readRecordsmanagementArchiveIndex_archiveId_modification();

    /**
     * Update archive indexes
     *
     * @uses recordsManagement/archive/updateIndex
     *
     * @return recordsManagement/archive/fulltextModificationResult
     */
    public function updateRecordsmanagementArchiveModifyindex();

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
     * @return recordsManagement/archive/search
     * @uses recordsManagement/archives/read
     */
    public function readRecordsmanagementArchives($profileReference = null, $status = null, $archiveName = null, $agreementReference = null, $archiveId = null, $archiveExpired = null, $finalDisposition = null, $origniatorOrgRegNumber = null, $archiveIdOriginator = null);

    /**
     * View the archive
     *
     * @return recordsManagement/archive/getDescription The recordsManagement/archive object
     * @uses  recordsManagement/archiveDescription/read_archiveId_
     */
    public function readRecordsmanagementArchivedescription_archiveId_();

    /**
     * Retrieve an archive document by its id
     *
     * @return recordsManagement/archive/getContents
     * @uses  recordsManagement/archive/readDocument_docId_
     */
    public function readRecordsmanagementDocument_docId_();

        /**
     * Retrieve an archive resource by its id
     *
     * @return recordsManagement/archive/getContents
     * @uses  recordsManagement/archive/readDigitalResource_resId_
     */
    public function readRecordsmanagementDigitalresource_resId_();

    /**
     * Retrieve an archive content document (CDO)
     *
     * @return recordsManagement/archive/getContents
     *
     * @uses  recordsManagement/archive/readContents_archiveId__resId_
     */
    public function readRecordsmanagementContents_archiveId__resId_();

    /**
     * Retrieve an archive content document (CDO)
     * @param string $originatorArchiveId    The archive identifier of the originator
     * @param string $originatorOrgRegNumber The originatoriOrgRegNumber
     *
     * @return recordsManagement/archive/getContents
     *
     * @uses  recordsManagement/archive/readContentsbyoriginatorarchiveid_originatorArchiveId__originatorOrgRegNumber_
     */
    public function readRecordsmanagementContentsbyoriginatorarchiveid($originatorArchiveId, $originatorOrgRegNumber);

    /**
     * Check if archive exists
     * @param string $archiveId The archive identifier
     *
     * @return recordsManagement/archive/exists
     *
     * @uses recordsManagement/archive/read_archiveId_Exists
     */
    public function readRecordsmanagementArchive_archiveId_Exists($archiveId);
}
