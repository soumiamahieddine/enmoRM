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
interface eventInterface
{

    /**
     * Search a journal event
     *
     * @param string    $eventType   The type of the event
     * @param string    $objectClass The class of the object
     * @param string    $objectId    The identifier of the object (event.objectId) OR on eventInfo (archive.originatorArchiveId, archivalProfile.reference, message.reference)
     * @param timestamp $minDate     The minimum date of the event
     * @param timestamp $maxDate     The maximum date of the event
     * @param string    $org         The org or org unit on event (event.orgRegNumber, event.orgUnitRegNumber) OR on eventInfo (archive.archiverOrgRegNumber, archive.originatorOrgRegNumber, message.senderOrgRegNumber, message.recipientOrgRegNumber)
     * @param integer   $maxResults  Limit numbr of results to return
     *
     * @action lifeCycle/journal/searchEvent
     */
    public function readSearch(
        $eventType = false,
        $objectClass = false,
        $objectId = false,
        $minDate = false,
        $maxDate = false,
        $org = false,
        $maxResults = null
    );

    /**
     * Count a journal event
     *
     * @param string    $eventType   The type of the event
     * @param string    $objectClass The class of the object
     * @param string    $objectId    The identifier of the object (event.objectId) OR on eventInfo (archive.originatorArchiveId, archivalProfile.reference, message.reference)
     * @param timestamp $minDate     The minimum date of the event
     * @param timestamp $maxDate     The maximum date of the event
     * @param string    $org         The org or org unit on event (event.orgRegNumber, event.orgUnitRegNumber) OR on eventInfo (archive.archiverOrgRegNumber, archive.originatorOrgRegNumber, message.senderOrgRegNumber, message.recipientOrgRegNumber)
     * @param integer   $maxResults  Limit numbr of results to return
     *
     * @action lifeCycle/journal/searchCount
     */
    public function readCount(
        $eventType = null,
        $objectClass = null,
        $objectId = null,
        $minDate = null,
        $maxDate = null,
        $org = null
    );

    /**
     * Get an events by id
     *
     * @action lifeCycle/journal/getEventFromJournal
     *
     */
    public function read_eventId_();

    /**
     * Get eventType list
     *
     * @action lifeCycle/journal/listEventType
     *
     */
    public function readEventtypelist();
}
