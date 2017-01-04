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
 * Trait for archives conversion
 */
trait archiveConversionTrait
{
    /**
     * Flag for converison
     * @param array $documentIds Array of document identifier
     *
     * @return bool
     */
    /*
    public function conversion($documentIds)
    {
        // Medona
        if (\laabs::hasBundle("medona")) {
            $archiveConversionRequestController = \laabs::newController("medona/ArchiveConversionRequest");

            $archiveIds = array();

            $documentsByOriginator = array();
            foreach ($documentIds as $documentId) {
                $archiveDocumentDigitalResource = $this->sdoFactory->find('recordsManagement/archiveDocumentDigitalResource', "docId='".(string) $documentId."'")[0];
                $archiveIds[] = $archiveDocumentDigitalResource->archiveId;

                if (!isset($documentsByOriginator[$archiveDocumentDigitalResource->originatorOrgRegNumber])) {
                    $documentsByOriginator[$archiveDocumentDigitalResource->originatorOrgRegNumber] = array();
                }

                $documentsByOriginator[$archiveDocumentDigitalResource->originatorOrgRegNumber][] = $archiveDocumentDigitalResource;
            }

            $senderOrg = \laabs::getToken('ORGANIZATION');
            if (!$senderOrg) {
                throw \laabs::newException('medona/invalidMessageException', "No current organization choosen");
            }

            foreach ($documentsByOriginator as $originatorOrgRegNumber => $documents) {
                $recipientOrg = $this->organizationController->getOrgByRegNumber($originatorOrgRegNumber);

                $archiveConversionRequestController->send((string) \laabs::newId(), $senderOrg, $recipientOrg, $documents);
            }
        }

        return $documentIds;
    }*/

    /**
     * Convert archive
     * @param id $resId The resource identifier
     *
     * @return array The convert documents
     */
    public function convert($resId)
    {
        $digitalResource = $this->digitalResourceController->retrieve($resId);
        $archive = $this->sdoFactory->read("recordsManagement/archive", $digitalResource->archiveId);

        // Store document and resources
        if (!$this->currentServiceLevel) {
            if (isset($archive->serviceLevelReference)) {
                $this->useServiceLevel('deposit', $archive->serviceLevelReference);
            } else {
                $this->useServiceLevel('deposit');
            }
        }

        return $this->convertResource($archive, $digitalResource);
    }

    /**
     * Convert an archive resource
     * @param object $archive
     * @param object $digitalResource
     * 
     * @return digitalResource
     */
    protected function convertResource($archive, $digitalResource)
    {
        $eventInfo = array(
            'resId' => $digitalResource->resId,
            'hashAlgorithm' => $digitalResource->hashAlgorithm, 
            'hash' => $digitalResource->hash, 
            'address' => $digitalResource->address[0]->path, 
            'convertedResId' => null,
            'convertedHashAlgorithm' => null,
            'convertedHash' => null,
            'convertedAddress' => null,
            'softwareName' => null, 
            'softwareVersion' => null
        );

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $convertedResource = $this->digitalResourceController->convert($digitalResource, $archive->storagePath.'/copies');
        } catch (\Exception $e) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            if (isset($convertedResource)) {
                $this->digitalResourceController->rollbackStorage($convertedResource);

                $eventInfo['convertedResId'] = $convertedResource->resId;
                $eventInfo['convertedHashAlgorithm'] = $convertedResource->hashAlgorithm;
                $eventInfo['convertedHash'] = $convertedResource->hash;
                $eventInfo['convertedAddress'] = $convertedResource->address[0]->path;
                $eventInfo['software'] = $convertedResource->softwareName.' '.$convertedResource->softwareVersion;
            }

            $event = $this->lifeCycleJournalController->logEvent('recordsManagement/conversion', 'recordsManagement/archive', $archive->archiveId, $eventInfo, false);
            $archive->lifeCycleEvent[] = $event;

            throw $e;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        if ($convertedResource == false) {
            return;
        }

        $eventInfo['convertedResId'] = $convertedResource->resId;
        $eventInfo['convertedHashAlgorithm'] = $convertedResource->hashAlgorithm;
        $eventInfo['convertedHash'] = $convertedResource->hash;
        $eventInfo['convertedAddress'] = $convertedResource->address[0]->path;
        $eventInfo['software'] = $convertedResource->softwareName.' '.$convertedResource->softwareVersion;

        $event = $this->lifeCycleJournalController->logEvent('recordsManagement/conversion', 'recordsManagement/archive', $archive->archiveId, $eventInfo);
        $archive->lifeCycleEvent[] = $event;

        return $convertedResource;
    }
}
