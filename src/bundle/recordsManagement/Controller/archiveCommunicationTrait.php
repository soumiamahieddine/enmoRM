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
     * @param string $originatorArchiveId
     * @param array  $originatingDate
     * @param string $filePlanPosition
     * @param bool   $hasParent
     * @param string $description
     * @param string $text
     * @param bool   $partialRetentionRule
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
        $originatorArchiveId = null,
        $originatingDate = null,
        $filePlanPosition = null,
        $hasParent = null,
        $description = null,
        $text = null,
        $partialRetentionRule = null
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
            'originatorArchiveId' => $originatorArchiveId,
            'originatingDate' => $originatingDate,
            'filePlanPosition' => $filePlanPosition,
            'hasParent' => $hasParent,
            'partialRetentionRule' => $partialRetentionRule,
        ];

        if (!empty($description) || !empty($text)) {
            $searchClasses = [];
            if (!$profileReference) {
                 $searchClasses['recordsManagement/description'] = $this->useDescriptionController('recordsManagement/description');

                $descriptionClassController = \laabs::newController('recordsManagement/descriptionClass');

                foreach ($descriptionClassController->index() as $descriptionClass) {
                    $searchClasses[$descriptionClass->name] = $this->useDescriptionController($descriptionClass->name);
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

        $this->logDelivery($archive);

        return $archive;
    }

    /**
     * Retrieve an archive resource contents
     * @param string $archiveId The archive identifier
     * @param string $resId     The resource identifier
     *
     * @return digitalResource/digitalResource
     */
    public function consultation($archiveId, $resId)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
        $digitalResource = $this->digitalResourceController->retrieve($resId);

        if (!$this->accessVerification($archive) || $digitalResource->archiveId != $archiveId) {
            throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
        }

        $this->logConsultation($archive, $digitalResource);

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
