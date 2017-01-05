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
     * @return bool
     */
    public function dispose($archiveIds)
    {
        $currentDate = \laabs::newTimestamp();

        foreach ($archiveIds as $archiveId) {
            $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
            $this->checkRights($archive);

            if ($archive->finalDisposition != "destruction") {
                throw new \bundle\recordsManagement\Exception\notDisposableArchiveException("Archive not set for destruction.");
            }

            if ($archive->disposalDate > $currentDate) {
                throw new \bundle\recordsManagement\Exception\notDisposableArchiveException("Disposal date not reached.");
            }
        }

        $archiveList = $this->setStatus($archiveIds, 'disposable');

        return $archiveList;
    }

    /**
     * Eliminate archive
     * @param string $archiveId The archive identifier
     *
     * @return bool
     */
    public function eliminate($archiveId)
    {
        $archive = $this->getDescription($archiveId);

        $result = $this->setStatus($archiveId, 'disposed');

        $eventItems = array(
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
        );

        $logged = false;
        if (isset($archive->digitalResources) && count($archive->digitalResources)) {
            foreach ($archive->digitalResources as $digitalResource) {
                $eventInfo['resId'] = (string) $digitalResource->resId;
                $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                $eventInfo['hash'] = $digitalResource->hash;
                $eventInfo['address'] = $digitalResource->address[0]->path;

                $event = $this->lifeCycleJournalController->logEvent('recordsManagement/elimination', 'recordsManagement/archive', $archive->archiveId, $eventInfo);

                $logged = true;
            }
        }

        if (!$logged) {
            $eventInfo['resId'] = $eventInfo['hashAlgorithm'] = $eventInfo['hash'] = null;
            $eventInfo['address'] = $archive->storagePath;
            $event = $this->lifeCycleJournalController->logEvent('recordsManagement/elimination', 'recordsManagement/archive', $archive->archiveId, $eventInfo);
        }


        return $result;
    }

    /**
     * Cancel destruction
     * @param array $archiveIds Array of archive identifier
     *
     * @return bool
     */
    public function cancelDestruction($archiveIds)
    {
        return $this->setStatus($archiveIds, 'preserved');
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

        $archives = array('success' => [], 'error' => []);

        if (\laabs::hasDependency("fulltext")) {
            $fulltextController = \laabs::newController("recordsManagement/fulltext");
            $document = \laabs::newService('dependency/fulltext/Document');
        }

        foreach ($archiveIds as $archiveId) {
            $archive = $this->getDescription($archiveId);

            if ($archive->status != 'disposed') {
                $archives['error'][] = $archive;
                continue;
            }

            try {
                $this->destructArchive($archive);

                if (\laabs::hasDependency("fulltext")) {
                    $documentToRemove = clone($document);
                    $documentToRemove->addField("archiveId", $archiveId, "name");
                    $fulltextController->delete($archive->archivalProfileReference, $documentToRemove);
                }

                $destructionResult = true;
            } catch (\Exception $e) {
                $destructionResult = false;
                continue;
            }

            $eventInfo['archiverOrgRegNumber'] = $archive->archiverOrgRegNumber;
            $eventInfo['originatorOrgRegNumber'] = $archive->originatorOrgRegNumber;

            $logged = false;
            if (isset($archive->digitalResources) && count($archive->digitalResources)) {
                foreach ($archive->digitalResources as $digitalResource) {
                    $eventInfo['resId'] = (string) $digitalResource->resId;
                    $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                    $eventInfo['hash'] = $digitalResource->hash;
                    $eventInfo['address'] = $digitalResource->address[0]->path;

                    $event = $this->lifeCycleJournalController->logEvent('recordsManagement/destruction', 'recordsManagement/archive', $archive->archiveId, $eventInfo, $destructionResult);

                    $logged = true;
                }
            }

            if (!$logged) {
                $eventInfo['resId'] = $eventInfo['hashAlgorithm'] = $eventInfo['hash'] = null;
                $eventInfo['address'] = $archive->storagePath;
                $event = $this->lifeCycleJournalController->logEvent('recordsManagement/destruction', 'recordsManagement/archive', $archive->archiveId, $eventInfo, $destructionResult);
            }

            $archives['success'][] = $archive;
        }

        return $archives;
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
            if (isset($archive->descriptionClass) && isset($archive->descriptionId)) {
                $this->currentDescriptionController->delete($archive->descriptionId, $this->deleteDescription);
            }

            // Children archives
            $childrenArchives = $this->sdoFactory->readChildren('recordsManagement/archive', $archive);
            if (count($childrenArchives)) {
                foreach ($childrenArchives as $child) {
                    $destroyedArchiveId = array_merge($this->destructArchive($child), $destroyedArchiveId);
                }
            }

            $this->documentController->deleteArchiveDocuments($archive->archiveId, $archive->archivalProfileReference);

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
}
