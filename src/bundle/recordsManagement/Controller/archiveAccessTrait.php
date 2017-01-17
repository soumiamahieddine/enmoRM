<?php

/*
 *  Copyright (C) 2017 Maarch
 * 
 *  This file is part of bundle XXXX.
 *  Bundle XXXX is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  Bundle XXXX is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with bundle XXXX.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\recordsManagement\Controller;

/**
 * Archive access controller
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
trait archiveAccessTrait
{
    /**
     * Get archive metadata
     * @param string $archiveId The archive identifier
     *
     * @return recordsManagement/archive The archive metadata
     */
    public function getMetadata($archiveId)
    {
        $archive = $this->read($archiveId);

        if (!$this->accessVerification($archive)) {
            throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
        }

        $archive->digitalResources = $this->digitalResourceController->getResourcesByArchiveId($archiveId);

        $nbResource = count($archive->digitalResources);

        for ($i = 0; $i < $nbResource; $i++) {
            $archive->digitalResources[$i] = $this->digitalResourceController->info($archive->digitalResources[$i]->resId);
        }

        $this->logging($archive);

        return $archive;
    }

    /**
     * Validate archive access
     *
     * @param string $archiveId The archive identifier
     *
     * @return boolean The result of the authorization access
     */
    public function accessVerification($archiveId)
    {
        $archive = $this->read($archiveId);

        $comDateAccess = $this->accessComDateVerification($archive);

        $currentService = \laabs::getToken("ORGANIZATION");
        if (!$currentService) {
            return false;
        }

        $userServiceOrgRegNumbers = array_merge(array($currentService->registrationNumber), $this->userPositionController->readDescandantService((string) $currentService->orgId));

        foreach ($userServiceOrgRegNumbers as $userServiceOrgRegNumber) {
            $userService = $this->organizationController->getOrgByRegNumber($userServiceOrgRegNumber);

            // User orgUnit is owner
            if (isset($userService->orgRoleCodes) && (strpos((string) $userService->orgRoleCodes, 'owner') !== false)) {
                return true;
            }

            // Archiver or Originator
            if ($userServiceOrgRegNumber == (string) $archive->archiverOrgRegNumber || $userServiceOrgRegNumber == (string) $archive->originatorOrgRegNumber) {
                return true;
            }

            // If date is in the past, public communication is allowed
            if ($userService->ownerOrgId == $archive->originatorOwnerOrgId && $comDateAccess) {
                return true;
            }
        }
    }

    /**
     * Get archive package, data and metadata
     *
     * @param string $archiveId The archive identifier
     *
     * @return recordsManangement/archive The archive package (data and metadata)
     */
    public function getPackage($archiveId)
    {
        $archive = $this->read($archiveId);

        if (!$this->accessVerification($archive)) {
            throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
        }

        $archive->digitalResources = $this->digitalResourceController->getResourcesByArchiveId($archiveId);

        $nbResource = count($archive->digitalResources);

        for ($i = 0; $i < $nbResource; $i++) {
            $archive->digitalResources[$i] = $this->digitalResourceController->retrieve($archive->digitalResources[$i]->resId);
        }

        $this->logging($archive);

        return $archive;
    }

    /**
     * Get an archive package for the communication
     *
     * @param string $archiveId The archive identifier
     */
    public function getConmmunicationPackage($archiveId)
    {
        // Constituer les paquets à communiquer avec aip

        // $archive = $this->read($archiveId);

        // if (!$this->accessVerification($archive)) {
        //     throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
        // }

        // $this->logging($archive);
    }

    /**
     * Send archive for consultation
     *
     * @param string $archiveId The archive identifier
     *
     * @return recordsManagement/archive The archive package
     */
    public function sendForConsultation($archiveId)
    {
        // Envoyer pour consultation simple avec historique

        // $archive = $this->read($archiveId);

        // if (!$this->accessVerification($archive)) {
        //     throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
        // }

        // $this->logging($archive);
    }

    /**
     * Log the archive access
     *
     * @param recordsManagement/archive $archive The archive logged
     */
    public function logging($archive)
    {
        // Journaliser la consultation
        $eventItems['resId'] = null;
        $eventItems['hashAlgorithm'] = null;
        $eventItems['hash'] = null;
        $eventItems['address'] = $archive->storagePath;
        $this->lifeCycleJournalController->logEvent('recordsManagement/consultation', 'recordsManagement/archive', $archive->archiveId, $eventItems);

        foreach ($archive->digitalResources as $digitalResource) {
            $eventItems['resId'] = $digitalResource->resId;
            $eventItems['hashAlgorithm'] = $digitalResource->hashAlgorithm;
            $eventItems['hash'] = $digitalResource->hash;
            $eventItems['address'] = $archive->storagePath;

            $this->lifeCycleJournalController->logEvent('recordsManagement/consultation', 'digitalResource/digitalResource', $digitalResource->resId, $eventItems);
        }
    }

    /**
     * Verification of the communication date for access
     *
     * @param recordsManagement/archive $archive The archive to verify
     *
     * @return boolean The access right
     */
    private function accessComDateVerification($archive)
    {
        $access = true;

        if ($archive->accessRuleComDate) {
            $communicationDelay = $archive->accessRuleComDate->diff(\laabs::newTimestamp());
            $access = $communicationDelay->invert == 0 ? true : false;
        }

        return $access;
    }
}
