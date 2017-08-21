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
 * Trait for archives communication
 */
trait archiveCommunicationTrait
{

    /**
     * Search archives by profile / dates / agreement
     * @param string $archiveId
     * @param string $profileReference
     * @param string $status
     * @param string $archiveName
     * @param string $agreementReference
     * @param string $archiveExpired
     * @param string $finalDisposition
     * @param string $originatorOrgRegNumber
     * @param string $filePlanPosition
     * @param bool   $hasParent
     * @param string $description
     * @param string $text
     *
     * @return recordsManagement/archive[]
     */
    public function search(
        $archiveId = null,
        $profileReference = null,
        $status = null,
        $archiveName = null,
        $agreementReference = null,
        $archiveExpired = null,
        $finalDisposition = null,
        $originatorOrgRegNumber = null,
        $filePlanPosition = null,
        $hasParent = null,
        $description = null,
        $text = null
    ) {
        $archives = [];

        $archiveArgs = [
            'archiveId' => $archiveId,
            'profileReference' => $profileReference,
            'status' => $status,
            'archiveName' => $archiveName,
            'agreementReference' => $agreementReference,
            'archiveExpired' => $archiveExpired,
            'finalDisposition' => $finalDisposition,
            'originatorOrgRegNumber' => $originatorOrgRegNumber,
            'filePlanPosition' => $filePlanPosition,
            'hasParent' => $hasParent,
        ];

        if (!empty($description) || !empty($text)) {
            $searchClasses = [];
            if (!$profileReference) {
                $archivalProfiles = $this->archivalProfileController->index();
                foreach ($archivalProfiles as $archivalProfile) {
                    if ($archivalProfile->descriptionClass != '' && !isset($searchClasses[$archivalProfile->descriptionClass])) {
                        $searchClasses[$archivalProfile->descriptionClass] = $this->useDescriptionController($archivalProfile->descriptionClass);
                    } elseif (!isset($searchClasses['recordsManagement/description'])) {
                        $searchClasses['recordsManagement/description'] = $this->useDescriptionController('recordsManagement/description');
                    }
                }
            } else {
                $archivalProfile = $this->archivalProfileController->getByReference($profileReference);
                if ($archivalProfile->descriptionClass != '') {
                    $searchClasses[$archivalProfile->descriptionClass] = $this->useDescriptionController($archivalProfile->descriptionClass);
                } else {
                    $searchClasses['recordsManagement/description'] = $this->useDescriptionController('recordsManagement/description');
                }
            }

            foreach ($searchClasses as $descriptionClass => $descriptionController) {
                $archives = array_merge($archives, $descriptionController->search($description, $text, $archiveArgs));
            }
        } else {
            $queryString = $this->getArchiveAssert($archiveArgs);

            $originators = array();
            foreach ((array) $this->organizationController->getOrgsByRole('originator') as $originator) {
                $originators[$originator->registrationNumber] = $originator;
            }

            $archives = $this->sdoFactory->find('recordsManagement/archive', $queryString, null, false, false, 100);
            foreach ($archives as $archive) {
                if (!empty($archive->disposalDate) && $archive->disposalDate <= \laabs::newDate()) {
                    $archive->disposable = true;
                }

                if (isset($originators[$archive->originatorOrgRegNumber])) {
                    $archive->originator = $originators[$archive->originatorOrgRegNumber];
                }
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
                $eventItems['size'] = $digitalResource->size;

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

        $this->logEventConsultation($digitalResource);

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

        $this->logEventConsultation($digitalResource);

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

    /**
     * Logging event when a resource is consult
     *
     * @param digitalResource/digitalResource $digitalResource The consulted resource
     */
    private function logEventConsultation($digitalResource)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $digitalResource->archiveId);
        $serviceLevel = $this->serviceLevelController->getByReference($archive->serviceLevelReference);

        if (strrpos($serviceLevel->control, "logConsultation") == false) {
            return;
        }

        $eventItems = [];
        $eventItems['resId'] = $eventItems['hashAlgorithm'] = $eventItems['hash'] = $eventItems['address'] = null;

        if (empty($digitalResource)) {
            $this->lifeCycleJournalController->logEvent('recordsManagement/consultation', 'digitalResource/digitalResource', $resId, $eventItems, null, false);
        }

        $eventItems['resId'] = $resId;
        $eventItems['hashAlgorithm'] = $digitalResource->hashAlgorithm;
        $eventItems['hash'] = $digitalResource->hash;
        $eventItems['address'] = $digitalResource->address[0]->path;
        $eventItems['size'] = $digitalResource->size;

        $this->lifeCycleJournalController->logEvent('recordsManagement/consultation', 'recordsManagement/archive', $digitalResource->archiveId, $eventItems);
    }
}
