<?php

/*
 * Copyright (C) 2017 Maarch
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
 * Trait for archives life cycle logging
 */
trait archiveLifeCycleTrait
{
    /**
     * Log an archive life cycle event
     * @param string                          $type                 The eventType
     * @param recordsManagement/archive       $archive              The archive
     * @param bool                            $operationResult      The event result
     * @param digitalResource/digitalResource $digitalResource      The resource
     * @param array                           $eventInfo            The event information
     * @param bool                            $logDigitalResources  Log or not the event on digital resource
     *
     * @return mixed The created event or the list of created event
     */
    protected function logLifeCycleEvent(
        $type,
        $archive,
        $operationResult = true,
        $digitalResource = null,
        $eventInfo = null,
        $logDigitalResources = null,
        $logChildren = false
    ) {
        $eventItems = !empty($eventInfo) ? $eventInfo : [];
        $res = null;

        $eventItems = array_merge($eventItems, get_object_vars($archive));

        //$eventItems["originatorOwnerOrgRegNumber"] = $archive->originatorOwnerOrgRegNumber;

        if ($digitalResource && $logDigitalResources) {
            $eventItems = array_merge($eventItems, get_object_vars($digitalResource));
            if (is_array($digitalResource->address) && !empty($digitalResource->address)) {
                $eventItems['address'] = $digitalResource->address[0]->path;
            }

            $res = $this->lifeCycleJournalController->logEvent(
                $type,
                'recordsManagement/archive',
                $archive->archiveId,
                $eventItems,
                $operationResult
            );
        } elseif (!empty($archive->digitalResources) && $logDigitalResources) {
            $res = [];

            foreach ($archive->digitalResources as $digitalResource) {
                $eventItems = array_merge($eventItems, get_object_vars($digitalResource));
                if (is_array($digitalResource->address) && !empty($digitalResource->address)) {
                    $eventItems['address'] = $digitalResource->address[0]->path;
                }

                $res[] = $this->lifeCycleJournalController->logEvent(
                    $type,
                    'recordsManagement/archive',
                    $archive->archiveId,
                    $eventItems,
                    $operationResult
                );
            }
        } else {
            $eventItems['address'] = $archive->storagePath;

            $res = $this->lifeCycleJournalController->logEvent(
                $type,
                'recordsManagement/archive',
                $archive->archiveId,
                $eventItems,
                $operationResult
            );
        }

        if ($logChildren && isset($archive->contents) && !empty($archive->contents)) {
            foreach ($archive->contents as $childArchive) {
                $this->logLifeCycleEvent($type, $childArchive, $operationResult, null, null, $logDigitalResources, true);
            }
        }

        return $res;
    }

