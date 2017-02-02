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
 * Trait for archives communication
 */
trait archiveCommunicationTrait
{

    /**
     * Search archives by profile / dates / agreement
     * @param string $profileReference
     * @param string $status
     * @param string $archiveName
     * @param string $agreementReference
     * @param string $archiveId
     * @param string $archiveExpired
     * @param string $finalDisposition
     * @param string $origniatorOrgRegNumber
     * @param string $archiveIdOriginator
     *
     * @return recordsManagement/archive[]
     */
    public function search($profileReference = false, $status = false, $archiveName = false, $agreementReference = false, $archiveId = false, $archiveExpired = null, $finalDisposition = false, $origniatorOrgRegNumber = false, $archiveIdOriginator = false)
    {
        $currentDate = \laabs::newDate();
        $currentDateString = $currentDate->format('Y-m-d');

        $queryParts = array();

        if ($archiveName) {
            $queryParts[] = "archiveName='$archiveName'";
        }
        if ($profileReference) {
            $queryParts[] = "archivalProfileReference='$profileReference'";
        }
        if ($agreementReference) {
            $queryParts[] = "archivalAgreementReference='$agreementReference'";
        }
        if ($archiveId) {
            $queryParts[] = "archiveId='$archiveId'";
        }
        if ($status) {
            $queryParts[] = "status='$status'";
        } else {
            $queryParts[] = "status=['preserved', 'disposable', 'restituable', 'restitution', 'restitued', 'frozen', 'error']";
        }
        if ($archiveExpired == "true") {
            $queryParts[] = "disposalDate<='$currentDateString'";
        }
        if ($archiveExpired == "false") {
            $queryParts[] = "disposalDate>='$currentDateString'";
        }
        if ($finalDisposition) {
            $queryParts[] = "finalDisposition='$finalDisposition'";
        }
        if ($origniatorOrgRegNumber) {
            $queryParts[] = "originatorOrgRegNumber='$origniatorOrgRegNumber'";
        }

        $originators = array();
        foreach ((array) $this->organizationController->getOrgsByRole('originator') as $originator) {
            $originators[$originator->registrationNumber] = $originator;
        }

        $archives = $this->sdoFactory->find('recordsManagement/archive', implode(' and ', $queryParts), null, false, false, 100);
        foreach ($archives as $archive) {
            if (!empty($archive->disposalDate) && $archive->disposalDate <= $currentDate) {
                $archive->disposable = true;
            }

            if (isset($originators[$archive->originatorOrgRegNumber])) {
                $archive->originator = $originators[$archive->originatorOrgRegNumber];
            }
        }

        return $archives;
    }

    /**
     * Restitute an archive
     * @param string $archiveId The idetifier of the archive
     *
     * @return recordsManagement/archive The restitue archive
     */
    public function communicate($archiveId)
    {
        $this->verifyIntegrity($archiveId);

        $archive = $this->retrieve($archiveId);

        // Life cycle journal
        $eventItems = array(
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
        );

        $eventItems['resId'] = null;
        $eventItems['hashAlgorithm'] = null;
        $eventItems['hash'] = null;
        $eventItems['address'] = $archive->storagePath;
        $this->lifeCycleJournalController->logEvent('recordsManagement/delivery', 'recordsManagement/archive', $archive->archiveId, $eventItems);

        if (!empty($archive->digitalResources)) {
            foreach ((array) $archive->digitalResources as $digitalResource) {
                $eventItems['resId'] = $digitalResource->resId;
                $eventItems['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                $eventItems['hash'] = $digitalResource->hash;
                $eventItems['address'] = $archive->storagePath;

                $this->lifeCycleJournalController->logEvent('recordsManagement/delivery', 'digitalResource/digitalResource', $digitalResource->resId, $eventItems);
            }
        }

        return $archive;
    }

    /**
     * Retrieve an archive resource contents
     * @param string $archiveId The archive identifier
     * @param string $resId     The resource identifier
     *
     * @return digitalResource/digitalResource
     */
    public function getContents($archiveId, $resId)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        if (!$this->accessVerification($archive)) {
            throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
        }

        $digitalResource = $this->digitalResourceController->retrieve($resId);

        return $digitalResource;
    }

    /**
     * Retrieve an archive resource contents
     * @param string $resId The resource identifier
     *
     * @return digitalResource/digitalResource
     */
    public function getDigitalResource($resId)
    {
        $digitalResource = $this->digitalResourceController->retrieve($resId);

        $archive = $this->getDescription($digitalResource->archiveId);

        if (!$this->accessVerification($archive)) {
            throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
        }

        return $digitalResource;
    }

    /**
     * Retrieve an archive resource contents
     * @param string $archiveId The archive identifier
     *
     * @return digitalResource/digitalResource[]
     */
    public function getDigitalResources($archiveId)
    {
        $digitalResources = [];
        foreach ($this->digitalResourceController->getResourcesByArchiveId($archiveId) as $digitalResource) {
            $digitalResources[] = $this->digitalResourceController->retrieve($digitalResource->resId);
        }

        return $digitalResources;
    }
}
