<?php

/*
 *  Copyright (C) 2017 Maarch
 * 
 *  This file is part of bundle XXXX.
 *  Bundle recordsManagement is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  Bundle recordsManagement is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
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
        
        if (!empty($archive->descriptionClass)) {
            $descriptionController = $this->useDescriptionController($archive->descriptionClass);
            $archive->descriptionObject = $descriptionController->read($archive->archiveId);
        } else {
            $index = 'archives';
            if (!empty($archive->archivalProfileReference)) {
                $index = $archive->archivalProfileReference;
            }

            $ft = \laabs::newService('dependency/fulltext/FulltextEngineInterface');
            $ftresults = $ft->find('archiveId:"'.$archiveId.'"', $index, $limit = 1);

            if (count($ftresults)) {
                $archive->descriptionObject = $ftresults[0];
            }
        }

        $archive->digitalResources = $this->digitalResourceController->getResourcesByArchiveId($archiveId);

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

        return $archive;
    }

    /**
     * Get an archive package for the communication
     *
     * @param string $archiveId The archive identifier
     */
    public function getCommunicationPackage($archiveId)
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

    /**
     * Get archive assert
     * @param array $args
     * 
     * @return string
     */
    public function getArchiveAssert($args)
    {
        // Args on archive
        $currentDate = \laabs::newDate();
        $currentDateString = $currentDate->format('Y-m-d');

        $queryParts = [];
        if (!empty($args['archiveName'])) {
            $queryParts[] = "archiveName='*".$args['archiveName']."*'";
        }
        if (!empty($args['profileReference'])) {
            $queryParts[] = "archivalProfileReference='".$args['profileReference']."'";
        }
        if (!empty($args['agreementReference'])) {
            $queryParts[] = "archivalAgreementReference='".$args['agreementReference']."'";
        }
        if (!empty($args['archiveId'])) {
            $queryParts[] = "archiveId='".$args['archiveId']."'";
        }
        if (!empty($args['status'])) {
            $queryParts[] = "status='".$args['status']."'";
        }
        if (!empty($args['archiveExpired']) && $args['archiveExpired'] == "true") {
            $queryParts[] = "disposalDate<='".$currentDateString."'";
        }
        if (!empty($args['archiveExpired']) && $args['archiveExpired'] == "false") {
            $queryParts[] = "disposalDate>='".$currentDateString."'";
        }
        if (!empty($args['finalDisposition'])) {
            $queryParts[] = "finalDisposition='".$args['finalDisposition']."'";
        }
        if (!empty($args['originatorOrgRegNumber'])) {
            $queryParts[] = "originatorOrgRegNumber='".$args['originatorOrgRegNumber']."'";
        }
        if (!empty($args['originatorArchiveId'])) {
            $queryParts[] = "originatorArchiveId='".$args['originatorArchiveId']."'";
        }
        if (!empty($args['originatingDate'])) {
            if (!empty($args['originatingDate'][0])) {
                $queryParts[] = "originatingDate>='".$args['originatingDate'][0]."'";
            }
            if (!empty($args['originatingDate'][1])) {
                $queryParts[] = "originatingDate<='".$args['originatingDate'][1]."'";
            }
        }
        if (!empty($args['depositorOrgRegNumber'])) {
            $queryParts[] = "depositorOrgRegNumber='".$args['depositorOrgRegNumber']."'";
        }
        if (!empty($args['filePlanPosition'])) {
            $queryParts[] = "filePlanPosition='".$args['filePlanPosition']."'";
        }
        if ($args['hasParent'] == true) {
            $queryParts[] = "parentArchiveId!=null";
        }
        if ($args['hasParent']  === false) {
            $queryParts[] = "parentArchiveId=null";
        }

        $accessRuleAssert = $this->getAccessRuleAssert($currentDateString);
        if ($accessRuleAssert) {
            $queryParts[] = $accessRuleAssert;
        }

        return implode(' and ', $queryParts);
    }

    /**
     * Get the query assert for access rule
     * @param string $currentDateString the date
     * 
     * @return string
     */
    public function getAccessRuleAssert($currentDateString)
    {
        $currentService = \laabs::getToken("ORGANIZATION");
        if (!$currentService) {
            return "true=false";
        }

        $userServiceOrgRegNumbers = array_merge(array($currentService->registrationNumber), $this->userPositionController->readDescandantService((string) $currentService->orgId));

        $owner = false;
        foreach ($userServiceOrgRegNumbers as $userServiceOrgRegNumber) {
            $userService = $this->organizationController->getOrgByRegNumber($userServiceOrgRegNumber);
            if (isset($userService->orgRoleCodes) && $userService->orgRoleCodes->contains('owner')) {
                return;
            }
        }

        $queryParts['originator'] = "originatorOrgRegNumber=['".implode("', '", $userServiceOrgRegNumbers)."']";
        $queryParts['archiver'] = "archiverOrgRegNumber=['".implode("', '", $userServiceOrgRegNumbers)."']";
        //$queryParts['depositor'] = "depositorOrgRegNumber=['". implode("', '", $userServiceOrgRegNumbers) ."']";

        $queryParts['accessRule'] = "(originatorOwnerOrgId = '".$currentService->ownerOrgId."' AND (accessRuleComDate <= '$currentDateString'))";

        return "(".implode(" OR ", $queryParts).")";
    }
}
