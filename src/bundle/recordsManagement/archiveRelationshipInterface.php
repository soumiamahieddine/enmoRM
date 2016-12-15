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
/* 
 * Copyright (C) 2015 Alexis Ragot <alexis.ragot@maarch.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Interface for archive relationship
 *
 * @package RecordsMangement
 * @author  Alexis Ragot Maarch <alexis.ragot@maarch.org>
 */
interface archiveRelationshipInterface
{
   
    /**
     * New empty relationship
     *
     * @return recordsManagement/archivalRelationship The archival relationship object
     *
     * @request READ recordsManagement/relationship
     * @view recordsManagement/relationship/edit
     */
    public function newRelationship();
    
    /**
     * Create a relationship
     * @param recordsManagement/archiveRelationShip $archiveRelationship The archive relationship object
     *
     * @return boolean
     *
     * @request CREATE recordsManagement/relationship
     * @action recordsManagement/archive/addRelationship
     */
    public function createRelationship($archiveRelationship);
    
    /**
     * Delete a relationship
     * @param recordsManagement/archiveRelationship $archiveRelationship The archive relationship object
     *
     * @return boolean
     *
     * @request DELETE recordsManagement/relationship
     * @action recordsManagement/archive/deleteRelationship
     */
    public function deleteRelationship($archiveRelationship);
    
    /**
     * Get archive relationships
     * @param string $archiveId The archive identifier
     *
     * @return array Array of recordsManagement/archiveRelationShip object
     *
     * @request READ recordsManagement/relationship/([^\/]+)
     */
    public function getByArchiveId($archiveId);
}