    /**
     * Log an archive deposit
     * @param recordsManagement/archive $archive         The archive
     * @param bool                      $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logDeposit($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/deposit',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo = false,
            $logDigitalResources = true
        );
    }

    /**
     * Log an archive consultation
     * @param recordsManagement/archive       $archive         The archive
     * @param digitalResource/digitalResource $resource        The resouce
     * @param bool                            $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logConsultation($archive, $resource, $operationResult = true)
    {
        if (empty($archive->serviceLevelReference)) {
            return;
        }

        $serviceLevel = $this->serviceLevelController->getByReference($archive->serviceLevelReference);

        if (strrpos($serviceLevel->control, "logConsultation") === false) {
            return;
        }

        if (empty($resource)) {
            $operationResult = false;
        }

        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/consultation',
            $archive,
            $operationResult,
            $resource,
            $eventInfo = false,
            $logDigitalResources = true
        );
    }


    /**
     * Log an archive delivery
     * @param recordsManagement/archive $archive         The archive
     * @param bool                      $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logDelivery($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/delivery',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo = false,
            $logDigitalResources = true
        );
    }


    /**
     * Log an archive resource conversion
     * @param digitalResource/digitalResource $originalResource  The resouce
     * @param digitalResource/digitalResource $convertedResource The resouce
     * @param recordsManagement/archive       $archive           The archive
     * @param bool                            $operationResult   The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logConvertion($originalResource, $convertedResource, $archive, $operationResult = true)
    {
        $eventInfo = [];
        if ($convertedResource) {
            if (!empty($convertedResource->resId)) {
                $eventInfo['convertedResId'] = $convertedResource->resId;
            }

            if (!empty($convertedResource->address)) {
                $eventInfo['convertedAddress'] = $convertedResource->address[0]->path;
            }

            $eventInfo['convertedHashAlgorithm'] = $convertedResource->hashAlgorithm;
            $eventInfo['convertedHash'] = $convertedResource->hash;
            $eventInfo['software'] = $convertedResource->softwareName.' '.$convertedResource->softwareVersion;
            $eventInfo['convertedSize'] = $convertedResource->size;
            $eventInfo['convertedPuid'] = $convertedResource->puid;
        }

        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/conversion',
            $archive,
            $operationResult,
            $originalResource,
            $eventInfo,
            $logDigitalResources = true
        );
    }

    /**
     * Log an archive elimination
     * @param recordsManagement/archive $archive         The archive
     * @param bool                      $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logElimination($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent('recordsManagement/elimination', $archive, $operationResult, false, false, true, true);
    }

    /**
     * Log an archive destruction request
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logDestructionRequest($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent('recordsManagement/destructionRequest', $archive, $operationResult);
    }

    /**
     * Log an archive destruction request cancel
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logDestructionRequestCancel($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent('recordsManagement/destructionRequestCanceling', $archive, $operationResult);
    }

    /**
     * Log an archive destruction
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logDestruction($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/destruction',
            $archive,
            $operationResult,
            false,
            false,
            true,
            true
        );
    }

    /**
     * delete a digital resource
     * @param recordsManagement/archive                  $archive   	  The archive
     * @param digitalResource/digitalResource  		     $digitalResource The resource
     * @param bool                                       $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logDestructionResource($archive, $digitalResource, $operationResult = true)
    {
        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/resourceDestruction',
            $archive,
            $operationResult,
            $digitalResource,
            $eventInfo = false,
            true
        );
    }

    /**
     * Log an archive restitution request
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logRestitutionRequest($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent('recordsManagement/restitutionRequest', $archive, $operationResult);
    }

    /**
     * Log an archive restitution request cancel
     * @param recordsManagement/archive $archive   	     The archive
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logRestitutionRequestCancel($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent('recordsManagement/restitutionRequestCanceling', $archive, $operationResult);
    }

    /**
     * Log an archive restitution
     * @param recordsManagement/archive $archive         The archive
     * @param bool                      $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logRestitution($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/restitution',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo = false,
            $logDigitalResources = true,
            $logChildren = true
        );
    }

    /**
     * Log an archive outgoing transfer
     * @param recordsManagement/archive $archive         The archive
     * @param bool                      $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logOutgoingTransfer($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent('recordsManagement/outgoingTransfer', $archive, $operationResult, false, false, true, true);
    }

    /**
     * Log an archive retention rule modification
     * @param recordsManagement/archive $archive         The archive
     * @param object                    $retentionRule   The retention rule object
     * @param bool                      $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logRetentionRuleModification($archive, $retentionRule, $operationResult = true)
    {
        $eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'retentionStartDate' => $retentionRule->retentionStartDate,
            'retentionDuration' => (string) $retentionRule->retentionDuration,
            'finalDisposition' => (string) $retentionRule->finalDisposition,
            'previousStartDate' => $retentionRule->previousStartDate,
            'previousDuration' => (string) $retentionRule->previousDuration,
            'previousFinalDisposition' => (string) $retentionRule->previousFinalDisposition,
        );

        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/retentionRuleModification',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo
        );
    }

    /**
     * Log an archive access rule modification
     * @param recordsManagement/archive                 $archive         The archive
     * @param object 					$accessRule      The access rule object
     * @param bool  					$operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logAccessRuleModification($archive, $accessRule, $operationResult = true)
    {
        $eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'accessRuleStartDate' => $accessRule->accessRuleStartDate,
            'accessRuleDuration' => (string) $accessRule->accessRuleDuration,
            'previousAccessRuleStartDate' => $accessRule->previousAccessRuleStartDate,
            'previousAccessRuleDuration' => (string) $accessRule->previousAccessRuleDuration,
        );

        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/accessRuleModification',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo
        );
    }

    /**
     * Log an archive freeze
     * @param recordsManagement/archive $archive         The archive
     * @param bool                      $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logFreeze($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/freeze',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo = false,
            $logDigitalResources = true
        );
    }

    /**
     * Log an archive unfreeze
     * @param recordsManagement/archive $archive         The archive
     * @param bool                      $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logUnfreeze($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/unfreeze',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo = false,
            $logDigitalResources = true
        );
    }

    /**
     * Log an archive metadata modification
     * @param recordsManagement/archive $archive         The archive
     * @param bool                      $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logMetadataModification($archive, $operationResult = true)
    {
        $eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
        );

        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/metadataModification',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo
        );
    }

    /**
     * Log an archive relatationship adding
     * @param recordsManagement/archive $archive             The archive
     * @param object                    $archiveRelationship The access rule object
     * @param bool                      $operationResult     The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logRelationshipAdding($archive, $archiveRelationship, $operationResult = true)
    {
        $eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'relatedArchiveId' => $archiveRelationship->relatedArchiveId
        );

        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/addRelationship',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo
        );
    }

    /**
     * Log an archive relatationship deleting
     * @param recordsManagement/archive $archive             The archive
     * @param object                    $archiveRelationship The access rule object
     * @param bool                      $operationResult     The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logRelationshipDeleting($archive, $archiveRelationship, $operationResult = true)
    {
        $eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'relatedArchiveId' => $archiveRelationship->relatedArchiveId
        );

        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/deleteRelationship',
            $archive,
            $operationResult,
            $digitalResource = false,
            $eventInfo
        );
    }

        /**
     * Log an archive integrity checking
     * @param recordsManagement/archive       $archive         The archive
     * @param string                          $info            The information
     * @param digitalResource/digitalResource $resource        The resource
     * @param bool                            $operationResult The operation result
     *
     * @return mixed The created event or the list of created event
     */
    public function logIntegrityCheck($archive, $info, $resource = null, $operationResult = true)
    {
        $currentOrganization = \laabs::getToken("ORGANIZATION");
        $archive->digitalResources = null;

        $eventInfo = array(
            'requesterOrgRegNumber' => $currentOrganization->registrationNumber,
            'info' => $info,
        );

        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/integrityCheck',
            $archive,
            $operationResult,
            $resource,
            $eventInfo,
            $logDigitalResources = true
        );
    }

    /**
     * Log a deposit of a new digital resource onto an existing archive
     * @param recordsManagement/archive       $archive         The archive
     * @param digitalResource/digitalResource $resource        The resource
     * @param bool                            $operationResult The operation result
     */
    public function logAddResource($archive, $resource, $operationResult = true)
    {
        $currentOrganization = \laabs::getToken("ORGANIZATION");
        $archive->digitalResources = null;

        $eventInfo = array(
            'depositorOrgRegNumber' => $currentOrganization->registrationNumber,
        );

        return $this->logLifeCycleEvent(
            $type = 'recordsManagement/depositNewResource',
            $archive,
            $operationResult,
            $resource,
            $eventInfo,
            $logDigitalResources = true
        );
    }

    /**
     * Log a deposit of a new digital resource onto an existing archive
     * @param recordsManagement/archive       $archive  The archive
     * @param bool                            $operationResult The operation result
     */
    public function logMetadataConsultation($archive, $operationResult = true)
    {
        return $this->logLifeCycleEvent('recordsManagement/metadataConsultation', $archive, $operationResult);
    }
}
