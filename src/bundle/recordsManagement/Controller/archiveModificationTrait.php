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
 * Trait for archives modification
 */
trait archiveModificationTrait
{

    /**
     * Read the retention rule of an archive
     * @param string $archiveId The archive identifier
     *
     * @return recordsManagement/archive[]
     */
    public function editArchiveRetentionRule($archiveId)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        $this->getAccessRule($archive);

        return \laabs::castMessage($archive, 'recordsManagement/archiveRetentionRule');

    }

    /**
     * Read the access rule of an archive
     * @param string $archiveId The archive identifier
     *
     * @return recordsManagement/archive[]
     */
    public function editArchiveAccessRule($archiveId)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        $this->getAccessRule($archive);

        return \laabs::castMessage($archive, 'recordsManagement/archiveAccessRule');
    }

    /**
     * Modify the archive retention
     * @param recordsManagement/archiveRetentionRule $retentionRule The retention rule object
     * @param mixed                                  $archiveIds    The archives ids
     *
     * @return bool
     */
    public function modifyRetentionRule($retentionRule, $archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = array('success' => array(), 'error' => array());

        $archives = array();
        $events = array();

        if (!$currentOrg = \laabs::getToken("ORGANIZATION")) {
            throw \laabs::newException('recordsManagement/noOrgUnitException', "Permission denied: You have to choose a working organization unit to proceed this action.");
        }

        foreach ($archiveIds as $archiveId) {
            $archive = $this->getDescription($archiveId);
            $this->checkRights($archive);

            if (!in_array($archive->status, array("preserved", "frozen"))) {
                array_push($res['error'], $archiveId);

                $operationResult = false;

            } else {
                $retentionRule->archiveId = $archiveId;
                $retentionRule->disposalDate = $this->calculateDate($retentionRule->retentionStartDate, $retentionRule->retentionDuration);

                $this->sdoFactory->update($retentionRule, 'recordsManagement/archive');

                $retentionRule->previousStartDate = $archive->retentionStartDate;
                $retentionRule->previousDuration = $archive->retentionDuration;
                $retentionRule->previousFinalDisposition = $archive->finalDisposition;

                // Update current object for caller
                $archive->retentionStartDate = $retentionRule->retentionStartDate;
                $archive->retentionDuration = $retentionRule->retentionDuration;
                $archive->disposalDate = $retentionRule->disposalDate;

                array_push($res['success'], $archiveId);

                $operationResult = true;

                $archives[] = $archive;

                // Life cycle journal
                // Certificate of modication
                $eventInfo = array(
                    'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
                    'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
                    'retentionStartDate' => (string) $retentionRule->retentionStartDate,
                    'retentionDuration' => (string) $retentionRule->retentionDuration,
                    'finalDisposition' => (string) $retentionRule->finalDisposition,
                    'previousStartDate' => (string) $retentionRule->previousStartDate,
                    'previousDuration' => (string) $retentionRule->previousDuration,
                    'previousFinalDisposition' => (string) $retentionRule->previousFinalDisposition,
                );

                if (empty($archive->digitalResources)) {
                    $eventInfo['resId'] = $eventInfo['hashAlgorithm'] = $eventInfo['hash'] = $eventInfo['address'] = null;
                    $event = $this->lifeCycleJournalController->logEvent(
                        'recordsManagement/retentionRuleModification',
                        'recordsManagement/archive',
                        $archive->archiveId,
                        $eventInfo,
                        $operationResult
                    );
                }
                foreach ($archive->digitalResources as $digitalResource) {
                        $eventInfo['resId'] = $digitalResource->resId;
                        $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                        $eventInfo['hash'] = $digitalResource->hash;
                        $eventInfo['address'] = $digitalResource->address[0]->path;

                        $event = $this->lifeCycleJournalController->logEvent(
                            'recordsManagement/retentionRuleModification',
                            'recordsManagement/archive',
                            $archive->archiveId,
                            $eventInfo,
                            $operationResult
                        );
                        $archive->lifeCycleEvent = array($event);
                }
            }
        }

        return $res;
    }

    /**
     * Modify the archive access
     * @param recordsManagement/archiveAccessCode $accessRule The access rule object
     * @param array                               $archiveIds The archives ids
     *
     * @return bool
     */
    public function modifyAccessRule($accessRule, $archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = array('success' => array(), 'error' => array());

        $archives = array();
        $operationResult = null;


        foreach ($archiveIds as $archiveId) {
            $archive = $this->getDescription($archiveId);
            $this->checkRights($archive);

            if (!in_array($archive->status, array("preserved", "frozen"))) {
                array_push($res['error'], $archiveId);

                $operationResult = false;
            } else {
                $accessRule->archiveId = $archiveId;

                if ($accessRule->accessRuleDuration != null && $accessRule->accessRuleStartDate != null) {
                    $accessRule->accessRuleComDate = $this->calculateDate($accessRule->accessRuleStartDate, $accessRule->accessRuleDuration);
                }

                $this->sdoFactory->update($accessRule, 'recordsManagement/archive');

                $accessRule->previousAccessRuleStartDate = $archive->accessRuleStartDate;
                $accessRule->previousAccessRuleDuration = $archive->accessRuleDuration;

                $archive->accessRuleStartDate = $accessRule->accessRuleStartDate;
                $archive->accessRuleDuration = $accessRule->accessRuleDuration;
                $archive->accessRuleComDate = $accessRule->accessRuleComDate;

                array_push($res['success'], $archiveId);

                $operationResult = true;

                $archives[] = $archive;

                // Life cycle journal
                // Certificate of modication
                $eventInfo = array(
                    'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
                    'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
                    'accessRuleStartDate' => (string) $accessRule->accessRuleStartDate,
                    'accessRuleDuration' => (string) $accessRule->accessRuleDuration,
                    'previousAccessRuleStartDate' => (string) $accessRule->previousAccessRuleStartDate,
                    'previousAccessRuleDuration' => (string) $accessRule->previousAccessRuleDuration,
                );

                if (empty($archive->digitalResources)) {
                    $eventInfo['resId'] = $eventInfo['hashAlgorithm'] = $eventInfo['hash'] = $eventInfo['address'] = null;
                    $event = $this->lifeCycleJournalController->logEvent(
                        'recordsManagement/accessRuleModification',
                        'recordsManagement/archive',
                        $archive->archiveId,
                        $eventInfo,
                        $operationResult
                    );
                }

                foreach ($archive->digitalResources as $digitalResource) {
                        $eventInfo['resId'] = $digitalResource->resId;
                        $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                        $eventInfo['hash'] = $digitalResource->hash;
                        $eventInfo['address'] = $digitalResource->address[0]->path;

                        $event = $this->lifeCycleJournalController->logEvent(
                            'recordsManagement/accessRuleModification',
                            'recordsManagement/archive',
                            $archive->archiveId,
                            $eventInfo,
                            $operationResult
                        );

                        $archive->lifeCycleEvent = array($event);
                }
            }
        }

        return $res;
    }

    /**
     * Suspend archives
     * @param mixed $archiveIds Array of archive identifier
     *
     * @return array
     */
    public function freeze($archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }
        $res = $this->setStatus($archiveIds, 'frozen');

        $archives = array();

        foreach ($archiveIds as $archiveId) {
            $archive = $this->getDescription($archiveId);
            $this->checkRights($archive);

            $operationResult = true;

            $archives[] = $archive;

            // Life cycle journal
            // Certificate of modication
            $eventInfo = array(
                'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
                'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            );

            foreach ($archive->digitalResources as $digitalResource) {
                $eventInfo['resId'] = $digitalResource->resId;
                $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                $eventInfo['hash'] = $digitalResource->hash;
                $eventInfo['address'] = $digitalResource->address[0]->path;

                $event = $this->lifeCycleJournalController->logEvent(
                    'recordsManagement/freeze',
                    'recordsManagement/archive',
                    $archive->archiveId,
                    $eventInfo,
                    $operationResult
                );

                $archive->lifeCycleEvent = array($event);
            }
        }

        return $res;
    }

    /**
     * Liberate archives
     * @param mixed $archiveIds Array of archive identifier
     *
     * @return array
     */
    public function unfreeze($archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }
        $res = $this->setStatus($archiveIds, 'preserved');

        $archives = array();
        foreach ($archiveIds as $archiveId) {
            $archive = $this->getDescription($archiveId);
            $this->checkRights($archive);

            $operationResult = true;

            $archives[] = $archive;

            // Life cycle journal
            // Certificate of modication
            $eventInfo = array(
                'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
                'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            );

            foreach ($archive->digitalResources as $digitalResource) {
                    $eventInfo['resId'] = $digitalResource->resId;
                    $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                    $eventInfo['hash'] = $digitalResource->hash;
                    $eventInfo['address'] = $digitalResource->address[0]->path;

                    $event = $this->lifeCycleJournalController->logEvent(
                        'recordsManagement/unfreeze',
                        'recordsManagement/archive',
                        $archive->archiveId,
                        $eventInfo,
                        $operationResult
                    );

                    $archive->lifeCycleEvent = array($event);
            }
        }

        return $res;
    }
    
    public function modifyMetadata($archiveId, $originatorArchiveId =null, $archiveName = null,$description = null)
    {
        $archive = $this->getDescription($archiveId);
        $this->checkRights($archive);
        
        if ($archiveName) {
            $archive->archiveName = $archiveName;
        }
        
        if ($originatorArchiveId) {
            $archive->originatorArchiveId = $originatorArchiveId;
        }
        
        if ($description) {
            if (!empty($archive->descriptionClass)) {
                $descriptionController = $this->useDescriptionController($archive->descriptionClass);
            } else {
                $descriptionController = $this->useDescriptionController('recordsManagement/description');
            }
            
            $descriptionController->update(json_decode($description),$archiveId);
        }
        
        $this->sdoFactory->update($archive, 'recordsManagement/archive');
        
        $operationResult = true;
        $res = true;
        $eventInfo = array(
                'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
                'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            );

            foreach ($archive->digitalResources as $digitalResource) {
                $eventInfo['resId'] = $digitalResource->resId;
                $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                $eventInfo['hash'] = $digitalResource->hash;
                $eventInfo['address'] = $digitalResource->address[0]->path;

                $event = $this->lifeCycleJournalController->logEvent(
                    'recordsManagement/metadata',
                    'recordsManagement/archive',
                    $archive->archiveId,
                    $eventInfo,
                    $operationResult
                );

                $archive->lifeCycleEvent = array($event);
            }
            
        return $res;
    }
    /**
     * Add a relationship to the archive
     * @param recordsManagement/archiveRelationship $archiveRelationship The relationship of the archive
     *
     * @return bool The result of the operation
     */
    public function addRelationship($archiveRelationship)
    {
        $this->archiveRelationshipController->createRelationship($archiveRelationship);

        $archive = $this->getDescription($archiveRelationship->archiveId);

        // Life cycle journal
        // Certificate of modication
        $eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
        );

        foreach ($archive->digitalResources as $digitalResource) {
            $eventInfo['resId'] = $digitalResource->resId;
            $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
            $eventInfo['hash'] = $digitalResource->hash;
            $eventInfo['address'] = $digitalResource->address[0]->path;
            $eventInfo['relatedArchiveId'] = $archiveRelationship->relatedArchiveId;

            $this->lifeCycleJournalController->logEvent('recordsManagement/addRelationship', 'recordsManagement/archive', $archive->archiveId, $eventInfo);
        }

        return true;
    }

    /**
     * delete a relationship
     * @param recordsManagement/archiveRelationship $archiveRelationship The archive relationship object
     *
     * @return recordsManagement/archiveRelationship
     */
    public function deleteRelationship($archiveRelationship)
    {
        $this->archiveRelationshipController->deleteRelationship($archiveRelationship);

        $archive = $this->getDescription($archiveRelationship->archiveId);

        // Life cycle journal
        // Certificate of modication
        $eventInfo = array('resId' => null, 'hashAlgorithm' => null, 'hash' => null, 'address' => null);

        $eventInfo['address'] = $archive->storagePath;
        $eventInfo['relatedArchiveId'] = $archiveRelationship->relatedArchiveId;

        $this->lifeCycleJournalController->logEvent('recordsManagement/deleteRelationship', 'recordsManagement/archive', $archive->archiveId, $eventInfo);

        return true;
    }


    /**
     * Index full text 
     * @param int $limit The maximum number of archive to index
     *
     * @return array The result of the operation
     */
    public function indexFullText($limit=200)
    {
        $res = [];
        $res['success'] = [];
        $res['fail'] = [];
        $archivesToIndex = $this->sdoFactory->find('recordsManagement/archive', "fullTextIndexation='requested'", null, null, null, $limit);
        if (isset(\laabs::configuration('recordsManagement')['stopWordsFilePath'])) {
            $stopWords = \laabs::configuration('recordsManagement')['stopWordsFilePath'];
            $stopWords = utf8_encode(file_get_contents($stopWords));
            $stopWords = preg_replace('/[\r\n]/', " ",$stopWords);
            $stopWords = explode(" ", $stopWords);
        }

        if (count($archivesToIndex)) {
            $descriptionController = $this->useDescriptionController('recordsManagement/description');

            foreach ($archivesToIndex as $archive) {
                $archive = $this->getDescription($archive->archiveId);

                try {
                    $fullText = $this->digitalResourceController->getFullTextByArchiveId($archive->archiveId);
                    $fullText = strtolower($fullText);
                    $fullText = preg_replace('/[.,\/#!?$%\^&\*;:{}=\-_\'`~()\r\n]|\s+/'," ", $fullText);

                    if (isset($stopWords)) {
                        $fullTextArray = explode(" ", $fullText);
                        $fullTextArray = array_diff($fullTextArray, $stopWords);
                        $fullText = implode(" ", $fullTextArray);
                    } else {
                        $fullText = preg_replace("/\b[^-]{1,2}\b/u", "-",  $fullText);
                    }

                    $descriptionController->create($archive, $fullText);

                    $operationResult = true;

                } catch(\Exception $e) {
                    $operationResult = false;
                    $archive->fullTextIndexation = "failed";
                    $this->sdoFactory->update('recordsManagement/archive', $archive);
                }

                $eventInfo = array(
                    'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
                    'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
                );

                foreach ($archive->digitalResources as $digitalResource) {
                    $eventInfo['resId'] = $digitalResource->resId;
                    $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                    $eventInfo['hash'] = $digitalResource->hash;
                    $eventInfo['address'] = $digitalResource->address[0]->path;

                    $event = $this->lifeCycleJournalController->logEvent(
                        'recordsManagement/metadata',
                        'recordsManagement/archive',
                        $archive->archiveId,
                        $eventInfo,
                        $operationResult
                    );

                }
                $archive->lifeCycleEvent = array($event);

                if ($operationResult) {
                    $res['success'][] = (string)$archive->archiveId;
                } else {
                    $res['error'][] = (string)$archive->archiveId;
                }
            }
        }

        return $res;
    }
}
