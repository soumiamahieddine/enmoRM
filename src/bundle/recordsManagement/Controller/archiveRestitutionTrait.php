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
 * Trait for archives restitution
 */
trait archiveRestitutionTrait
{
    /**
     * Flag for restitution
     * @param array $archiveIds     Array of archive identifier
     *
     * @return array The result of the operation
     */
    public function setForRestitution($archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $canditates = $this->setStatus($archiveIds, 'restituable');

        return $canditates;
    }

    /**
     * Restitute an archive
     * @param string $archiveId The idetifier of the archive
     *
     * @return recordsManagement/archive The restitue archive
     */
    public function restitute($archiveId)
    {
        $this->verifyIntegrity($archiveId);

        $archive = $this->retrieve($archiveId);

        $statusChanged = $this->setStatus((string) $archive->archiveId, "restituted");

        $valid = count($statusChanged["success"]) ? true : false;

        // Life cycle journal
        $this->logRestitution($archive, $valid);

        return $valid ? $archive : null;
    }

    /**
     * Validate the restitution restitution
     * @param array $archiveIds Array of archive identifier
     *
     * @return bool The result of the operation
     */
    public function validateRestitution($archiveIds)
    {
        return $this->setStatus($archiveIds, 'restituted');
    }

    /**
     * Cancel restitution
     * @param array $archiveIds Array of archive identifier
     *
     * @return bool The result of the operation
     */
    public function cancelRestitution($archiveIds)
    {
        return $this->setStatus($archiveIds, 'preserved');
    }

    /**
     * Destruct restituted resource
     * @param id $archiveIds The archive identifier or identifier list
     *
     * @return bool The result of the operation
     */
    public function destructRestituted($archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        foreach ($archiveIds as $archiveId) {
            $archive = $this->retrieve($archiveId);
            $destroyedArchives =  $this->destructArchive($archive);
            $archiveIds = array_diff($archiveIds, $destroyedArchives);
        }

        return true;
    }
}
