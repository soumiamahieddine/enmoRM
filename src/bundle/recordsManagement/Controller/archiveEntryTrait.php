<?php

/*
 *  Copyright (C) 2017 Maarch
 *
 *  This file is part of bundle recordsManagement.
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
 * Archive entry controller
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
trait archiveEntryTrait
{
    protected $originatorOrgs;

    /**
     * Instanciate a new archive
     *
     * @return recordsManagement/archive An archive
     */
    public function newArchive()
    {
        // Use archive xml parser for archive creation
        $archive = \laabs::newInstance('recordsManagement/archive');

        // Generate archive id
        if (!isset($archive->archiveId)) {
            $archive->archiveId = \laabs::newId();
        }

        $archive->timestamp = \laabs::newTimestamp();
        $archive->status = 'received';

        // Use current profile
        if (isset($this->currentArchivalProfile)) {
            $archive->archivalProfileReference = $this->currentArchivalProfile->reference;

            $archive->descriptionClass = $this->currentArchivalProfile->descriptionClass;

            $archive->accessRuleCode = $this->currentArchivalProfile->accessRuleCode;

            if ($this->currentArchivalProfile->retentionRuleCode) {
                $archive->retentionRuleCode = $this->currentArchivalProfile->retentionRuleCode;
            }
        }

        // Use current service level
        if (isset($this->currentServiceLevel)) {
            $archive->serviceLevelReference = $this->currentServiceLevel->reference;
        }

        return $archive;
    }

    /**
     * Receive an archive
     *
     * @param recordsManagement/archive $archive      The archive to receive
     * @param bool                      $zipContainer The archive is a zip container
     */
    public function receive($archive, $zipContainer = false)
    {
        $archive = \laabs::cast($archive, 'recordsManagement/archive');

        if (!isset($archive->archiveId)) {
            $archive->archiveId = \laabs::newId();
        }
        $archive->status = "received";
        $archive->depositDate = \laabs::newTimestamp();

        if ($zipContainer) {
            $archive = $this->processZipContainer($archive);
        }

        // Load archival profile, service level if specified
        // Instantiate description controller
        $this->useReferences($archive, 'deposit');

        // Complete management metadata from profile and service level
        $this->completeMetadata($archive);

        $this->useReferences($archive, 'deposit');

        // Validate metadata
        $this->validateCompliance($archive);

        // Check format conversion
        $this->convertArchive($archive);

        // Generate PDI + package
        $this->generateAIP($archive);

        // Deposit
        $this->deposit($archive);

        // Send certificate
        $this->sendResponse($archive);

        return $archive->archiveId;
    }

    /**
     * Process a zipContainer
     *
     * @param recordsManagement/archive $archive The archive
     *
     * @return recordsManagement/archive An archive
     */
    public function processZipContainer($archive)
    {
        $zip = $archive->digitalResources[0];

        $zipDirectory = $this->extractZip($zip);

        $archive->digitalResources = [];
        $cleanZipDirectory = array_diff(scandir($zipDirectory), array('..', '.'));
        $directory = $zipDirectory . DIRECTORY_SEPARATOR . reset($cleanZipDirectory);

        if (!is_dir($directory)) {
            throw new \core\Exception("The container file is non-compliant");
        }

        $scannedDirectory = array_diff(scandir($directory), array('..', '.'));

        foreach ($scannedDirectory as $filename) {
            if (is_link($directory . DIRECTORY_SEPARATOR . $filename)) {
                throw new \core\Exception("The container file contains symbolic links");
            }

            if (\laabs::strStartsWith($filename, $archive->archivalProfileReference . " ")) {
                $resource = $this->extractResource($directory, $filename);
                $resource->setContents(base64_encode($resource->getContents()));
                $archive->digitalResources[] = $resource;
            } else {
                $archiveUnit = $this->extractArchiveUnit($filename);
                $archiveUnit->archiveId = \laabs::newId();
                $resource = $this->extractResource($directory, $filename);
                $resource->setContents(base64_encode($resource->getContents()));
                $archiveUnit->digitalResources[] = $resource;
                $archive->contents[] = $archiveUnit;
            }
        }

        return $archive;
    }

    /**
     * Extract zip
     *
     * @param resource $zip
     *
     * @return string The direcotry path where the zip is extract
     */
    private function extractZip($zip)
    {
        $packageDir = \laabs\tempdir() . DIRECTORY_SEPARATOR . "MaarchRM" . DIRECTORY_SEPARATOR;

        if (!is_dir($packageDir)) {
            mkdir($packageDir, 0777, true);
        }

        $name = \laabs::newId();
        $zipfile = $packageDir . $name . ".zip";

        if (!is_dir($packageDir . $name)) {
            mkdir($packageDir . $name, 0777, true);
        }

        file_put_contents($zipfile, base64_decode($zip->getContents()));

        $this->zip->extract($zipfile, $packageDir. $name, false, null, "x");

        return $packageDir . $name;
    }

    /**
     * Extract the archive unit
     *
     * @param string $filename The filename
     *
     * @return recordsManagement/archive The extracted archive from directory
     */
    private function extractArchiveUnit($filename)
    {
        if (!preg_match('//u', $filename)) {
            $filename = utf8_encode($filename);
        }

        $archivalProfileReference = strtok($filename, " ");
        $archiveName = substr($filename, strlen($archivalProfileReference)+1);
        $archiveName = preg_replace('/\\.[^.\\s]{3,4}$/', '', $archiveName);

        $archive = \laabs::newInstance("recordsManagement/archive");
        $archive->archiveId = \laabs::newId();
        $archive->archivalProfileReference = $archivalProfileReference;
        $this->useArchivalProfile($archivalProfileReference);
        $archive->archiveName = $archiveName . " _ " . $this->currentArchivalProfile->name;

        return $archive;
    }

    /**
     * Extract a resource
     *
     * @param string $resourceDirectory The directory of the resource
     * @param string $filename          The filename
     *
     * @return digitalResource/digitalResource The extracted digital resource
     */
    private function extractResource($resourceDirectory, $filename)
    {
        $resource = $this->digitalResourceController->createFromFile($resourceDirectory . DIRECTORY_SEPARATOR . $filename, false);

        $this->digitalResourceController->getHash($resource, "SHA256");

        return $resource;
    }

    /**
     * Receive an archive
     *
     * @param string $batchDirectory      The path of the folder that contains archives
     * @param string $descriptionFilePath The path of the description file
     *
     * @return bool The result of the operation
     */
    public function receiveArchiveBatch($batchDirectory, $descriptionFilePath)
    {
        if (!is_dir($batchDirectory)) {
            throw new \core\Exception\NotFoundException("The batch folder does not exist.");
        }

        $descriptionFile = file_get_contents($descriptionFilePath);

        if (!$descriptionFile) {
            throw new \core\Exception("The description file can not be read.");
        }

        $archives = json_decode($descriptionFile);
        if (!$descriptionFile) {
            throw new \core\Exception("The description file is malformed.");
        }

        foreach ($archives as $archive) {
            foreach ($archive->digitalResources as $digitalResource) {
                $filePath = $batchDirectory . DIRECTORY_SEPARATOR . $digitalResource->fileName;

                $fileContent = file_get_contents($filePath);
                $digitalResource->handler = base64_encode($fileContent);
                $digitalResource->size = filesize($filePath);
            }

            $this->receive($archive);
        }

        return true;
    }

    /**
     * Complete the archive metadata
     *
     * @param recordsManagement/archive $archive The archive to complete
     */
    public function completeMetadata($archive)
    {
        // Set archive name when mono document
        if (empty($archive->archiveName)) {
            if (count($archive->digitalResources)) {
                foreach ($archive->digitalResources as $digitalResource) {
                    if (isset($digitalResource->fileName)) {
                        $archive->archiveName = pathinfo($digitalResource->fileName, \PATHINFO_FILENAME);

                        break;
                    }
                }
            } else {
                if (!empty($archive->archivalProfileReference)) {
                    $archivalProfile = $this->useArchivalProfile($archive->archivalProfileReference);
                    $archive->archiveName .= $archivalProfile->name;
                }
                if (!empty($archive->originatorArchiveId)) {
                    $archive->archiveName .= ' '.$archive->originatorArchiveId;
                }
                if (!empty($archive->originatingDate)) {
                    $archive->archiveName .= ' '.$archive->originatingDate;
                }

                $archive->archiveName = trim($archive->archiveName);

                if (empty($archive->archiveName)) {
                    throw new \core\Exception\BadRequestException('You must define at least the name, the identifier, the date of the document or a document', 400);
                }
            }
        }

        $this->completeManagementMetadata($archive);

        $this->completeProcessingStatus($archive);

        $this->manageFileplanPosition($archive);

        if (empty($archive->descriptionClass) && isset($this->currentArchivalProfile->descriptionClass)) {
            $archive->descriptionClass = $this->currentArchivalProfile->descriptionClass;
        }

        if (!empty($archive->contents)) {
            $nbArchiveObjects = count($archive->contents);
            for ($i = 0; $i < $nbArchiveObjects; $i++) {
                $archive->contents[$i]->serviceLevelReference = $archive->serviceLevelReference;
                $this->useReferences($archive->contents[$i], 'deposit');
                $archive->contents[$i]->fullTextIndexation = $archive->fullTextIndexation;
                $this->completeMetadata($archive->contents[$i]);
            }
        }
    }

    /**
     * Complete fileplanPosition with a directoryPath
     *
     * @param recordsManagement/archive $archive The archive to complete
     */
    protected function manageFileplanPosition($archive)
    {
        // Could classify automatically regarding archival profile rule
        if (empty($archive->filePlanPosition)) {
            return;
        }

        // File plan position is given as a path that must be opened (create if not exists)
        // to retrieve the folderId
        if ($archive->filePlanPosition[0] == '/') {
            $path = substr($archive->filePlanPosition, 1);
            $archive->filePlanPosition = $this->filePlanController->createFromPath($path, $archive->originatorOrgRegNumber, true);

            return;
        }
    }

    /**
     * Complete management metadata
     *
     * @param recordsManagement/archive $archive The archive to complete
     */
    protected function completeManagementMetadata($archive)
    {
        $this->completeArchivalProfileCodes($archive);
        $this->completeRetentionRule($archive);
        $this->completeAccessRule($archive);
        $this->completeServiceLevel($archive);
        $this->completeOriginator($archive);
        $this->completeArchiver($archive);
    }

    protected function completeOriginator($archive)
    {
        // Originator
        if (empty($archive->originatorOrgRegNumber)) {
            $currentOrg = \laabs::getToken("ORGANIZATION");
            if ($currentOrg) {
                $archive->originatorOrgRegNumber = $currentOrg->registrationNumber;
            }
        }

        if (!isset($this->originatorOrgs[$archive->originatorOrgRegNumber])) {
            $originator = $this->organizationController->getOrgByRegNumber($archive->originatorOrgRegNumber);
            $this->originatorOrgs[$archive->originatorOrgRegNumber] = $originator;
        } else {
            $originator = $this->originatorOrgs[$archive->originatorOrgRegNumber];
        }

        $archive->originatorOwnerOrgId = $originator->ownerOrgId;
        $originatorOrg = $this->organizationController->read($originator->ownerOrgId);
        $archive->originatorOwnerOrgRegNumber = $originatorOrg->registrationNumber;
    }

    protected function completeArchiver($archive)
    {
        if (empty($archive->archiverOrgRegNumber)) {
            $ownerOrgs = $this->organizationController->getOrgsByRole('owner');
            if (count($ownerOrgs) > 0) {
                $archive->archiverOrgRegNumber = $ownerOrgs[0]->registrationNumber;
            }
        }
    }

    /**
     * Complete management codes with the archival profile
     *
     * @param recordsManagement/archive $archive The archive to complete
     */
    public function completeArchivalProfileCodes($archive)
    {
        if ($archive->archivalProfileReference == "") {
            return;
        }

        $archivalProfile = $this->useArchivalProfile($archive->archivalProfileReference);

        if (!empty($archivalProfile->retentionRuleCode)) {
            $archive->retentionRuleCode = $archivalProfile->retentionRuleCode;
        }

        if (!empty($archivalProfile->accessRuleCode)) {
            $archive->accessRuleCode = $archivalProfile->accessRuleCode;
        }

        if (!empty($archivalProfile->retentionStartDate)) {
            $archive->retentionStartDate = $archivalProfile->retentionStartDate;
        }

        if (empty($archive->fileplanLevel)) {
            $archive->fileplanLevel = 'file';
            if (!empty($archivalProfile->fileplanLevel)) {
                $archive->fileplanLevel = $archivalProfile->fileplanLevel;
            }
        }
    }
    /**
     * Complete the access rule metadata
     *
     * @param recordsManagement/archive $archive The archive to complete
     */
    public function completeAccessRule($archive)
    {
        if (!empty($archive->accessRuleCode)) {
            $accessRule = $this->accessRuleController->edit($archive->accessRuleCode);
            $archive->accessRuleDuration = $accessRule->duration;
        }

        if (!empty($archive->accessRuleStartDate) && !empty($archive->accessRuleDuration)) {
            $archive->accessRuleComDate = $archive->accessRuleStartDate->shift($archive->accessRuleDuration);
        }
    }

    /**
     * Complete the retention rule metadata
     *
     * @param recordsManagement/archive $archive The archive to complete
     */
    public function completeRetentionRule($archive)
    {
        if (!empty($archive->retentionRuleCode)) {
            $retentionRule = $this->retentionRuleController->read($archive->retentionRuleCode);

            $archive->retentionDuration =  $retentionRule->duration;

            if (!$archive->finalDisposition) {
                $archive->finalDisposition =  $retentionRule->finalDisposition;
            }
        }

        if (is_string($archive->retentionStartDate)) {
            switch ($archive->retentionStartDate) {
                case 'originatingDate':
                    $archive->retentionStartDate = $archive->originatingDate;
                    break;

                case 'depositDate':
                    $archive->retentionStartDate = \laabs::newDate();
                    break;

                default:
                    $qname = \laabs\explode("/", $archive->retentionStartDate);
                    if ($qname[0] == "description" && isset($archive->descriptionObject->{$qname[1]})) {
                        $archive->retentionStartDate = \laabs::newDate($archive->descriptionObject->{$qname[1]});
                    } else {
                        $archive->retentionStartDate = null;
                    }
            }
        }

        $archive->disposalDate = null;
        if (!empty($archive->retentionStartDate) && !empty($archive->retentionDuration) && $archive->retentionDuration->y < 9999) {
            $archive->disposalDate = $archive->retentionStartDate->shift($archive->retentionDuration);
        }
    }

    /**
     * Complete the service level
     *
     * @param recordsManagement/archive $archive The archive to complete
     */
    public function completeServiceLevel($archive)
    {
        if (!empty($archive->archivalProfileReference) && empty($archive->serviceLevelReference)) {
            $currentOrg = \laabs::getToken("ORGANIZATION");
            $archivalProfileAccess = $this->organizationController->getOrgUnitArchivalProfile($currentOrg->orgId, $archive->archivalProfileReference);

            if (!empty($archivalProfileAccess)) {
                $archive->serviceLevelReference = $archivalProfileAccess->serviceLevelReference;
            }
        }

        $this->useServiceLevel('deposit', $archive->serviceLevelReference);

        $archive->serviceLevelReference = $this->currentServiceLevel->reference;

        if (strpos($this->currentServiceLevel->control, "fullTextIndexation")) {
            $archive->fullTextIndexation = "requested";
        } else {
            $archive->fullTextIndexation = "none";
        }
    }

    /**
     * Complete processing status
     *
     * @param recordsManagement/archive $archive The archive to complete
     */
    protected function completeProcessingStatus($archive)
    {
        if (!empty($archive->processingStatus) || !isset($archive->archivalProfileReference)) {
            return;
        }

        $processingStatuses = json_decode($this->currentArchivalProfile->processingStatuses);

        if (empty($processingStatuses)) {
            return;
        }

        // Recovery Initial and Default statuses if exists ...
        foreach ($processingStatuses as $code => $processingStatus) {
            if ($processingStatus->default == true) {
                $archive->processingStatus = $code;
            }
        }
    }

    /**
     * Validate the archive compliance
     *
     * @param recordsManagement/archive $archive The archive to validate
     */
    public function validateCompliance($archive)
    {
        $this->validateFileplan($archive);
        $this->validateArchiveDescriptionObject($archive);
        $this->validateManagementMetadata($archive);
        $this->validateAttachments($archive);
        $this->validateProcessingStatus($archive);
    }

    /**
     * Check and set the processing status
     *
     * @param recordsManagement/archive $archive The archive to setting
     */
    public function validateProcessingStatus($archive)
    {
        if (empty($archive->processingStatus) || !isset($this->currentArchivalProfile)) {
            return;
        }

        $processingStatuses = json_decode($this->currentArchivalProfile->processingStatuses);

        if (!isset($processingStatuses->{$archive->processingStatus})) {
            throw new \core\Exception\BadRequestException("The processing status isn't initial");
        }

        $archiveProcessingStatus = $processingStatuses->{$archive->processingStatus};

        if ($archiveProcessingStatus->type != 'initial') {
            throw new \core\Exception\BadRequestException("The processing status isn't initial");
        }
    }

    /**
     * Validate archive description object
     *
     * @param recordsManagement/archive $archive The archive object
     */
    protected function validateArchiveDescriptionObject($archive)
    {
        if (!isset($this->currentArchivalProfile)) {
            return;
        }

        $this->validateDescriptionModel($archive->descriptionObject, $this->currentArchivalProfile);
    }

    protected function validateDescriptionModel($object, $archivalProfile)
    {
        $descriptionSchemeProperties = $this->descriptionSchemeController->getDescriptionFields($archivalProfile->descriptionClass);

        $archivalProfileFields = [];
        foreach ($archivalProfile->archiveDescription as $archiveDescription) {
            if (!isset($object->{$archiveDescription->fieldName}) && $archiveDescription->required) {
                throw new \core\Exception\BadRequestException('Null value not allowed for metadata %1$s', 400, null, [$archiveDescription->fieldName]);
            }

            $archivalProfileFields[$archiveDescription->fieldName] = $archiveDescription;
        }

        foreach ($object as $name => $value) {
            if (!isset($archivalProfileFields[$name]) && !$archivalProfile->acceptUserIndex) {

                throw new \core\Exception\BadRequestException('Metadata %1$s is not allowed', 400, null, [$name]);
            }

            if (isset($descriptionSchemeProperties[$name])) {
                $this->validateDescriptionField($value, $descriptionSchemeProperties[$name]);
            }
        }
    }

    protected function validateDescriptionField($value, $descriptionField)
    {
        $type = $descriptionField->type;
        switch ($type) {
            case 'name':
                if (!empty($descriptionField->enumeration) && !in_array($value, $descriptionField->enumeration)) {
                    throw new \core\Exception\BadRequestException('Forbidden value for metadata %1$s', 400, null, [$descriptionField->name]);
                }
                break;

            case 'text':
                break;

            case 'number':
                if (!is_int($value) && !is_float($value)) {
                    throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$descriptionField->name]);
                }
                break;

            case 'boolean':
                if (!is_bool($value) && !in_array($value, [0, 1])) {
                    throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$descriptionField->name]);
                }
                break;

            case 'date':
                if (!is_string($value)) {
                    throw new \core\Exception\BadRequestException('Invalid value for metadata %1$s', 400, null, [$descriptionField->name]);
                }
                break;
        }
    }

    /**
     * Validate the archive management metadata
     *
     * @param \bundle\recordsManagement\Controller\recordsManagement/archive $archive
     */
    protected function validateManagementMetadata($archive)
    {
        if (isset($archive->archivalProfileReference) && !$this->sdoFactory->exists("recordsManagement/archivalProfile", ["reference"=>$archive->archivalProfileReference])) {
            throw new \core\Exception\NotFoundException("The archival profile reference not found");
        }

        if (isset($archive->retentionRuleCode) && !$this->sdoFactory->exists("recordsManagement/retentionRule", $archive->retentionRuleCode)) {
            throw new \core\Exception\NotFoundException("The retention rule not found");
        }

        if (isset($archive->accessRuleCode) && !$this->sdoFactory->exists("recordsManagement/accessRule", $archive->accessRuleCode)) {
            throw new \core\Exception\NotFoundException("The access rule not found");
        }

        $nbArchiveObjects = 0;

        if (!empty($archive->contents)) {
            $nbArchiveObjects = count($archive->contents);
        }

        if ($nbArchiveObjects) {
            $containedProfiles = [];

            $this->useArchivalProfile($archive->archivalProfileReference);
            foreach ($this->currentArchivalProfile->containedProfiles as $profile) {
                $containedProfiles[] = $profile->reference;
            }

            for ($i = 0; $i < $nbArchiveObjects; $i++) {
                if (empty($archive->contents[$i]->archivalProfileReference)) {
                    if (!$this->currentArchivalProfile->acceptArchiveWithoutProfile) {
                        throw new \core\Exception\BadRequestException("Invalid contained archive profile %s", 400, null, $archive->contents[$i]->archivalProfileReference);
                    }
                } elseif (!in_array($archive->contents[$i]->archivalProfileReference, $containedProfiles)) {
                    throw new \core\Exception\BadRequestException("Invalid contained archive profile %s", 400, null, $archive->contents[$i]->archivalProfileReference);
                }

                $this->validateManagementMetadata($archive->contents[$i]);
            }

        }
    }

    /**
     * Validate archival profile of a child archive
     *
     * @param \bundle\recordsManagement\Controller\recordsManagement/archive $archive
     */
    protected function validateFileplan($archive)
    {
        // No parent, check orgUnit can deposit with the profile
        if (empty($archive->parentArchiveId)) {
            if (!$this->organizationController->checkProfileInOrgAccess($archive->archivalProfileReference, $archive->originatorOrgRegNumber)) {
                throw new \core\Exception\BadRequestException("Invalid archive profile");
            }

            return;
        }

        // Parent : read and check fileplan
        $parentArchive = $this->sdoFactory->read('recordsManagement/archive', $archive->parentArchiveId);

        // Check level in file plan
        if ($parentArchive->fileplanLevel == 'item') {
            throw new \core\Exception\BadRequestException("Parent archive is an item and can not contain items.");
        }

        // No profile on parent, accept any profile
        if (empty($parentArchive->archivalProfileReference)) {
            return;
        }

        // Load parent archive profile
        if (!isset($this->archivalProfiles[$parentArchive->archivalProfileReference])) {
            $parentArchivalProfile = $this->archivalProfileController->getByReference($parentArchive->archivalProfileReference);
            $this->archivalProfiles[$parentArchive->archivalProfileReference] = $parentArchivalProfile;
        } else {
            $parentArchivalProfile = $this->archivalProfiles[$parentArchive->archivalProfileReference];
        }

        // No profile : check parent profile accepts archives without profile
        if (empty($archive->archivalProfileReference) && $parentArchivalProfile->acceptArchiveWithoutProfile) {
            return;
        }

        // Profile on content : check profile is accepted
        foreach ($parentArchivalProfile->containedProfiles as $containedProfile) {
            if ($containedProfile->reference == $archive->archivalProfileReference) {
                return;
            }
        }

        throw new \core\Exception\BadRequestException("Invalid archive profile");
    }


    /**
     * Validate the archive management metadata
     *
     * @param \bundle\recordsManagement\Controller\recordsManagement/archive $archive
     */
    protected function validateAttachments($archive)
    {
        if (!$archive->digitalResources) {
            $archive->digitalResources = [];
        } else {
            $formatDetection = strrpos($this->currentServiceLevel->control, "formatDetection") === false ? false : true;
            $formatValidation = strrpos($this->currentServiceLevel->control, "formatValidation") === false ? false : true;
        }

        foreach ($archive->digitalResources as $digitalResource) {
            if (empty($digitalResource->archiveId)) {
                $digitalResource->archiveId = $archive->archiveId;
            }

            if (empty($digitalResource->resId)) {
                $digitalResource->resId = \laabs::newId();
            }

            $contents = base64_decode($digitalResource->getContents());

            $digitalResource->setContents($contents);

            $filename = tempnam(sys_get_temp_dir(), 'digitalResource.format');
            file_put_contents($filename, $contents);

            if ($formatDetection) {
                $format = $this->formatController->identifyFormat($filename);

                if ($format) {
                    $digitalResource->puid = $format->puid;
                }
            }

            if ($formatValidation) {
                $validation = $this->formatController->validateFormat($filename);
                if (!$validation !== true && is_array($validation)) {
                    throw new \core\Exception\BadRequestException("Invalid format attachments for %s", 404, null, [$digitalResource->fileName]);
                }
            }

            if (empty($digitalResource->hash) || empty($digitalResource->hashAlgorithm)) {
                $this->digitalResourceController->getHash($digitalResource, $this->hashAlgorithm);
            }
        }

        $nbArchiveObjects = 0;

        if (!empty($archive->contents)) {
            $nbArchiveObjects = count($archive->contents);
        }

        for ($i = 0; $i < $nbArchiveObjects; $i++) {
            $this->validateAttachments($archive->contents[$i]);
        }
    }

    /**
     * Convert resources of archive
     *
     * @param recordsManagement/archive $archive The archive to convert
     *
     * @return void
     */
    public function convertArchive($archive)
    {
        if (empty($this->currentServiceLevel)) {
            $this->useServiceLevel('deposit', $archive->serviceLevelReference);
        }

        if (strrpos($this->currentServiceLevel->control, "convertOnDeposit") == false) {
            return;
        }

        $conversionRules = \laabs::newController("digitalResource/conversionRule")->index();

        if (empty($conversionRules)) {
            return;
        }

        if (isset($archive->digitalResources)) {
            $nbResources = count($archive->digitalResources);
        } else {
            $nbResources = 0;
        }

        if (isset($archive->contents)) {
            $nbArchiveObjects = count($archive->contents);
        } else {
            $nbArchiveObjects = 0;
        }

        for ($i = 0; $i < $nbResources; $i++) {
            $convertedResource = $this->convertResource($archive, $archive->digitalResources[$i]);
            if ($convertedResource != false) {
                $archive->digitalResources[] = $convertedResource;
            }
        }

        for ($i = 0; $i < $nbArchiveObjects; $i++) {
            $this->convertArchive($archive->contents[$i]);
        }
    }

    /**
     * Generate the archival information package
     * @param recordsManagement/archive $archive The archive use to generate the AIP
     */
    public function generateAIP($archive)
    {

    }

    /**
     * Deposit a new archive
     *
     * @param recordsManagement/archive $archive The archive to deposit
     * @param string                    $path    The file plan position
     *
     * @return recordsManagement/archive The archive
     */
    public function deposit($archive, $path = null)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        if(!empty($archive->contents)) {
            $nbArchiveObjects = count($archive->contents);
        } else {
            $nbArchiveObjects = null;
        }

        try {
            $archive->status = 'preserved';
            $archive->depositDate = \laabs::newTimestamp();

            $this->openContainers($archive, $path);

            $this->sdoFactory->create($archive, 'recordsManagement/archive');

            if (!empty($archive->digitalResources)) {
                $this->storeResources($archive);
            }
            $this->storeDescriptiveMetadata($archive);

            for ($i = 0; $i < $nbArchiveObjects; $i++) {
                $archive->contents[$i]->parentArchiveId = $archive->archiveId;
                $archive->contents[$i]->originatorOwnerOrgId = $archive->originatorOwnerOrgId;
                $archive->contents[$i]->serviceLevelReference = $archive->serviceLevelReference;

                $this->deposit($archive->contents[$i], $archive->storagePath);
            }
        } catch (\Exception $exception) {
            $nbResources = 0;
            if (!is_null($archive->digitalResources)) {
                $nbResources = count($archive->digitalResources);
            }
            for ($i = 0; $i < $nbResources; $i++) {
                $this->digitalResourceController->rollbackStorage($archive->digitalResources[$i]);
            }

            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();

            // TimeStamp last modification date of the parent archive.
            if (!empty($archive->parentArchiveId)) {
                $parentArchive = $this->sdoFactory->read('recordsManagement/archive', $archive->parentArchiveId);
                $parentArchive->lastModificationDate = \laabs::newTimestamp();
                $this->sdoFactory->update($parentArchive, 'recordsManagement/archive');
            }
        }

        $this->logDeposit($archive);

        return $archive;
    }

    /**
     * Send response
     */
    public function sendResponse()
    {
    }

    protected function openContainers($archive, $path = null)
    {
        if (empty($path)) {
            if (isset($archive->parentArchiveId)) {
                $parentArchive = $this->sdoFactory->read('recordsManagement/archive', $archive->parentArchiveId);

                $path = $parentArchive->storagePath;
            } else {
                if (!$this->storePath) {
                    $path = $archive->originatorOwnerOrgId."/".$archive->originatorOrgRegNumber;
                } else {
                    $path = $this->storePath;
                }

                $path = $this->resolveStoragePath($archive, $path);
            }
        } else {
            $path = $this->resolveStoragePath($archive, $path);
        }

        // Add archiveId as container name in path
        $path .= "/".$archive->archiveId;

        if (!$this->currentServiceLevel) {
            if (isset($archive->serviceLevelReference)) {
                $this->useServiceLevel('deposit', $archive->serviceLevelReference);
            } else {
                $this->useServiceLevel('deposit');
            }
        }

        $metadata = get_object_vars($archive);
        unset($metadata['contents']);
        foreach ($metadata as $name => $value) {
            if (is_null($value)) {
                unset($metadata[$name]);
            }
        }

        $storagePath = $this->digitalResourceController->openContainers($this->currentServiceLevel->digitalResourceClusterId, $path, $metadata);

        $archive->storagePath = $storagePath;
    }

    /**
     * Store archive resources
     * @param recordsManagement/archive $archive The archive to deposit
     */
    protected function storeResources($archive)
    {
        $nbResources = count($archive->digitalResources);

        for ($i = 0; $i < $nbResources; $i++) {
            $digitalResource = $archive->digitalResources[$i];
            $digitalResource->archiveId = $archive->archiveId;

            $this->digitalResourceController->store($digitalResource);
        }
    }

    /**
     * Store archive resources
     *
     * @param recordsManagement/archive $archive The archive to deposit
     */
    protected function storeDescriptiveMetadata($archive)
    {
        $descriptionController = $this->useDescriptionController($archive->descriptionClass);

        $descriptionController->create($archive);
    }

    /**
     * Resolve the storage path
     *
     * @param array  $values  Array of value to resolve the path
     * @param string $pattern The pattern to resolve
     *
     * @return string The storage path
     */
    public function resolveStoragePath($values, $pattern = null)
    {
        if (!$pattern) {
            $pattern = $this->storePath;
        }

        $values = is_array($values) ? $values : get_object_vars($values);

        if (preg_match_all("/\<[^\>]+\>/", $pattern, $variables)) {
            foreach ($variables[0] as $variable) {
                $token = substr($variable, 1, -1);
                switch (true) {
                    case $token == 'app':
                        $pattern = str_replace($variable, \laabs::getApp(), $pattern);
                        break;

                    case $token == 'instance':
                        if ($instanceName = \laabs::getInstanceName()) {
                            $pattern = str_replace($variable, $instanceName, $pattern);
                        } else {
                            $pattern = "instance";
                        }
                        break;

                    case isset($values[$token]):
                        $pattern = str_replace($variable, (string) $values[$token], $pattern);
                        break;
                }
            }
        }

        return $pattern;
    }
}
