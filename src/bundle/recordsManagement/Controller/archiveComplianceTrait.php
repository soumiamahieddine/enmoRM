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
 * Trait for archives compliance
 */
trait archiveComplianceTrait
{
    //Params
    private $limit;
    private $delayDate;


    /**
     * Check the integrity of archives by a process of sampling
     * @param string $serviceLevelReference The service level reference
     * @return boolean
     *
     * @throws \Exception
     */
    public function sampling($serviceLevelReference = null)
    {
        $currentOrganization = \laabs::getToken("ORGANIZATION");

        if (!$currentOrganization) {
            throw \laabs::newException("recordsManagement/logException", "An organization is required to check an archive integrity");
        }

        if (!empty($serviceLevelReference)) {
            $serviceLevels = [];
            $serviceLevels[] = $this->serviceLevelController->getByReference($serviceLevelReference);
        } else {
            $serviceLevels = $this->serviceLevelController->index();
        }

        $queryPart = [];
        $queryPart["status"] = "status!='error' AND status!='disposed'";
        $queryPart["parentArchiveId"] = "parentArchiveId=null";

        foreach ($serviceLevels as $serviceLevel) {
            if (($serviceLevel->samplingFrequency <= 0) || ($serviceLevel->samplingRate <= 0)) {
                continue;
            }

            $eventInfo = [];
            $eventInfo['startDatetime'] = \laabs::newTimestamp();

            $events = $this->lifeCycleJournalController->getObjectEvents($serviceLevel->serviceLevelId, 'recordsManagement/serviceLevel', 'recordsManagement/periodicIntegrityCheck');
            $lastEvent = end($events);

            if (!empty($lastEvent)) {
                $diffWithLastEvent = \laabs::newTimestamp()->getTimestamp() - $lastEvent->timestamp->getTimestamp();
                $remainder = $lastEvent->nbArchivesToCheck - $lastEvent->archivesChecked;
            } else {
                $diffWithLastEvent = 3600 * 24;
                $remainder = 0;
            }

            $queryPart["serviceLevelReference"] = "serviceLevelReference='" . $serviceLevel->reference . "'";
            $nbArchives = $this->sdoFactory->count("recordsManagement/archive", \laabs\implode(" AND ", $queryPart));

            $percentage = $diffWithLastEvent / ($serviceLevel->samplingFrequency * 3600 * 24);
            
            if ($percentage > 1) {
                $percentage = 1;
            }
            
            $nbArchivesToCheck = ($nbArchives * $percentage) + $remainder ;
            $nbArchivesInSample = $nbArchivesToCheck * ($serviceLevel->samplingRate / 100);

            $nbArchivesToCheck = ceil($nbArchivesToCheck);
            $nbArchivesInSample = ceil($nbArchivesInSample);

            $archives = $this->sdoFactory->find("recordsManagement/archive", \laabs\implode(" AND ", $queryPart), null, "<lastCheckDate <depositDate", null, $nbArchivesToCheck);
            shuffle($archives);

            $success = true;
            $archivesChecked = 0;

            for ($i = 0; $i < $nbArchivesInSample; $i++) {
                $archive = array_pop($archives);
                $success = $this->checkArchiveIntegrity($archive);

                if (!$success) {
                    break;
                } else {
                    $archivesChecked++;
                }
            }

            if ($success) {
                for ($i = 0, $count = count($archives); $i < $count; $i++) {
                    $this->setValidatedArchiveWithoutCheck($archives[$i]);
                    $archivesChecked++;
                }
            }

            $eventInfo['endDatetime'] = \laabs::newTimestamp();
            $eventInfo['nbArchivesToCheck'] = $nbArchivesToCheck;
            $eventInfo['nbArchivesInSample'] = $nbArchivesInSample;
            $eventInfo['archivesChecked'] = $archivesChecked;

            $this->lifeCycleJournalController->logEvent('recordsManagement/periodicIntegrityCheck', 'recordsManagement/serviceLevel', $serviceLevel->serviceLevelId, $eventInfo, $success);
        }

        return true;
    }

    /**
     * Set a validated archive with it's children without check
     * @param recordsManagement/archive $archive The archive object
     */
    private function setValidatedArchiveWithoutCheck($archive)
    {
        $archive->lastCheckDate = \laabs::newTimestamp();

        $this->sdoFactory->update($archive);

        $this->logIntegrityCheck($archive, "Without check");

        $children = $this->sdoFactory->find('recordsManagement/archive', "parentArchiveId = '$archive->archiveId' AND status!='error' AND status!='disposed'");

        for ($i = 0, $count = count($children); $i < $count; $i++) {
            $this->setValidatedArchiveWithoutCheck($children[$i]);
        }
    }
    /**
     * Check integrity of one or several archives giving their identifiers
     * @param object  $archiveIds         An array of archive identifier or an archive identifier
     *
     * @return recordsManagement/archive[] Array of archive object
     */
    public function verifyIntegrity($archiveIds)
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
     * Verify archives integrity
     * @param archive $archive The archive object
     *
     * @return bool The result of the integrity check
     */
    protected function checkArchiveIntegrity($archive)
    {
        $valid = true;
        $info = 'Checking succeeded';

        $currentOrganization = \laabs::getToken("ORGANIZATION");

        if (!$currentOrganization) {
            throw \laabs::newException("recordsManagement/logException", "An organization is required to check an archive integrity");
        }

        $archive->digitalResources = $this->getDigitalResources($archive->archiveId);

        $errors = [];
        if (count($archive->digitalResources)) {
            foreach ($archive->digitalResources as $digitalResource) {
                try {
                    $this->checkResourceIntegrity($archive, $digitalResource);
                    $this->logIntegrityCheck($archive, "Checking succeeded", $digitalResource, true);
                } catch (\Exception $e) {
                    $errors[] = $e->getMessage();
                    $this->logIntegrityCheck($archive, $e->getMessage(), $digitalResource, false);
                }

            }
        }

        if (!empty($errors)) {
            $valid = false;
            $info = \laabs\implode(", ", $errors);
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
        
        $this->logIntegrityCheck($archive, $info, null, $valid);

        return $valid;
    }

    /**
     * Verify resource integrity
     * @param archive $archive The archive object
     * @param object $resource The resource object
     *
     * @return bool The result of the integrity check
     */
    protected function checkResourceIntegrity($archive, $resource)
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

                if (!$this->digitalResourceController->verifyResource($resource, \bundle\digitalResource\Controller\cluster::MODE_WRITE)) {

                    break;
                }

                $valid = true;
            }
        } else {
            $valid = $this->digitalResourceController->verifyResource($resource, \bundle\digitalResource\Controller\cluster::MODE_WRITE);
        }

        foreach ($resource->relatedResource as $relatedResource) {
            if (!$this->checkResourceIntegrity($archive, $relatedResource)) {
                $valid = false;
            }
        }

        return $valid;
    }
}