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
interface eventFormatInterface
{
    /**
     * Get event format list
     *
     * @action lifeCycle/eventFormat/index
     */
    public function readEventformatlist();

    /**
     * Edit an event format
     * @param string $eventFormatType The event Format type
     * 
     * @action lifeCycle/eventFormat/edit
     */
    public function readEventformatEdit($eventFormatType);

    /**
     * Create an event format
     * @param lifeCycle/eventFormat $eventFormat The eventFormat object to record
     *
     * @action lifeCycle/eventFormat/create
     */
    public function createEventformat($eventFormat);

    /**
     * update an event format
     * @param lifeCycle/eventFormat $eventFormat The eventFormat object to record
     * 
     * @action lifeCycle/eventFormat/update
     */
    public function updateEventformat($eventFormat);
    
    /**
     * Delete an event format
     * @param qname $type The type of event format to remove
     *
     * @action lifeCycle/eventFormat/delete
     */
    public function deleteEventformat_type_();
}
