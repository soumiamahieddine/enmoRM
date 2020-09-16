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
 * Standard interface for log archive description class
 */
interface logInterface
{
    /**
     * Get a  search result
     * @param string  $archiveId   The archive identifier
     * @param string  $type        The type
     * @param date    $fromDate    The date
     * @param date    $toDate      The date
     * @param string  $processName The process name
     * @param string  $processId   The process identifier
     * @param string  $sortBy      The process identifier
     * @param integer $maxResults  Max number of results to return

     * @action recordsManagement/log/find
     */
    public function readFind(
        $archiveId = null,
        $type = null,
        $fromDate = null,
        $toDate = null,
        $processName = null,
        $processId = null,
        $sortBy = ">fromDate",
        $maxResults = null
    );

    /**
     * Count search results
     *
     * @param string  $archiveId   The archive identifier
     * @param string  $type        The type
     * @param date    $fromDate    The date
     * @param date    $toDate      The date
     * @param string  $processName The process name
     * @param string  $processId   The process identifier

     * @action recordsManagement/log/countFind
     */
    public function countFind(
        $archiveId = null,
        $type = null,
        $fromDate = null,
        $toDate = null,
        $processName = null,
        $processId = null
    );

    /**
     * Deposit a log file
     * @param string    $journalFileName   The name of the journal to deposit
     * @param timestamp $fromDate          The journal start date
     * @param timestamp $toDate            The journal end date
     * @param string    $type              The tye of the journal (system, lifeCycle, application)
     * @param string    $processName       The journal process name
     * @param string    $timestampFileName The name of the timestamp file
     *
     * @action recordsManagement/log/depositJournal
     */
    public function createDepositjournal(
        $journalFileName,
        $fromDate,
        $toDate,
        $type,
        $processName = null,
        $timestampFileName = null
    );

    /**
     * @param string $archiveId     Archiver identifier
     * @param string $resourceId    Resource identifier
     *
     * @action recordsManagement/log/contents
     */
    public function contents_type__archiveId__resourceId_();
}
