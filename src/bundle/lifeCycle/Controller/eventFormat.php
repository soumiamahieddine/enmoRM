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

namespace bundle\lifeCycle\Controller;

/**
 * Class event format
 *
 * @author Alexis RAGOT <alexis.ragot@maarch.org>
 */
class eventFormat
{
    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get event format list
     *
     * @return array The event format list
     */
    public function index()
    {
        return $this->sdoFactory->find("lifeCycle/eventFormat", null, null, "type");
    }

    /**
     * Get event format by type
     * @param qname $type The evnet type
     *
     * @return lifeCycle/eventFormat The event format
     */
    public function read($type)
    {
        return $this->sdoFactory->read("lifeCycle/eventFormat", $type);
    }

    /**
     * Update an event format
     * @param string $eventFormatType The event Format type
     *
     * @return lifeCycle/eventFormat The event format object
     */
    public function edit($eventFormatType)
    {
        return $eventFormat = $this->sdoFactory->read("lifeCycle/eventFormat", $eventFormatType);
    }
    
    /**
     * Create an event format
     * @param lifeCycle/eventFormat $eventFormat The event format object to records
     *
     * @return lifeCycle/eventFormat The event format object
     */
    public function create($eventFormat)
    {
        return $this->sdoFactory->create($eventFormat, "lifeCycle/eventFormat");
    }
    
    /**
     * Update an event format
     * @param lifeCycle/eventFormat $eventFormat The eventFormat object to record
     *
     * @return lifeCycle/eventFormat The event format object
     */
    public function update($eventFormat)
    {
        return $this->sdoFactory->update($eventFormat, "lifeCycle/eventFormat");
    }

    /**
     * Delete an event format
     * @param qname $type The type of event format to remove
     *
     * @return lifeCycle/eventFormat The event format object
     */
    public function delete($type)
    {
        $eventFormat = $this->sdoFactory->read("lifeCycle/eventFormat", $type);

        $this->sdoFactory->delete($eventFormat, "lifeCycle/eventFormat");
    }
}
