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
     */
    public function getMetadata($archiveId)
    {
        // Récupérer les métadonnées
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

            // Archiver
            if ($userServiceOrgRegNumber == (string) $archive->archiverOrgRegNumber) {
                return true;
            }

            // Originator
            if ($userServiceOrgRegNumber == (string) $archive->originatorOrgRegNumber) {
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
     */
    public function getPackage($archiveId)
    {
        // Récupérer les paquets (data+méta)
    }

    /**
     * Get an archive package for the communication
     *
     * @param string $archiveId The archive identifier
     */
    public function getConmmunicationPackage($archiveId)
    {
        // Constituer les paquets à communiquer
    }

    /**
     * Send archive for consultation
     */
    public function sendForConsultation()
    {
        // Envoyer pour consultation simple
    }

    /**
     * Log the archive access
     *
     * @param recordsManagement/archive $archive The archive logged
     */
    public function logging($archive)
    {
        // Journaliser
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
