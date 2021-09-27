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
 * Interface for archive retrieval
 */
interface retrieveInterface
{
    /**
     * Search form
     *
     * @uses recordsManagement/archivalProfile/readIndex
     *
     * @return recordsManagement/archive/searchForm
     *
     * @throws records/exception/orgException organization/organization/noOriginatorException
     * @throws records/exception/defaultJson Exception
     */
    public function readRecordsmanagementArchivesSearchform();


    /**
     * get form to update index
     *
     * @return recordsManagement/archive/fulltextModificationForm
     */
    //public function readRecordsmanagementArchiveIndex_archiveId_modification();

    /**
     * Update archive indexes
     *
     * @uses recordsManagement/archive/updateIndex
     *
     * @return recordsManagement/archive/fulltextModificationResult
     */
    //public function updateRecordsmanagementArchiveModifyindex();

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
     * @param string  $description
     * @param string  $text
     * @param string  $archiverArchiveId
     * @param integer $maxResults
     * @param boolean $isDiscoverable
     *
     * @uses recordsManagement/archives/read
     * @uses recordsManagement/archives/readCount
     *
     * @return recordsManagement/archive/search
     */
    public function readRecordsmanagementArchives(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $description = null,
        $text = null,
        $archiverArchiveId = null,
        $maxResults = null,
        $isDiscoverable = false
    );

    /**
     * Get metadata to edit
     *
     * @return recordsManagement/archive/edit The recordsManagement/archive object
     * @uses  recordsManagement/archiveDescription/read_archiveId_
     */
    public function readRecordsmanagementArchivedescription_archiveId_Geteditmetadata();

    /**
     * Retrieve an archive content document (CDO)
     *
     * @return recordsManagement/archive/getContents
     *
     * @uses  recordsManagement/archive/readConsultation_archiveId_Digitalresource_resId_
     */
    public function readRecordsmanagementContents_archiveId__resId_();

    /**
     * Check if archive exists
     * @param string $archiveId The archive identifier
     *
     * @return recordsManagement/archive/exists
     *
     * @uses recordsManagement/archive/read_archiveId_Exists
     */
    public function readRecordsmanagementArchive_archiveId_Exists($archiveId);

    /**
     * Export archive and children
     *
     * @uses recordsManagement/archive/readExport_archiveId_
     * @return recordsManagement/archive/export
     */
    public function readRecordsmanagementExport_archiveId_();
}
