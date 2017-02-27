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
 * Class for Records Management archives
 */
class archive
{

    use archiveEntryTrait,
        archiveAccessTrait,
        archiveCommunicationTrait,
        archiveModificationTrait,
        archiveRestitutionTrait,
        archiveComplianceTrait,
        archiveConversionTrait,
        archiveDestructionTrait;

    /**
     * Sdo Factory for management of archive persistance
     * @var dependency/sdo/Factory
     */
    protected $sdoFactory;

    /**
     * Controller for digital resource
     * @var digitalResource/Controller/digitalResource
     */
    protected $digitalResourceController;

    /**
     * Controller for access rules
     * @var recordsManagement/Controller/accessRule
     */
    protected $accessRuleController;

    /**
     * Controller for archive relationships
     * @var recordsManagement/Controller/archiveRelationship
     */
    protected $archiveRelationshipController;

    /**
     * Controller for archival profiles
     * @var recordsManagement/Controller/archivalProfile
     */
    protected $archivalProfileController;

    /**
     * Controller for service levels
     * @var recordsManagement/Controller/serviceLevel
     */
    protected $serviceLevelController;

    /**
     * Controller for life cycle journal events
     * @var recordsManagement/Controller/lifeCycleJournal
     */
    protected $lifeCycleJournalController;

    /**
     * Controller for life cycle journal events
     * @var recordsManagement/Controller/lifeCycleJournal
     */
    protected $organizationController;

    /**
     * Controller for user position
     * @var recordsManagement/Controller/userPosition
     */
    protected $userPositionController;

    /**
     * Controller for user position
     * @var recordsManagement/Controller/servicePosition
     */
    protected $servicePositionController;

    /**
     * Previously loaded archival profiles, indexed by reference
     * @var array
     */
    protected $archivalProfiles;

    /**
     * Currently used archival profile
     * @var recordsManagement/archivalProfile
     */
    protected $currentArchivalProfile;

    /**
     * Previously loaded service levels, indexed by reference
     * @var array
     */
    protected $serviceLevels;

    /**
     * Currently used service level
     * @var recordsManagement/serviceLevel
     */
    protected $currentServiceLevel;

    /**
     * Previously loaded description object controllers, indexed by reference
     * @var array
     */
    protected $descriptionControllers;

    /**
     * Currently description object controller
     * @var object
     */
    protected $currentDescriptionController;

    /**
     * The hash algo for resources
     * @var string
     */
    protected $hashAlgorithm;

    /**
     * Delete description when an archive is deleted
     * @var object
     */
    protected $deleteDescription;

    /**
     * The configuration to accept an archive when there is a conversion error
     * @var bool
     */
    protected $conversionError;

