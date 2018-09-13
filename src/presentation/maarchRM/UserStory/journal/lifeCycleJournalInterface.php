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
 * User story of life cycle journal
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface lifeCycleJournalInterface
{
    /**
     * Show the event search form
     *
     * @return lifeCycle/journal/searchForm
     */
    public function readJournalSearch();

    /**
     * Search a journal event
     * @param string $eventType     The type of the event
     * @param string $objectClass   The class of the object
     * @param string $objectId      The identifier of the object
     * @param string $minDate       The minimum date of the event
     * @param string $maxDate       The maximum date of the event
     *
     * @return lifeCycle/journal/searchEvent
     * @uses lifeCycle/event/readSearch
     */
    public function readJournals($eventType = false, $objectClass = false, $objectId = false, $minDate = false, $maxDate = false);

    /**
     * Get the current journal
     * @param id      $journalId The identifier of the journal
     * @param integer $offset    The reading offset
     * @param integer $limit     The maximum number of event to load
     *
     * @return lifeCycle/journal/readJournal
     *
     * @uses lifeCycle/journal/read_journalId_
     *
     */
    public function readJournal_journalId_($journalId, $offset = 0, $limit = 300);
}