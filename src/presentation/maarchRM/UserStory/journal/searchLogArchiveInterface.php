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
namespace presentation\maarchRM\UserStory\journal;

/**
 * User story of archive log search
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface searchLogArchiveInterface
{
    /**
     * @return recordsManagement/log/search/form
     */
    public function readLogSearch();

    /**
     * @uses recordsManagement/log/readFind
     * @uses recordsManagement/log/countFind
     *
     * @return recordsManagement/log/find
     */
    public function readLogs();

    /**
     *
     * @param string    $archiveId      Archive identifier
     * @param string    $resourceId     Resource identifier
     *
     * @return recordsManagement/log/contents
     * @uses recordsManagement/log/contents_type__archiveId__resourceId_
     */
    public function readLogContents_type__archiveId__resourceId_();

    /**
     * Check integrity of log
     * @param string $archiveId
     *
     * @uses lifeCycle/journal/checkIntegrity
     * @return recordsManagement/log/checkIntegrity
     */
    public function readJournal_journalId_Checkintegrity($archiveId);

    /**
     * View the archive
     *
     * @return recordsManagement/archive/getDescription The recordsManagement/archive object
     * @uses  recordsManagement/archiveDescription/read_archiveId_
     */
    public function readRecordsmanagementArchivedescription_archiveId_();

    /**
     * Retrieve an archive content document (CDO)
     *
     * @return recordsManagement/archive/getContents
     *
     * @uses  recordsManagement/archive/readConsultation_archiveId_Digitalresource_resId_
     */
    public function readRecordsmanagementContents_archiveId__resId_();
}