    /**
     * The configuration of the storage path
     * @var string
     */
    protected $storePath;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory        The dependency sdo factory service
     * @param string                  $hashAlgorithm     The hash algorithm for digital archives
     * @param string                  $deleteDescription The delete description
     * @param string                  $conversionError   The conversion error
     * @param string                  $storePath         The storage path
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $hashAlgorithm = 'SHA256', $deleteDescription = true, $conversionError = false, $storePath = null)
    {

        $this->hashAlgorithm = $hashAlgorithm;

        $this->deleteDescription = $deleteDescription;

        $this->sdoFactory = $sdoFactory;

        $this->digitalResourceController = \laabs::newController("digitalResource/digitalResource");

        $this->archiveRelationshipController = \laabs::newController("recordsManagement/archiveRelationship");

        $this->archivalProfileController = \laabs::newController("recordsManagement/archivalProfile");

        $this->serviceLevelController = \laabs::newController("recordsManagement/serviceLevel");

        $this->lifeCycleJournalController = \laabs::newController("lifeCycle/journal");

        $this->accessRuleController = \laabs::newController('recordsManagement/accessRule');

        $this->organizationController = \laabs::newController('organization/organization');

        $this->userPositionController = \laabs::newController('organization/userPosition');

        $this->servicePositionController = \laabs::newController('organization/servicePosition');

        $this->retentionRuleController = \laabs::newController("recordsManagement/retentionRule");

        $this->conversionError = (bool) $conversionError;

        $this->storePath = $storePath;
    }

    /**
     * Load archive references
     * @param object $archive   The archive
     * @param string $operation The requested operation: deposit, communication, modification, restitution, destruction
     */
    public function useReferences($archive, $operation)
    {

        if (!empty($archive->archivalProfileReference)) {
            $this->useArchivalProfile($archive->archivalProfileReference);
        }

        if (!empty($archive->serviceLevelReference)) {
            $this->useServiceLevel($operation, $archive->serviceLevelReference);
        } else {
            $this->useServiceLevel($operation);
        }

        if (!empty($archive->descriptionClass)) {
            $this->useDescriptionController($archive->descriptionClass);
        } else {
            $this->useDescriptionController('recordsManagement/description');
        }
    }

    /**
     * Select an archival profile for use
     * @param string $archivalProfileReference
     *
     * @return recordsManagement/archivalProfile
     */
    public function useArchivalProfile($archivalProfileReference)
    {
        if (!isset($this->archivalProfiles[$archivalProfileReference])) {
            $this->currentArchivalProfile = $this->archivalProfileController->getByReference($archivalProfileReference);
            $this->archivalProfiles[$archivalProfileReference] = $this->currentArchivalProfile;
        } else {
            $this->currentArchivalProfile = $this->archivalProfiles[$archivalProfileReference];
        }

        return $this->currentArchivalProfile;
    }

    /**
     * Select a service level for use
     * @param string $operation
     * @param string $serviceLevelReference
     *
     * @return recordsManagement/serviceLevel
     */
    public function useServiceLevel($operation, $serviceLevelReference = null)
    {
        if (!$serviceLevelReference) {
            $this->currentServiceLevel = $this->serviceLevelController->getDefault();

            $this->serviceLevels[$this->currentServiceLevel->reference] = $this->currentServiceLevel;
        } else {
            if (!isset($this->serviceLevels[$serviceLevelReference])) {
                $this->currentServiceLevel = $this->serviceLevelController->getByReference($serviceLevelReference);

                $this->serviceLevels[$serviceLevelReference] = $this->currentServiceLevel;
            } else {
                $this->currentServiceLevel = $this->serviceLevels[$serviceLevelReference];
            }
        }

        switch ($operation) {
            case 'deposit':
                $mode = 'write';
                $limit = true;
                break;

            case 'destruction':
                $mode = 'delete';
                $limit = false;
                break;

            case 'restitution':
            case 'communication':
            default:
                $mode = "read";
                $limit = true;
                break;
        }

        $digitalResourceCluster = $this->digitalResourceController->useCluster($this->currentServiceLevel->digitalResourceClusterId, $mode, $limit);

        $control = explode(" ", $this->currentServiceLevel->control);

        if (in_array("storeMetadata", $control) && !$digitalResourceCluster->storeMetadata) {
            throw \laabs::newException('recordsManagement/serviceLevelException', "The Service level requires the digital resource cluster to store metadata, but the selected cluster does not.");
        }

        if (in_array("storeMetadata", $control) && count($digitalResourceCluster->clusterRepository) < 2) {
            throw \laabs::newException('recordsManagement/serviceLevelException', "The Service level requires a redundant storage, but the selected cluster does not have sufficiant repositories.");
        }

        return $this->currentServiceLevel;
    }

    /**
     * Select a description controller
     * @param string $descriptionClass
     *
     * @return object
     */
    public function useDescriptionController($descriptionClass)
    {
        if (!isset($this->descriptionControllers[$descriptionClass])) {
            $this->currentDescriptionController = \laabs::newController($descriptionClass);

            $this->descriptionControllers[$descriptionClass] = $this->currentDescriptionController;
        } else {
            $this->currentDescriptionController = $this->descriptionControllers[$descriptionClass];
        }

        return $this->currentDescriptionController;
    }

    /**
     * Read an archive by its id
     * @param string $archiveId
     *
     * @return recordsManagement/archive
     */
    public function read($archiveId)
    {
        return $this->sdoFactory->read('recordsManagement/archive', $archiveId);
    }

    /**
     * Retrieve an archive by its id
     * @param string $archiveId
     *
     * @return recordsManagement/archive
     */
    public function retrieve($archiveId)
    {
        $archive = $this->read($archiveId);

        $this->getArchiveComponents($archive, true);

        return $archive;
    }

    /**
     * Get the archives by originator
     * @param string $originatorOrgRegNumber
     *
     * @return array
     */
    public function getArchiveByOriginator($originatorOrgRegNumber)
    {
        $archives = $this->sdoFactory->read("recordsManagement/archive", array('originatorOrgRegNumber' => $originatorOrgRegNumber));

        return $archives;
    }

    /**
     * Get the archive originator
     * @param string $archiveId
     *
     * @return string
     */
    public function getArchiveOriginatorOrgRegNumber($archiveId)
    {
        $archive = $this->sdoFactory->read("recordsManagement/archive", $archiveId);

        return $archive->originatorOrgRegNumber;
    }

    /**
     * Get archives by status
     * @param string $status
     *
     * @return recordsManagement/archive[]
     */
    public function getByStatus($status)
    {
        $archives = $this->sdoFactory->find('recordsManagement/archive', "status='$status'");

        return $archives;
    }

    /**
     * Retrieve an archive description by its archive id
     * @param string $archiveId The archive identifer
     *
     * @return recordsManagement/archive
     */
    public function getDescription($archiveId)
    {
        if (!$this->sdoFactory->exists('recordsManagement/archive', $archiveId)) {
            throw \laabs::newException("recordsManagement/unknownArchive", "The archive identifier '$archiveId' not exist");
        }

        $archive = $this->sdoFactory->read('recordsManagement/archive', $archiveId);

        if (!empty($archive->descriptionClass)) {
            $descriptionController = $this->useDescriptionController($archive->descriptionClass);
        } else {
            $descriptionController = $this->useDescriptionController('recordsManagement/description');
        }

        $archive->descriptionObject = $descriptionController->read($archive->archiveId);
        
        /*    $index = 'archives';
            if (!empty($archive->archivalProfileReference)) {
                $index = $archive->archivalProfileReference;
            }

            $ft = \laabs::newService('dependency/fulltext/FulltextEngineInterface');
            $ftresults = $ft->find('archiveId:"'.$archiveId.'"', $index, $limit = 1);

            if (count($ftresults)) {
                $archive->descriptionObject = $ftresults[0];
            }
        }*/

        $archive->lifeCycleEvent = $this->lifeCycleJournalController->getObjectEvents($archive->archiveId, 'recordsManagement/archive');

        $archive->digitalResources = $this->digitalResourceController->getResourcesByArchiveId($archive->archiveId);
        foreach ($archive->digitalResources as $i => $digitalResource) {
            $archive->digitalResources[$i] = $this->digitalResourceController->info($digitalResource->resId);
        }

        $archive->originatorOrg = $this->organizationController->getOrgByRegNumber($archive->originatorOrgRegNumber);
        if (isset($archive->archiverOrgRegNumber)) {
            $archive->archiverOrg = $this->organizationController->getOrgByRegNumber($archive->archiverOrgRegNumber);
        }
        if (isset($archive->depositorOrgRegNumber)) {
            $archive->depositorOrg = $this->organizationController->getOrgByRegNumber($archive->depositorOrgRegNumber);
        }

        $this->getParentArchive($archive);
        $this->getChildrenArchives($archive);

        $archive->childrenRelationships = $this->archiveRelationshipController->getByArchiveId($archive->archiveId);
        $archive->parentRelationships = $this->archiveRelationshipController->getByRelatedArchiveId($archive->archiveId);

        $archive->communicability = $this->accessVerification($archive);

        return $archive;
    }

    /**
     * Get the parent archive
     * @param recordsManagement/archive $archive The archive
     *
     * @return recordsManagement/archive
     */
    protected function getParentArchive($archive)
    {
        if (isset($archive->parentArchiveId)) {
            $archive->parentArchive = $this->sdoFactory->read("recordsManagement/archive", $archive->parentArchiveId);
        }

        return $archive;
    }

    /**
     * Get the children archives
     * @param recordsManagement/archive $archive The parent archive
     *
     * @return recordsManagement/archive Archive with children archives
     */
    protected function getChildrenArchives($archive)
    {
        $archive->childrenArchives = $this->sdoFactory->find("recordsManagement/archive", "parentArchiveId='".(string) $archive->archiveId."'");

        foreach ($archive->childrenArchives as $child) {
            $this->getChildrenArchives($child);
        }

        return $archive;
    }

    protected function getArchiveComponents($archive, $withContents = false)
    {
        $this->getAccessRule($archive);

        $archive->lifeCycleEvent = $this->lifeCycleJournalController->getObjectEvents($archive->archiveId, 'recordsManagement/archive');

        if (!empty($archive->descriptionClass)) {
            $descriptionController = $this->useDescriptionController($archive->descriptionClass);
            $archive->descriptionObject = $descriptionController->read($archive->archiveId);
        }

        $archiveDigitalResources = $this->digitalResourceController->getResourcesByArchiveId($archive->archiveId);
        foreach ($archiveDigitalResources as $digitalResource) {
            $archive->digitalResources[] = $this->digitalResourceController->retrieve($digitalResource->resId);
        }

        $archive->contents = $this->sdoFactory->find('recordsManagement/archive', "parentArchiveId = '".(string) $archive->archiveId."'");
        foreach ($archive->contents as $content) {
            $this->getArchiveComponents($content, $withContents);
        }

        $archive->relatedArchives = $this->archiveRelationshipController->getByArchiveId($archive->archiveId);
        $archive->relatedArchives = $this->archiveRelationshipController->getByRelatedArchiveId($archive->archiveId);

        $archive->originatorOrg = $this->organizationController->getOrgByRegNumber($archive->originatorOrgRegNumber);

    }

    /**
     * Change the status of an archive
     * @param mixed  $archiveIds Identifiers of the archives to update
     * @param string $status     New status to set
     *
     * @return array Archives ids separate by successfully updated archives ['success'] and not updated archives ['error']
     */
    public function setStatus($archiveIds, $status)
    {
        $statusList = [];
        $statusList['preserved'] = array('frozen', 'disposable', 'error');
        $statusList['frozen'] = array('preserved', 'disposable');
        $statusList['disposable'] = array('preserved');
        $statusList['disposed'] = array('disposable');
        $statusList['error'] = array('preserved', 'frozen', 'disposable', 'disposed');

        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = array('success' => array(), 'error' => array());

        if (!isset($statusList[$status])) {
            $res['error'] = $archiveIds;

            return $res;
        }
        foreach ($archiveIds as $archiveId) {
            $archiveStatus = $this->sdoFactory->read('recordsManagement/archiveStatus', $archiveId);

            if (!in_array($archiveStatus->status, $statusList[$status])) {
                array_push($res['error'], $archiveId);
            } else {
                $archiveStatus->status = $status;

                $childrenArchives = $this->sdoFactory->index('recordsManagement/archive', "archiveId", "parentArchiveId = '$archiveId'");
                $this->setStatus($childrenArchives, $status);

                $this->sdoFactory->update($archiveStatus);
                array_push($res['success'], $archiveId);
            }
        }

        return $res;
    }

    /**
     * Calculate the communication date of an archive
     * @param timestamp $startDate The start date
     * @param duration  $duration  The duration
     *
     * @return date
     */
    public function calculateDate($startDate, $duration)
    {
        if (empty($startDate) || empty($duration)) {
            return null;
        }
        if ($duration == "P999999999Y") {
            return $duration;
        }

        return $startDate->shift($duration);
    }

    /**
     * Check if the current user have the rights on an archive
     * @param recordsManagement/archive $archive
     *
     * @return boolean THe result of the operation
     */
    public function checkRights($archive)
    {
        $accountToken = \laabs::getToken('AUTH');
        $account = \laabs::newController('auth/userAccount')->edit($accountToken->accountId);


        $currentOrganization = \laabs::getToken("ORGANIZATION");
        $userOrgList = [];
        $positionController = null;

        if (!$currentOrganization) {
            return false;
        }

        if ($currentOrganization->orgRoleCodes && in_array("owner", $currentOrganization->orgRoleCodes)) {
            return true;
        }

        if ($account->accountType == "user") {
            $positionController = $this->userPositionController;
        } else {
            $positionController = $this->servicePositionController;
        }

        $userOrgList = $positionController->listMyServices();

        if (!(in_array($archive->originatorOrgRegNumber, $userOrgList) || in_array($archive->archiverOrgRegNumber, $userOrgList))) {
            throw \laabs::newException('recordsManagement/accessDeniedException', "Permission denied");
        }

        return true;
    }

    /**
     * Calculate access rule from archive
     * @param recordsManagement/archive         $archive
     * @param recordsManagement/archivalProfile $archivalProfile
     *
     * @return recordsManagement/accessRule[]
     */
    public function getAccessRule($archive, $archivalProfile = false)
    {
        $accessRules = array();
        if (!empty($archive->accessRuleCode)) {
            $accessRuleCode = $archive->accessRuleCode;
        } elseif (!empty($archive->archivalProfileReference)) {
            $archivalProfile = $this->archivalProfileController->getByReference($archive->archivalProfileReference);
            $accessRuleCode = $archivalProfile->accessRuleCode;
        } else {
            return;
        }
        if (!empty($accessRuleCode)) {
            $archive->accessRule = $this->accessRuleController->edit($accessRuleCode);
        }
    }

    /**
     * Check if archive exists
     * @param string $archiveId The archive identifier
     *
     * @return object Object with archiveId and a boolean 'exist'
     */
    public function exists($archiveId)
    {
        $result = new \stdClass();
        $result->archiveId = $archiveId;
        $result->exist = false;
        if ($this->sdoFactory->exists("recordsManagement/archive", array("archiveId" => $archiveId))) {
            $result->exist = true;
        }

        return $result;
    }

    /**
     * Find archives
     * @param string $q       The query string
     * @param string $profile The index
     * @param int    $limit   The result limit
     *
     * @return array The fulltext result
     */
    public function find($q = null, $profile = false, $limit = null)
    {
        $q = trim($q);
        if ($q == null || empty($q)) {
            throw new \bundle\recordsManagement\Exception\invalidParameterException("The query string is empty");
        }

        if ($limit < 1) {
            $limit = null;
        }

        $archivalProfiles = \laabs::newController("recordsManagement/archivalProfile")->index(true);

        $indexList = $descriptionClassList = [];
        foreach ($archivalProfiles as $archivalProfile) {
            if ($archivalProfile->descriptionClass == '') {
                $indexList[] = $archivalProfile->reference;
            } else {
                $descriptionClassList[] = $archivalProfile->descriptionClass;
            }
        }

        if ($profile) {
            if (in_array($profile, $indexList)) {
                $index = [$profile];
            } elseif (in_array($profile, $descriptionClassList)) {
                $descriptionClass = [$profile];
            } else {
                return [];
            }
        } else {
            $index = $indexList;
            $descriptionClass = $descriptionClassList;
        }

        $currentOrg = \laabs::getToken("ORGANIZATION");

        if (!$currentOrg) {
            return array();
        }

        $ftresults = [];

        if (isset($currentOrg->orgRoleCodes) && is_array($currentOrg->orgRoleCodes)) {
            $currentOrg->orgRoleCodes = \laabs\implode(" ", $currentOrg->orgRoleCodes);
        }

        if (count($index)) {
            $fulltextQueryString = [];
            $fulltextQueryString[] = $q;

            if (isset($currentOrg->orgRoleCodes) && strpos($currentOrg->orgRoleCodes, "owner") == false) {
                $orgRegNumbers = \laabs::newController("organization/userPosition")->listMyCurrentDescendantServices();

                $fulltextQueryString[] = " and originatorOrgRegNumber:(".\laabs\implode(" || ", $orgRegNumbers).")";
            }

            $ft = \laabs::newService('dependency/fulltext/FulltextEngineInterface');
            $ftresults = $ft->find(\laabs\implode(" ", $fulltextQueryString), $index, $limit);
        }

        if (count($descriptionClassList)) {
            $descriptionClassArgs = preg_split("# and #", $q);

            if (isset($currentOrg->orgRoleCodes) && strpos($currentOrg->orgRoleCodes, "owner") == false) {
                $orgRegNumbers = \laabs::newController("organization/userPosition")->listMyCurrentDescendantServices();

                $fulltextQueryString[] = " and originatorOrgRegNumber:(".\laabs\implode(" || ", $orgRegNumbers).")";
            }
        }

        return $ftresults;
    }
}
