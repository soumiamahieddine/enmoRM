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
     * Controller for life cycle journal events
     * @var recordsManagement/Controller/lifeCycleJournal
     */
    protected $lifeCycleJournalController;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->lifeCycleJournalController = \laabs::newController("lifeCycle/journal");
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

        try {
            $this->sdoFactory->create($archiveRelationship, "recordsManagement/archiveRelationship");
        } catch (Exception $e) {
            throw new \Exception("Error Processing Request", 1);
        }

        $this->logEvent($archiveRelationship, 'recordsManagement/addRelationship');

        return $archiveRelationship;
    }

    public function update($archiveRelationship)
    {
        try {
            $relation = $this->sdoFactory->update($archiveRelationship, "recordsManagement/archiveRelationship");
        } catch (Exception $e) {
            throw new \Exception("Error Processing Request", 1);
        }

        $this->logEvent($archiveRelationship, 'recordsManagement/updateRelationship');

        return $relation;
    }

    /**
     * Delete a relationship
     * @param recordsManagement/archiveRelationship $archiveRelationship The archive relationship object
     *
     * @return boolean The archiveRelationship
     */
    public function delete($archiveRelationship)
    {
        //$archiveRelationship = \laabs::castCollection(get_object_vars($archiveRelationship), 'recordsManagement/archiveRelationship');

        try {
            $this->sdoFactory->delete($archiveRelationship);
        } catch (Exception $e) {
            throw new \Exception("Error Processing Request", 1);
        }

        $this->logEvent($archiveRelationship, 'recordsManagement/deleteRelationship');

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

        foreach ($archiveRelationships as $relationship) {
            $this->decodeDescription($relationship);
        }

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

        foreach ($archiveRelationships as $relationship) {
            $this->decodeDescription($relationship);
        }

        return $archiveRelationships;
    }

    public function getUniqueRelationship($archiveId, $relatedArchiveId)
    {
        return $this->sdoFactory->find("recordsManagement/archiveRelationship", "archiveId='$archiveId' AND relatedArchiveId='$relatedArchiveId'");
    }

    protected function decodeDescription($relationship)
    {
        $relationship->description = json_decode($relationship->description);
    }

    protected function logEvent($archiveRelationship, $event)
    {
        $archiveController = \laabs::newController('recordsManagement/archive');
        $archive = $archiveController->read($archiveRelationship->archiveId);
        $relatedArchive = $archiveController->read($archiveRelationship->relatedArchiveId);

        $archiveEventInfo = [
            'resId' => (string) $archiveRelationship->archiveId,
            'hashAlgorithm' => '',
            'hash' => '',
            'address' => '',
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'relatedArchiveId' => (string) $archiveRelationship->relatedArchiveId
        ];

        $relatedArchiveEventInfo = [
            'resId' => (string) $archiveRelationship->relatedArchiveId,
            'hashAlgorithm' => '',
            'hash' => '',
            'address' => '',
            'originatorOrgRegNumber' => $relatedArchive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $relatedArchive->archiverOrgRegNumber,
            'relatedArchiveId' => (string) $archiveRelationship->archiveId
        ];

        // Add relationship for archive to link
        $this->lifeCycleJournalController->logEvent(
            $event,
            'recordsManagement/archive',
            $archiveRelationship->archiveId,
            array_merge($archiveEventInfo, get_object_vars($archive)),
            true
        );

        // Add relationship event for archive which is link with archive source
        $this->lifeCycleJournalController->logEvent(
            $event,
            'recordsManagement/archive',
            $archiveRelationship->relatedArchiveId,
            array_merge($relatedArchiveEventInfo, get_object_vars($relatedArchive)),
            true
        );
    }
}
