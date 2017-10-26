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
namespace presentation\maarchRM\UserStory\adminTech;

/**
 * User story admin event type
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface adminEventFormatInterface
{
    /**
     * Read list of event format
     * @uses lifeCycle/eventFormat/readEventformatlist
     *
     * @return lifeCycle/eventFormat/index
     */
    public function readLifecycleEventsformats();
    
    /**
     * Edit an event format
     * @param string $eventFormatType The event Format type
     * 
     * @uses lifeCycle/eventFormat/readEventformatEdit
     *
     * @return lifeCycle/eventFormat/edit
     */
    public function readLifecycleEventformat($eventFormatType);

    /**
     * Create an event format
     * @param lifeCycle/eventFormat $eventFormat The eventFormat object to record
     *
     * @uses lifeCycle/eventFormat/createEventformat
     * @return lifeCycle/eventFormat/create
     */
    public function createLifecycleEventformat($eventFormat);

    /**
     * Update an event format
     * @param lifeCycle/eventFormat $eventFormat The eventFormat object to record
     * 
     * @uses lifeCycle/eventFormat/updateEventformat
     * @return lifeCycle/eventFormat/update
     */
    public function updateLifecycleEventformat($eventFormat);
}