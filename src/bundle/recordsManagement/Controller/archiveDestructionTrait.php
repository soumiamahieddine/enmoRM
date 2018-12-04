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
 * Trait for archives destruction
 */
trait archiveDestructionTrait
{
    /**
     * Flag for disposal
     * @param array $archiveIds The archives ids
     *
     * @return bool The result of the operation
     */
    public function dispose($archiveIds)
    {
        $archives = [];

        foreach ($archiveIds as $archiveId) {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

            $this->checkRights($archive);

            $this->checkDisposalRights($archive) ;

            $this->listChildrenArchive($archive, true);

            if ($archive->childrenArchives) {
                $archiveChildrenIds = $this->checkChildren($archive->childrenArchives);
                $archiveIds = array_merge($archiveIds, $archiveChildrenIds);
            }
            $archives[] = $archive;
        }

        $archiveList = $this->setStatus($archiveIds, 'disposable');
        foreach ($archives as $archive) {
            $this->logDestructionRequest($archive);
        }

        return $archiveList;
    }

    /**
     * Eliminate archive
     * @param string $archiveId The archive identifier
     *
     * @return bool The result of the operation
     */
    public function eliminate($archiveId)
    {
        $archive = $this->retrieve((string)$archiveId);

        $result = $this->setStatus($archiveId, 'disposed');

        $eventItems = array(
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
        );

        $this->logElimination($archive);

        return $result;
    }

    /**
     * Cancel destruction
     * @param array $archiveIds Array of archive identifier
     *
     * @return bool The result of the operation
     */
    public function cancelDestruction($archiveIds)
    {
        foreach ($archiveIds as $archiveId) {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
            $this->logDestructionRequestCancel($archive);
        }

        return $this->setStatus($archiveIds, 'preserved');
    }

    /**
     * Delete archive and his related archive
     * @param id $archiveIds The archive identifier or an identifier list
     *
     * @return recordsManagement/archive[] The destroyed archives
     */
    public function destructDisposableArchives()
    {
        $archiveIds = $this->sdoFactory->index('recordsManagement/archive', "archiveId", "status = 'disposable'");
        $this->setStatus($archiveIds, 'disposed');

        $destructResult = $this->destruct($archiveIds);
        $res = [];
        $res['success'] = [];
        $res['error'] = [];

        foreach ($destructResult['success'] as $archive) {
            $res['success'][] = $archive->archiveId;
        }

        foreach ($destructResult['error'] as $archive) {
            $res['error'][] = $archive->archiveId;
        }

        return $res;
    }

    /**
     * Delete archive and his related archive
     * @param id $archiveIds The archive identifier or an identifier list
     *
     * @return recordsManagement/archive[] The destroyed archives
     */
    public function destruct($archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $archives = $this->verifyIntegrity($archiveIds);
        
        $destructArchives = [];
        $destructArchives['error'] = $archives['error'];
        $destructArchives['success'] = [];

        foreach ($archives['success'] as $archiveId) {
            $archive = $this->retrieve((string)$archiveId);

            if ($archive->status != 'disposed' && $archive->status != 'restituted' && $archive->status != 'transfered') {
                $destructArchives['error'][] = $archive;
                continue;
            }

            try {
                $this->destructArchive($archive);

                $destructionResult = true;
            } catch (\Exception $e) {
                $destructionResult = false;
                continue;
            }

            $this->logDestruction($archive);

            $destructArchives['success'][] = $archive;
        }

        return $destructArchives;
    }

    /**
     * Destruct an archive
     * @param recordsManagement/archive $archive The archive
     *
     * @return array the destroyed archives identifiers
     **/
    private function destructArchive($archive)
    {
        $destroyedArchiveId = array();

        // Load agreement, profile and service level
        $this->useReferences($archive, 'destruction');

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            // Delete description
            if ($archive->descriptionClass) {
                $this->currentDescriptionController->delete($archive->archiveId, $this->deleteDescription);
            }

            // Children archives
            $childrenArchives = $this->sdoFactory->readChildren('recordsManagement/archive', $archive);
            if (count($childrenArchives)) {
                foreach ($childrenArchives as $child) {
                    $destroyedArchiveId = array_merge($this->destructArchive($child), $destroyedArchiveId);
                }
            }

            if (empty($archive->digitalResources)) {
                $archive->digitalResources = $this->digitalResourceController->getResourcesByArchiveId($archive->archiveId);
            }

            foreach ($archive->digitalResources as $digitalResource) {
                $this->digitalResourceController->delete($digitalResource->resId);
            }

            if ($this->deleteDescription) {
                // Relationship
                $relationships = $this->sdoFactory->readChildren('recordsManagement/archiveRelationship', $archive);
                foreach ($relationships as $relationship) {
                    $this->sdoFactory->delete($relationship);
                }
                $this->sdoFactory->delete($archive);
            }

            $destroyedArchiveId[] = $archive->archiveId;

        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }
            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $destroyedArchiveId;
    }

    protected function checkChildren($archivesChildren)
    {
        $archiveIds = [];

        foreach ($archivesChildren as $archive) {
            $this->checkDisposalRights($archive, true) ;

            if ($archive->childrenArchives) {
                $archiveChildrenIds = $this->checkChildren($archive->childrenArchives);
                $archiveIds = array_merge($archiveIds, $archiveChildrenIds);
            }

            $archiveIds[] = (string) $archive->archiveId;
        }

        return $archiveIds;
    }

    public function checkDisposalRights($archive, $isChild = false)
    {
        $archiveIds = [];

        $this->checkRights($archive);
        $currentDate = \laabs::newTimestamp();

        $beforeError = ($isChild) ? "Children archives : ": "";

        if (isset($archive->finalDisposition) && $archive->finalDisposition != "destruction") {
            throw new \bundle\recordsManagement\Exception\notDisposableArchiveException($beforeError."Archive not set for destruction.");
        }

        if (isset($archive->disposalDate) && $archive->disposalDate > $currentDate) {
            throw new \bundle\recordsManagement\Exception\notDisposableArchiveException($beforeError."Disposal date not reached.");
        }

        if (empty($archive->disposalDate) && (isset($archive->retentionRuleCode) || isset($archive->retentionDuration))) {
            throw new \bundle\recordsManagement\Exception\notDisposableArchiveException($beforeError."Disposal date not reached.");
        }

        //if finaldisposition is not null or empty
        if (empty($archive->finalDisposition) || is_null($archive->finalDisposition)) {
            throw new \bundle\recordsManagement\Exception\notDisposableArchiveException($beforeError."Final disposition must be advised for this action");
        }

        //if retention is not null or empty
        if (empty($archive->retentionStartDate) || is_null($archive->retentionStartDate)) {
            throw new \bundle\recordsManagement\Exception\notDisposableArchiveException($beforeError."Retention Start date must be advised for this action.");
        }

        return $archiveIds ;
    }
}
