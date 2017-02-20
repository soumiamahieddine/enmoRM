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
 * Trait for archives compliance
 */
trait archiveComplianceTrait
{
    //Params
    private $limit;
    private $delayDate;


    /**
     * Check integrity of one or several archives giving their identifiers
     * @param object  $archiveIds         An array of archive identifier or an archive identifier
     * @param boolean $integrityByJournal Validate integrity by life cycle journal or by the database
     *
     * @return array Array of archive object
     */
    public function verifyIntegrity($archiveIds, $integrityByJournal = true)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = array('success' => [], 'error' => []);

        $archives = $this->sdoFactory->find("recordsManagement/archive", "archiveId=['".implode("', '", $archiveIds)."']");

        foreach ($archives as $key => $archive) {
            if ($this->checkArchiveIntegrity($archive)) {
                $res['success'][] = (string) $archive->archiveId;
            } else {
                $res['error'][] = (string) $archive->archiveId;
            }
        }

        return $res;
    }


    /**
     * Periodic integrity compliance method
     * @param string $limit The limit
     * @param string $delay The delay
     *
     * @return array
     */
    public function periodicIntegrityCompliance($limit = 1000, $delay = "P1M")
    {
        $this->limit = $limit;
        $this->delayDate = \laabs::newTimestamp()->sub(\laabs::newDuration($delay));

        $archives = $this->sdoFactory->find('recordsManagement/archive', "status!=:notDeleted AND (lastCheckDate<=:delayDate OR (lastCheckDate=null AND depositDate<=:delayDate)) AND parentArchiveId=null", ['notDeleted' => 'disposed', 'delayDate' => $this->delayDate], 'lastCheckDate, depositDate', 0, $limit);

        $archiveIds = [];

        foreach ($archives as $key => $archive) {
            $this->checkArchiveIntegrity($archive);
            $archiveIds[] = $archive->archiveId;
        }

        return $archiveIds;
    }

    /**
     * Verify archives integrity
     * @param archive $archive The archive object
     *
     * @return bool The result of the integrity check
     */
    protected function checkArchiveIntegrity($archive)
    {
        $valid = true;
        $info = 'OK';

        $currentOrganization = \laabs::getToken("ORGANIZATION");

        if (!$currentOrganization) {
            throw \laabs::newException("recordsManagement/logException", "The journal must be archived by an owner organization.");
        }


        try {
            $archive->digitalResources = $this->getDigitalResources($archive->archiveId);

            if (count($archive->digitalResources)) {
                foreach ($archive->digitalResources as $digitalResource) {
                    if (!$this->checkResourceIntegrity($archive, $digitalResource, $currentOrganization)) {
                        $valid = false;
                    }
                }
            }
        } catch (\Exception $e) {
            $valid = false;
            $info = $e->getMessage();
        }

        // recusrively check archive objects
        $children = $this->sdoFactory->find('recordsManagement/archive', "parentArchiveId = '$archive->archiveId'");
        if (count($children)) {
            foreach ($children as $child) {
                $valid = $valid && $this->checkArchiveIntegrity($child);
            }
        }

        if ($valid && $archive->status == "error") {
            $archive->status = "preserved";
        } elseif (!$valid) {
            $archive->status = "error";
        }

        $archive->lastCheckDate = \laabs::newTimestamp();

        $this->sdoFactory->update($archive);

        $eventInfo['resId'] = '';
        $eventInfo['hashAlgorithm'] = '';
        $eventInfo['hash'] = '';
        $eventInfo['address'] = '';
        $eventInfo['requesterOrgRegNumber'] = $currentOrganization->registrationNumber;
        $eventInfo['info'] = $info;

        $this->lifeCycleJournalController->logEvent('recordsManagement/integrityCheck', 'recordsManagement/archive', $archive->archiveId, $eventInfo, $valid);

        return $valid;
    }

    protected function checkResourceIntegrity($archive, $resource, $currentOrganization)
    {
        $valid = false;

        // Retrieve resource creation event
        $creationEvents = $this->lifeCycleJournalController->matchEvent((string) $resource->created, $resource->resId);
        if (count($creationEvents)) {
            foreach ($creationEvents as $creationEvent) {
                // Keep only deposit or conversion events, that create resources
                if ($creationEvent->eventType != 'recordsManagement/deposit' && $creationEvent->eventType != 'recordsManagement/conversion') {
                    continue;
                }

                // Discard events which resId is not current resource identifier (avoid id collisions with other objetcs)
                if (!isset($creationEvent->resId) || $creationEvent->resId != $resource->resId) {
                    continue;
                }

                if ($resource->hash != $creationEvent->hash) {
                    break;
                }

                if (!$this->digitalResourceController->verifyResource($resource)) {
                    break;
                }

                $valid = true;
            }
        } else {
            $valid = $this->digitalResourceController->verifyResource($resource);
        }

        $eventInfo = [];
        $eventInfo['resId'] = $resource->resId;
        $eventInfo['hashAlgorithm'] = $resource->hashAlgorithm;
        $eventInfo['hash'] = $resource->hash;
        $eventInfo['address'] = $resource->address[0]->path;
        $eventInfo['requesterOrgRegNumber'] = $currentOrganization->registrationNumber;
        $eventInfo['info'] = 'Invalid hash: resource may have been altered on the repository';

        $this->lifeCycleJournalController->logEvent('recordsManagement/integrityCheck', 'digitalResource/digitalResource', $resource->resId, $eventInfo, $valid);

        foreach ($resource->relatedResource as $relatedResource) {
            if (!$this->checkResourceIntegrity($archive, $relatedResource, $currentOrganization)) {
                $valid = false;
            }
        }

        return $valid;
    }
}
