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

namespace bundle\recordsManagement\Controller;

/**
 * Interface for archive description class control
 *
 * @package RecordsManagement
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 */
interface archiveDescriptionInterface
{
    /**
     * Validate the description object
     * @param object $description
     * 
     * @return bool
     */
    //public function validate($description);

    /**
     * Create the description object
     * @param object $archive
     */
    public function create($archive);

    /**
     * Retrieve the description object
     * @param id $archiveId
     */
    public function read($archiveId);

    /**
     * Search the description objects
     * @param string $description The search args on description object
     * @param string $text        The search args on text
     * @param array  $args        The search args on archive std properties
     */
    public function search($description=null, $text=null, array $args=[]);

    /**
     * Update the description object
     * @param object $archive
     */
    public function update($archive);

    /**
     * Delete the description object
     * @param id   $archiveId
     * @param bool $deleteDescription
     */
    public function delete($archiveId, $deleteDescription = true);
} // END class
