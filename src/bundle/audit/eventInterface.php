<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle audit.
 *
 * Bundle audit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle audit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle audit.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\audit;

/**
 * Interface for audit
 */
interface eventInterface
{
    /**
     * Create new audit events
     * @param string $path      The path of called service
     * @param object  $variables The path variables
     * @param mixed  $input     The input data
     * @param mixed  $output    The output data
     * @param bool   $status    The result of action: success or failure (business exception)
     * @param mixed  $info      The info on caller process/client/system
     *
     * @action audit/event/add
     */
    public function create($path, array $variables = null, $input = null, $output = null, $status = false, $info =null);

    /**
     * Get search form for entries
     *
     * @param timestamp $fromDate  Start date
     * @param timestamp $toDate    End date
     * @param string    $event     Variables
     * @param string    $accountId Id of account
     * @param string    $status
     * @param string    $term      Term to search
     * @param integer   $maxResults Max results to display
     *
     * @action audit/event/search
     */
    public function readSearch($fromDate = null, $toDate = null, $event = null, $accountId = null, $status = null, $term = null, $maxResults = null);

    /**
     * Get count search for entries
     *
     * @param timestamp $fromDate   Start date
     * @param timestamp $toDate     End date
     * @param string    $event      Variables
     * @param string    $accountId  Id of account
     * @param string    $status
     * @param string    $term       Term to search
     *
     * @action audit/event/count
     */
    public function readCount($fromDate = null, $toDate = null, $event = null, $accountId = null, $status = null, $term = null);

    /**
     * Get search form for entries
     *
     * @action audit/event/getEvent
     */
    public function read_eventId_();

    /**
     * Chain the last journal
     *
     * @action audit/journal/chainJournal
     */
    public function createChainjournal();
}
