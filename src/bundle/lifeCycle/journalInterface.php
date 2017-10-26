<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle lifeCycle.
 *
 * Bundle lifeCycle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle lifeCycle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle lifeCycle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\lifeCycle;

/**
 * Class of archives life cycle journal
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
interface journalInterface
{

    /**
     * Read the list of journals
     *
     * @action lifeCycle/journal/getJournalList
     *
     */
    public function readList();

    /**
     * Get the current journal
     * @param id      $journalId The journal identifier
     * @param integer $offset    The reading offset
     * @param integer $limit     The maximum number of event to load
     *
     * @action lifeCycle/journal/readJournal
     *
     */
    public function read_journalId_($journalId, $offset = 0, $limit = 300);

    /**
     * Chain the last journal
     *
     * @action lifeCycle/journal/chainJournal
     */
    public function createChainjournal();

    /**
     * Check integrity 
     * @param string $archiveId
     * 
     * @action lifeCycle/journal/checkIntegrity
     */
    public function checkIntegrity($archiveId);
}
