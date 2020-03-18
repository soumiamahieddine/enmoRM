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
     *
     * @param array $archiveIds  The archives ids
     * @param string $identifier
     * @param string $comment
     * @param string $format    The message format
     * 
     * @return mixed
     * @throws \bundle\recordsManagement\Exception\notDisposableArchiveException
     */
    public function dispose($archiveIds, $identifier = null, $comment = null, $format = null)
    {
        $archives = [];

        $archiveChildrenIds = [];
        foreach ($archiveIds as $archiveId) {
            $archiveChildrenIds[$archiveId] = $this->listChildrenArchiveId($archiveId);
        }

        $res = [];
        $res['success'] = [];
        $res['error'] = [];

        foreach ($archiveChildrenIds as $archiveParentId => $childrenArchiveIds) {
            foreach ($childrenArchiveIds as $childrenArchiveId) {
                $archive = $this->sdoFactory->read('recordsManagement/archive', $childrenArchiveId);
                foreach ($archives as $a) {
                    if ($a->archiveId == $archive->archiveId) {
                        continue 2;
                    }
                }

                $this->checkRights($archive);
                if ($this->checkDisposalRights($archive)) {
                    $res['error'][] = $archiveParentId;
                    continue 2;
                } else {
                    $res['success'][] = $archiveParentId;
                }

                $this->listChildrenArchive($archive, true);
                $archives[] = $archive;
            }
        }

        $archiveList = $this->setStatus($res['success'], 'disposable');

        foreach ($archives as $archive) {
            $this->logDestructionRequest($archive);
        }

        if (isset(\laabs::configuration("medona")['transaction']) && \laabs::configuration("medona")['transaction']) {
            $this->sendDestructionRequest($archives, $identifier, $comment, $format);
        }

        return $res;
    }

    /**
     *
     * Send destruction request
     *
     * @param $archives
     * @param null $identifier
     * @param null $comment
     * @param string $format
     * 
     * @return mixed
     * @throws \Exception
     */
    protected function sendDestructionRequest($archives, $identifier = null, $comment = null, $format = null)
    {
        $archiveDestructionRequestController = \laabs::newController("medona/ArchiveDestructionRequest");

        $archivesByOriginator = array();
        foreach ($archives as $archive) {
            if (!isset($archivesByOriginator[$archive->originatorOrgRegNumber])) {
                $archivesByOriginator[$archive->originatorOrgRegNumber] = array();
            }

            $archivesByOriginator[$archive->originatorOrgRegNumber][] = $archive;
        }
        $requesterOrg = null;
        if (!$requesterOrg) {
            $requesterOrg = \laabs::getToken('ORGANIZATION');
            if (!$requesterOrg) {
                throw \laabs::newException('medona/invalidMessageException', "No current organization choosen");
            }
        }
        $requesterOrgRegNumber = $requesterOrg->registrationNumber;

        if (!$identifier) {
            $identifier = "archiveDestructionRequest_".date("Y-m-d-H-i-s");
        }

        $reference = $identifier;
        foreach ($archivesByOriginator as $originatorOrgRegNumber => $archives) {
            $i = 1;
            $recipientOrgRegNumber = $archives[0]->archiverOrgRegNumber;

            $unique = array(
                'type' => 'ArchiveDestructionRequest',
                'senderOrgRegNumber' => $requesterOrgRegNumber,
                'reference' => $reference,
            );

            while ($this->sdoFactory->exists("medona/message", $unique)) {
                $i++;
                $unique['reference'] = $reference = $identifier.'_'.$i;
            }

            $archiveDestructionRequestController->send(
                $reference,
                $archives,
                $comment,
                $requesterOrgRegNumber,
                $recipientOrgRegNumber,
                $originatorOrgRegNumber,
                $format
            );
        }

        return $archives;
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
        $archiveIdsWithChildren = $archiveIds;
        foreach ($archiveIds as $archiveId) {
            $archiveIdsWithChildren = array_merge($archiveIdsWithChildren, $this->getChildrenArchives($archiveId));
        }

        foreach ($archiveIdsWithChildren as $archiveId) {
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

            if ($archive->status != 'disposed'
                && $archive->status != 'restituted'
                && $archive->status != 'transfered'
            ) {
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

            if ($archive->contents) {
                $archiveChildrenIds = $this->checkChildren($archive->contents);
                $archiveIds = array_merge($archiveIds, $archiveChildrenIds);
            }

            $archiveIds[] = (string) $archive->archiveId;
        }

        return $archiveIds;
    }

    /**
     * @param $archive
     * @param bool $isChild
     * @return array
     * @throws \bundle\recordsManagement\Exception\notDisposableArchiveException
     */
    public function checkDisposalRights($archive, $isChild = false)
    {
        $error = "";

        $this->checkRights($archive);
        $currentDate = \laabs::newTimestamp();

        $beforeError = ($isChild) ? "Children archives : ": "";

        if (isset(\laabs::configuration("recordsManagement")['actionWithoutRetentionRule'])) {
            $actionWithoutRetentionRule = \laabs::configuration("recordsManagement")['actionWithoutRetentionRule'];
        } else {
            $actionWithoutRetentionRule = "preserve";
        }

        if (isset($archive->finalDisposition) && $archive->finalDisposition != "destruction") {
            // throw new \bundle\recordsManagement\Exception\notDisposableArchiveException(
            //     $beforeError."Archive not set for destruction."
            // );
            return new \bundle\recordsManagement\Exception\notDisposableArchiveException(
                $beforeError."Archive not set for destruction."
            );
        }
        if (isset($archive->disposalDate) && $archive->disposalDate > $currentDate) {
            // throw new \bundle\recordsManagement\Exception\notDisposableArchiveException(
            //     $beforeError."Disposal date not reached."
            // );
            return new \bundle\recordsManagement\Exception\notDisposableArchiveException(
                $beforeError."Disposal date not reached."
            );
        }

        if ($archive->status === "frozen") {
            // throw new \bundle\recordsManagement\Exception\notDisposableArchiveException(
            //     $beforeError."Archive not set for destruction."
            // );
            return new \bundle\recordsManagement\Exception\notDisposableArchiveException(
                $beforeError."Archive not set for destruction."
            );
        }

        if ((!isset($archive->finalDisposition)
                || empty($archive->finalDisposition)
                || empty($archive->disposalDate)
            )
            && $actionWithoutRetentionRule == "preserve") {
            // throw new \bundle\recordsManagement\Exception\notDisposableArchiveException(
            //     $beforeError."There is a missing management information (date or retention rule)."
            // );
            return new \bundle\recordsManagement\Exception\notDisposableArchiveException(
                $beforeError."There is a missing management information (date or retention rule)."
            );
        }
    }

    /**
     * remove resources from an archive
     *
     * @param  string   $archiveId Archive Id
     * @param  string[] $resIds    An array of resources id
     *
     * @return array               Array of resId ordered by success and errors
     */
    public function deleteResource($archiveId, $resIds)
    {
        $currentService = \laabs::getToken("ORGANIZATION");
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        $userAccountController = \laabs::newController('auth/userAccount');
        if (($currentService->registrationNumber != $archive->archiverOrgRegNumber
                || !$userAccountController->hasPrivilege("archiveManagement/addResource"))
            && !in_array("owner", $currentService->orgRoleCodes)) {
            return false ;
        }
        $destructResources = [];
        $destructResources['error'] = [];
        $destructResources['success'] = [];

        foreach ($resIds as $resId) {
            $digitalResource = $this->digitalResourceController->info($resId);
            try {
                $this->digitalResourceController->delete($resId);
                $destructResources['success'][] = $resId;
                $this->logDestructionResource($archive, $digitalResource);
            } catch (\Exception $e) {
                $destructResources['error'][] = $resId;
                $this->logDestructionResource($archive, $digitalResource, false);
            }
        }

        if (isset(\laabs::configuration("medona")['transaction'])
            && \laabs::configuration("medona")['transaction']) {
            $this->sendModificationNotification([$archive]);
        }

        return $destructResources;
    }
}
