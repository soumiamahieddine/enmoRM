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

        $resChildren = array('success' => array(), 'error' => array());

        $archives = [];
        $archiveChildrenIds = [];

        foreach ($archiveIds as $archiveId) {
            $children = $childrenWithParent = $this->listChildrenArchiveId($archiveId);
            // Unset first element (it's the parent ID)
            unset($children[0]);

            foreach ($children as $child) {
                // If one of children is unable to change status, the parent is on error
                // and unset of the archivesIds to not change status
                if (!$this->checkStatus($child, 'restituable')) {
                    array_push($resChildren['error'], $archiveId);
                    unset($archiveIds[array_search($archiveId, $archiveIds)]);
                    break;
                } else {
                    $archiveChildrenIds = array_merge($archiveChildrenIds, $childrenWithParent);
                }
            }
        }

        foreach ($archiveChildrenIds as $archiveChildrenId) {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveChildrenId);
            $this->checkRights($archive);
            
            $archives[] = $archive;
        }
        
        foreach ($archives as $archive) {
            $this->logRestitutionRequest($archive);
        }

        $archiveList = $this->setStatus($archiveIds, 'restituable');
        $archiveList = array_merge_recursive($archiveList, $resChildren);

        return $archiveList;
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

        $archive = $this->retrieve((string)$archiveId, true);
        $this->logRestitution($archive);
        return $archive;
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
        $archiveList = $this->setStatus($archiveIds, 'preserved');
        foreach ($archiveIds as $archiveId) {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
            $this->logRestitutionRequest($archive);
        }
        return $archiveList;
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
            $archive = $this->retrieve((string)$archiveId, true);
            $destroyedArchives =  $this->destructArchive($archive);
            $archiveIds = array_diff($archiveIds, $destroyedArchives);
        }

        return true;
    }
}
