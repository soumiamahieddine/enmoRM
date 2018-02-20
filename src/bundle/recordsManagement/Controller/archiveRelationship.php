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
 * Class for archive relationship
 *
 * @package RecordsManagement
 * @author  Alexis Ragot Maarch <alexis.ragot@maarch.org>
 */
class archiveRelationship
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
     * New empty relationship
     *
     * @return recordsManagement/archiveRelationship The archival relationship object
     */
    public function newRelationship()
    {
        return \laabs::newInstance("recordsManagement/archiveRelationship");
    }

    /**
     * Create a relationship
     * @param recordsManagement/archiveRelationship $archiveRelationship The archive relationship object
     *
     * @throws unknownArchive
     * @throws sameArchivesException
     *
     * @return recordsManagement/archiveRelationship The archiveRelationship
     */
    public function create($archiveRelationship)
    {
        if ($this->sdoFactory->exists("recordsManagement/archiveRelationship", $archiveRelationship)) {
            throw new \bundle\recordsManagement\Exception\archiveRelationshipException();
        }
        if ($archiveRelationship->relatedArchiveId == $archiveRelationship->archiveId) {
            throw new \bundle\recordsManagement\Exception\sameArchivesException();
        }
        if (!$this->sdoFactory->exists("recordsManagement/archive", $archiveRelationship->relatedArchiveId)) {
            throw new \bundle\recordsManagement\Exception\unknownArchive($archiveRelationship->relatedArchiveId);
        }
        
        $this->sdoFactory->create($archiveRelationship, "recordsManagement/archiveRelationship");

        return $archiveRelationship;
    }

    /**
     * Delete a relationship
     * @param recordsManagement/archiveRelationship $archiveRelationship The archive relationship object
     *
     * @return recordsManagement/archiveRelationship The archiveRelationship
     */
    public function delete($archiveRelationship)
    {
        $archiveRelationship = \laabs::castCollection(get_object_vars($archiveRelationship), 'recordsManagement/archiveRelationship');
        $this->sdoFactory->delete($archiveRelationship);

        return true;
    }

    /**
     * Get archive relationships
     *
     * @param string $archiveId The archive identifier
     *
     * @return array Array of recordsManagement/archiveRelationShip object
     */
    public function getByArchiveId($archiveId)
    {
        $archiveRelationships = $this->sdoFactory->find("recordsManagement/archiveRelationship", "archiveId='$archiveId'");

        return $archiveRelationships;
    }

    /**
     * Get archive relationships by related archive identifier
     *
     * @param string $relatedArchiveId The related archive identifier
     *
     * @return array Array of recordsManagement/archiveRelationShip object
     */
    public function getByRelatedArchiveId($relatedArchiveId)
    {
        $archiveRelationships = $this->sdoFactory->find("recordsManagement/archiveRelationship", "relatedArchiveId='$relatedArchiveId'");

        return $archiveRelationships;
    }
}
