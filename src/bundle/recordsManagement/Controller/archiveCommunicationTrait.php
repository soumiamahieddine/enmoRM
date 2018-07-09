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
     * @param string $originatorOwnerOrgId
     * @param string $originatorArchiveId
     * @param array  $originatingDate
     * @param string $filePlanPosition
     * @param bool   $hasParent
     * @param string $description
     * @param string $text
     * @param bool   $partialRetentionRule
     * @param string $retentionRuleCode
     * @param string $depositStartDate
     * @param string $depositEndDate
     * @param string $originatingStartDate
     * @param string $originatingEndDate
     *
     * @return recordsManagement/archive[] Array of recordsManagement/archive object
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
        $originatorOwnerOrgId = null,
        $originatorArchiveId = null,
        $originatingDate = null,
        $filePlanPosition = null,
        $hasParent = null,
        $description = null,
        $text = null,
        $partialRetentionRule = null,
        $retentionRuleCode = null,
        $depositStartDate = null,
        $depositEndDate = null,
        $originatingStartDate = null,
        $originatingEndDate = null
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
            'originatorOwnerOrgId' => $originatorOwnerOrgId,
            'originatorArchiveId' => $originatorArchiveId,
            'originatingDate' => $originatingDate,
            'filePlanPosition' => $filePlanPosition,
            'hasParent' => $hasParent,
            'partialRetentionRule' => $partialRetentionRule,
            'retentionRuleCode' => $retentionRuleCode,
            'depositStartDate' => $depositStartDate,
            'depositEndDate' => $depositEndDate,
            'originatingDate' => [$originatingStartDate, $originatingEndDate], // [0] startDate, [1] endDate
        ];

        $queryParts = array();
        $queryParams = array();

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
            if ($archiveId){
                $queryParts['archiveId'] = "archiveId = :archiveId";
                $queryParams['archiveId'] = $archiveId;
            } else {
                if ($profileReference){
                    $queryParts['archivalProfileReference'] = "archivalProfileReference = :archivalProfileReference";
                    $queryParams['archivalProfileReference'] = $profileReference;
                }

                if ($status){
                    $queryParts['status'] = "status = :status";
                    $queryParams['status'] = $status;
                }

                if ($retentionRuleCode){
                    $queryParts['retentionRuleCode'] = "retentionRuleCode = :retentionRuleCode";
                    $queryParams['retentionRuleCode'] = $retentionRuleCode;
                }

                if ($originatorOrgRegNumber){
                    $queryParts['originatorOrgRegNumber'] = "originatorOrgRegNumber = :originatorOrgRegNumber";
                    $queryParams['originatorOrgRegNumber'] = $originatorOrgRegNumber;
                }

                if ($finalDisposition){
                    $queryParts['finalDisposition'] = "finalDisposition = :finalDisposition";
                    $queryParams['finalDisposition'] = $finalDisposition;
                }

                if ($originatingStartDate && $originatingEndDate) {
                    $queryParams['originatingStartDate'] = $originatingStartDate;
                    $queryParams['originatingEndDate'] = $originatingEndDate;
                    $queryParts['originatingDate'] = "originatingDate >= :originatingStartDate AND originatingDate <= :originatingEndDate";
                } elseif ($originatingStartDate) {
                    $queryParams['originatingStartDate'] = $originatingStartDate;
                    $queryParts['originatingDate'] = "originatingDate >= :originatingStartDate";

                } elseif ($originatingEndDate) {
                    $queryParams['originatingEndDate'] = $originatingEndDate;
                    $queryParts['originatingDate'] = "originatingDate <= :originatingEndDate";
                }

                if ($depositStartDate && $depositEndDate) {
                    $queryParams['depositStartDate'] = $depositStartDate;
                    $queryParams['depositEndDate'] = $depositEndDate;
                    $queryParts['depositDate'] = "depositDate >= :depositStartDate AND depositDate <= :depositEndDate";
                } elseif ($depositStartDate) {
                    $queryParams['depositStartDate'] = $depositStartDate;
                    $queryParts['depositDate'] = "depositDate >= :depositStartDate";

                } elseif ($depositEndDate) {
                    $queryParams['depositEndDate'] = $depositEndDate;
                    $queryParts['depositDate'] = "depositDate <= :depositEndDate";
                }
                if($archiveExpired){
                    $currentDate = \laabs::newDate();
                    $currentDateString = $currentDate->format('Y-m-d');
                    if ($archiveExpired == "true") {

                        $queryParams['disposalDate'] = $currentDateString;
                        $queryParts['disposalDate'] = "disposalDate <= :disposalDate";
                    }else if($archiveExpired == "false"){
                        $queryParams['disposalDate'] = $currentDateString;
                        $queryParts['disposalDate'] = "disposalDate >= :disposalDate";
                    }
                }

                if($partialRetentionRule){
                    $queryParts['partialRetentionRule'] = "(retentionDuration=NULL OR retentionStartDate=NULL OR retentionRuleCode=NULL)";
                }

            }

            $queryParams['descriptionClass'] = 'recordsManagement/log';
            $queryParts['descriptionClass'] = "(descriptionClass != :descriptionClass OR descriptionClass=NULL)";


            $queryString = \laabs\implode(' AND ', $queryParts);
            $archives = $this->sdoFactory->find('recordsManagement/archive', $queryString, $queryParams, false, false, 300);
        }

        foreach ($archives as $archive) {
            if (!empty($archive->disposalDate) && $archive->disposalDate <= \laabs::newDate()) {
                $archive->disposable = true;
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
     * @return digitalResource/digitalResource Archive resource contents
     */
    public function consultation($archiveId, $resId)
    {
        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);
        try {
            $digitalResource = $this->digitalResourceController->retrieve($resId);

            $resourceIntegrity = true;
            foreach ($digitalResource->address as $address) {
                if (!$address->integrityCheckResult) {
                    $resourceIntegrity = false;
                }
            }

            if (!$resourceIntegrity) {
                $this->logIntegrityCheck($archive, "Invalid resource", $digitalResource, false);
            }

            if (!$this->accessVerification($archive) || $digitalResource->archiveId != $archiveId) {
                throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
            }

            $this->logConsultation($archive, $digitalResource);
            
        } catch (\Exception $e) {
            $this->logConsultation($archive, $digitalResource, false);

            throw $e;
        }

        $binaryDataObject = \laabs::newInstance("recordsManagement/BinaryDataObject");
        $binaryDataObject->attachment = new \stdClass();
        $binaryDataObject->attachment->data = base64_encode($digitalResource->getContents());
        $binaryDataObject->attachment->uri = "";
        $binaryDataObject->attachment->filename = $digitalResource->fileName;

        if (!empty($digitalResource->fileExtension)) {
            $digitalResource->fileName = $digitalResource->fileName . $digitalResource->fileExtension;
        }

        $binaryDataObject->format = $digitalResource->puid;
        $binaryDataObject->mimetype = $digitalResource->mimetype;
        $binaryDataObject->size = $digitalResource->size;

        $binaryDataObject->messageDigest = new \stdClass();
        $binaryDataObject->messageDigest->value = $digitalResource->hash;
        $binaryDataObject->messageDigest->algorithm = $digitalResource->hashAlgorithm;

        return $binaryDataObject;
    }

    /**
     * Retrieve an archive resource contents
     * @param string $archiveId The archive identifier
     *
     * @return digitalResource/digitalResource[] Array of digitalResource/digitalResource object
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
