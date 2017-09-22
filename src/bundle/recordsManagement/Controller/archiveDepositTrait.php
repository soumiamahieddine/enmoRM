<?php
/*
 * Copyright (C) G015 Maarch
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
 * Trait for archive deposit
 */
trait archiveDepositTrait
{
    protected $originatorOrgs;

    /**
     * Instanciate a new archive from a file
     *
     * @return recordsManagement/archive
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
            $archive->retentionRuleCode = $this->currentArchivalProfile->retentionRuleCode;
        }

        // Use current service level
        if (isset($this->currentServiceLevel)) {
            $archive->serviceLevelReference = $this->currentServiceLevel->reference;
        }

        return $archive;
    }

    /**
     * Receive a new archive and store the pending archive if an import directory is given
     * @param recordsManagement/archive $archive The archive
     *
     * @return recordsManagement/archive
     */
    public function receive($archive)
    {
        $currentOrg = \laabs::getToken("ORGANIZATION");
        if (!$currentOrg) {
            throw \laabs::newException('recordsManagement/noOrgUnitException', "Permission denied: You have to choose a working organization unit to proceed this action.");
        }

        $archive = \laabs::cast($archive, 'recordsManagement/archive');

        $retentionRule = $this->retentionRuleController->read($archive->retentionRuleCode);
        $archive->retentionDuration =  $retentionRule->duration;
        $archive->finalDisposition =  $retentionRule->finalDisposition;

        if (empty($archive->originatorOrgRegNumber)) {
            $archive->originatorOrgRegNumber = \laabs::getToken("ORGANIZATION")->registrationNumber;
        }

        $this->useReferences($archive, 'deposit');

        if (!isset($archive->archiveId)) {
            $archive->archiveId = \laabs::newId();
        }
        $archive->status = "received";

        // Use current profile
        if (isset($this->currentArchivalProfile)) {
            $archive->archivalProfileReference = $this->currentArchivalProfile->reference;
            if (empty($archive->descriptionClass) && !empty($this->currentArchivalProfile->descriptionClass)) {
                $archive->descriptionClass = $this->currentArchivalProfile->descriptionClass;
            }

            if (!empty($this->currentArchivalProfile->retentionRuleCode)) {
                $archive->retentionRuleCode = $this->currentArchivalProfile->retentionRuleCode;
            }

            if (empty($archive->accessRuleCode)) {
                $archive->accessRuleCode = $this->currentArchivalProfile->accessRuleCode;
            }

            if (empty($this->currentArchivalProfile->retentionStartDate)) {
                $archive->retentionStartDate = null;
            }
        }

        // Set archive name when mono document
        if (empty($archive->archiveName) && count($archive->digitalResources) == 1) {
            if (isset($archive->digitalResources[0]->fileName)) {
                $archive->archiveName = $archive->digitalResources[0]->fileName;
            }
        }

        // Use current service level
        if (isset($this->currentServiceLevel)) {
            $archive->serviceLevelReference = $this->currentServiceLevel->reference;
        }

        $this->getManagementRules($archive);

        if (!isset($archive->descriptionClass) && \laabs::hasDependency('fulltext')) {
            $fulltextController = \laabs::newController("recordsManagement/fulltext");

            $fulltextController->checkRequiredFields($archive->archivalProfileReference, $archive->descriptionObject);
            $fulltextController->validateDescriptionFields($archive->descriptionObject);
        }

        if (!empty($archive->descriptionClass) && isset($archive->descriptionObject)) {
            $archive->descriptionObject = \laabs::castObject($archive->descriptionObject, $archive->descriptionClass);

            $this->validateArchiveDescriptionObject($archive);
        }
        // Resources
        if ($archive->digitalResources) {
            //$formatController = \laabs::newController("digitalResource/format");
            $droid = \laabs::newService('dependency/fileSystem/plugins/fid');
            //$tika = \laabs::newService('dependency/fileSystem/plugins/Tika');

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
                if ($format = $droid->match($filename)) {
                    $digitalResource->puid = $format->puid;
                }

                //var_dump($tika->getInfo($filename));

                if (empty($digitalResource->hash) || empty($digitalResource->hashAlgorithm)) {
                    $this->digitalResourceController->getHash($digitalResource, $this->hashAlgorithm);
                }
            }
        }

        $this->deposit($archive);
    }

    /**
     * Get management rules
     * @param object $archive
     *
     * @return disposalRule
     */
    public function getManagementRules($archive)
    {
        $conf = \laabs::configuration('recordsManagement');
        if (isset($conf['archivalProfileType']) && $conf['archivalProfileType'] > 1) {
            $this->getManagementRulesFromProfile($archive);
        }

        if (isset($archive->retentionRuleCode) && !isset($archive->retentionDuration)) {
            try {
                $retentionRule = $this->retentionRuleController->read($archive->retentionRuleCode);
                $archive->retentionDuration = $retentionRule->duration;
            } catch (\Exception $e) {

            }
        }
        if ($archive->retentionStartDate == "depositDate") {
            $archive->retentionStartDate = $archive->depositDate;
        }

        /*if (is_string($archive->retentionStartDate)) {
            $qname = \laabs\explode("/", $archive->retentionStartDate);
            if ($qname[0] == "description") {
                $i = 0;
                while ($archive->descriptionObject[$i]->name != $qname[1]) {
                    $i++;
                }
                $archive->retentionStartDate = \laabs::newDate($archive->descriptionObject[$i]->value);
            } else {
            }
        }*/

        if (isset($archive->retentionStartDate) && isset($archive->retentionDuration)) {
            $archive->disposalDate = $archive->retentionStartDate->shift($archive->retentionDuration);
        }

        if (isset($archive->accessRuleCode) && !isset($archive->accessRuleDuration)) {
            try {
                $accessRule = $this->accessRuleController->edit($archive->accessRuleCode);
                $archive->accessRuleDuration = $accessRule->duration;
            } catch (\Exception $e) {

            }
        }

        if (isset($archive->retentionStartDate) && isset($archive->accessRuleDuration)) {
            $archive->accessRuleComDate = $archive->retentionStartDate->shift($archive->accessRuleDuration);
        }
    }

    protected function getManagementRulesFromProfile($archive)
    {
        // Access & communication rules
        if ($archive->archivalProfileReference) {
            try {
                $archivalProfile = $this->useArchivalProfile($archive->archivalProfileReference);
            } catch (\Exception $e) {

            }
        }

        // Retention rule
        if ($archive->retentionDuration == null
            && $archive->finalDisposition == null
            && !empty($archive->retentionStartDate)
            && isset($archive->retentionRuleCode)
        ) {
                $retentionRule = $this->sdoFactory->read('recordsManagement/retentionRule', $archive->retentionRuleCode);
                $archive->retentionDuration = $retentionRule->duration;
                $archive->finalDisposition = $retentionRule->finalDisposition;
        }

        // Access rule
        if ($archive->accessRuleDuration == null
            && !empty($archive->accessRuleStartDate)
            && isset($archive->accessRuleCode)
        ) {
                $accessRule = $this->sdoFactory->read('recordsManagement/accessRule', $archive->accessRuleCode);
                $archive->accessRuleDuration = $accessRule->duration;
        }

        if ($archive->retentionDuration == null
            && $archive->finalDisposition == null
            && $archive->retentionStartDate == null
            && isset($archivalProfile)
            && $archivalProfile->retentionRule
        ) {
            $archive->retentionRuleCode = $archivalProfile->retentionRule->code;
            $archive->retentionDuration = $archivalProfile->retentionRule->duration;
            $archive->finalDisposition = $archivalProfile->retentionRule->finalDisposition;
            $dateRule = strtok($archivalProfile->retentionStartDate, LAABS_URI_SEPARATOR);
            switch ($dateRule) {
                case 'definedLater':
                    $archive->retentionStartDate = null;
                    break;

                case 'originatingDate':
                    $archive->retentionStartDate = $archive->originatingDate;
                    break;

                case 'depositDate':
                    $archive->retentionStartDate = \laabs::newDate();
                    break;

                case 'description':
                    $path = strtok(LAABS_URI_SEPARATOR);
                    if (isset($archive->descriptionObject)) {
                        if (is_object($archive->descriptionObject) && isset($archive->descriptionObject->{$path})) {
                            $archive->retentionStartDate = $archive->descriptionObject->{$path};
                        } elseif (is_array($archive->descriptionObject)) {
                            foreach ($archive->descriptionObject as $field) {
                                if ($field->name == $path) {
                                    $archive->retentionStartDate = \laabs::newDate($field->value);
                                }
                            }
                        }
                    }
                    break;
            }
        }
    }

    private function validateArchiveDescriptionObject($archive)
    {
        if (!empty($archive->archivalProfileReference)) {
            if (!empty($archive->descriptionClass) && !empty($archive->descriptionObject)) {
                $this->validateDescriptionObject($archive->descriptionObject, $this->currentArchivalProfile);
            }
        }

        return $archive;
    }

    /**
     * Check if an object correspond to an archival profile
     * @param mixed                             $object          The metadata object to check
     * @param recordsManagement/archivalProfile $archivalProfile The reference of the profile
     *
     * @return boolean The result of the validation
     */
    public function validateDescriptionObject($object, $archivalProfile)
    {
        if (\laabs::getClass($object)->getName() != $archivalProfile->descriptionClass) {
            return false;
        }

        foreach ($archivalProfile->archiveDescription as $description) {
            $fieldName = explode(LAABS_URI_SEPARATOR, $description->fieldName);
            $propertiesList = array($object);

            foreach ($fieldName as $name) {
                $newPropertiesList = array();
                foreach ($propertiesList as $propertyValue) {
                    if (isset($propertyValue->{$name})) {
                        if (is_array($propertyValue->{$name})) {
                            foreach ($propertyValue->{$name} as $value) {
                                $newPropertiesList[] = $value;
                            }
                        } else {
                            $newPropertiesList[] = $propertyValue->{$name};
                        }
                    } else {
                        $newPropertiesList[] = null;
                    }
                }
                $propertiesList = $newPropertiesList;
            }

            foreach ($propertiesList as $propertyValue) {
                if ($description->required && $propertyValue == null) {
                    throw new \bundle\recordsManagement\Exception\archiveDoesNotMatchProfileException('The description class does not match with the archival profile.');
                }
            }
        }

        return true;
    }

    /**
     * Deposit a new archive
     * @param recordsManagement/archive $archive          The archive to deposit
     * @param string                    $filePlanPosition The file plan position
     *
     * @return boolean The result of the operation
     */
    public function deposit($archive, $filePlanPosition = null)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }
        
        try {
            if (!isset($this->originatorOrgs[$archive->originatorOrgRegNumber])) {
                $originatorOrg = $this->organizationController->getOrgByRegNumber($archive->originatorOrgRegNumber);
                $this->originatorOrgs[$archive->originatorOrgRegNumber] = $originatorOrg;
            } else {
                $originatorOrg = $this->originatorOrgs[$archive->originatorOrgRegNumber];
            }

            $archive->originatorOwnerOrgId = $originatorOrg->ownerOrgId;
            $archive->status = 'preserved';
            $archive->depositDate = \laabs::newTimestamp();

            $this->getManagementRules($archive);

            // Record descriptive metadata in database table/model
            if (!empty($archive->descriptionClass)) {
                if (isset($archive->descriptionObject)) {
                    $descriptionController = $this->useDescriptionController($archive->descriptionClass);
                    $descriptionController->create($archive);
                }
            } elseif (\laabs::hasDependency('fulltext')) {
                $fulltextController = \laabs::newController("recordsManagement/fulltext");

                // Record descriptive metadata in fulltext index
                $index = 'archives';
                if (isset($archive->archivalProfileReference)) {
                    $index = $archive->archivalProfileReference;
                }

                $baseIndex = $fulltextController->getArchiveIndex($archive);

                if (isset($archive->descriptionObject)) {
                    $archiveIndex = clone($baseIndex);
                    $fulltextController->mergeIndex($archive->descriptionObject, $archiveIndex);
                    $fulltextController->addDocument($index, $archiveIndex);
                }
            }

            // Store resources
            if (!$this->currentServiceLevel) {
                if (isset($archive->serviceLevelReference)) {
                    $this->useServiceLevel('deposit', $archive->serviceLevelReference);
                } else {
                    $this->useServiceLevel('deposit');
                }
            }

            if (empty($filePlanPosition)) {
                if (!$this->storePath) {
                    $filePlanPosition = $archive->originatorOwnerOrgId."/".$archive->originatorOrgRegNumber."/<10000>/".$archive->archiveId;
                } else {
                    $filePlanPosition = $this->resolveStoragePath($archive);
                }
            }

            $archive->storagePath = $filePlanPosition;

            $this->sdoFactory->create($archive, 'recordsManagement/archive');

            if (isset($archive->digitalResources) && count($archive->digitalResources) > 0) {
                foreach ($archive->digitalResources as $digitalResource) {
                    $digitalResource->archiveId = $archive->archiveId;

                    $this->digitalResourceController->store($digitalResource, $this->currentServiceLevel->digitalResourceClusterId, $filePlanPosition);
                }
            }

            if (isset($archive->contents) && count($archive->contents)) {
                foreach ($archive->contents as $content) {
                    $content->parentArchiveId = $archive->archiveId;
                    $this->deposit($content, $filePlanPosition."/".(string) $content->archiveId);
                }
            }

        } catch (\Exception $exception) {
            try {
                if (\laabs::hasDependency('fulltext') && isset($this->fulltextController)) {
                    $this->fulltextController->delete($index, $baseIndex);
                }

                if ($archive->digitalResources) {
                    foreach ($archive->digitalResources as $digitalResource) {
                        $this->digitalResourceController->rollbackStorage($digitalResource);
                    }
                }
            } catch (\Exception $exception) {

            }

            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        // Life cycle journal
        // get certificate of deposit
        $eventInfo = array(
            'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            'depositorOrgRegNumber' => $archive->depositorOrgRegNumber,
            'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
        );

        $logged = false;
        if (isset($archive->digitalResources) && count($archive->digitalResources)) {
            foreach ($archive->digitalResources as $digitalResource) {
                $eventType = 'recordsManagement/deposit';
                $address = $digitalResource->address[0];

                $eventInfo['resId'] = (string) $digitalResource->resId;
                $eventInfo['hashAlgorithm'] = $digitalResource->hashAlgorithm;
                $eventInfo['hash'] = $digitalResource->hash;
                $eventInfo['address'] = $address->path;

                if (isset($relationship->resource->puid)) {
                    $eventInfo['format'] = $digitalResource->puid;
                } else {
                    $eventInfo['format'] = $digitalResource->mimetype;
                }

                if (isset($digitalResource->relatedResId)) {
                    $eventType = 'recordsManagement/depositOfLinkedResource';
                    
                    $eventInfo['linkedResId'] = $digitalResource->relatedResId;
                    $eventInfo['relationshipType'] = $digitalResource->relationshipType;
                }

                $event = $this->lifeCycleJournalController->logEvent($eventType, 'recordsManagement/archive', $archive->archiveId, $eventInfo);
                $archive->lifeCycleEvent[] = $event;

                $logged = true;
            }
        }

        if (!$logged) {
            $eventInfo['resId'] = $eventInfo['hashAlgorithm'] = $eventInfo['hash'] = null;
            $eventInfo['address'] = $filePlanPosition;
            $event = $this->lifeCycleJournalController->logEvent('recordsManagement/deposit', 'recordsManagement/archive', $archive->archiveId, $eventInfo);
            $archive->lifeCycleEvent[] = $event;
        }

        try {

            $this->checkForConvertion($archive);

        } catch (\Exception $e) {
            if ($this->conversionError) {
                throw $e;
            }
        }

        return $archive->archiveId;
    }

    /**
     * Resolve the storage path
     * @param array $values Array of value to resolve the path
     *
     * @return string The storage path
     */
    public function resolveStoragePath($values)
    {
        $filePlanPosition = $this->storePath;
        $values = is_array($values) ? $values : get_object_vars($values);

        $matches = array();
        preg_match_all("/\<(.*?)\>/", $this->storePath, $matches);

        foreach ($matches[1] as $key => $match) {
            if ((intval($match) != 0 && is_int(intval($match))) || $match == "Y" || $match == "m" || $match == "d") {
                continue;
            }

            if (!isset($values[$match])) {
                $values[$match] = $match;
            }

            $filePlanPosition = str_replace($matches[0][$key], (string) $values[$match], $filePlanPosition);
        }

        return $filePlanPosition;
    }

    /**
     * Check information to convert if possible
     * @param recordsManagement/archive $archive The deposit archive
     *
     * @return boolean Return true resource was be converted
     */
    private function checkForConvertion($archive)
    {
        if (strrpos($this->currentServiceLevel->control, "convertOnDeposit") == false) {
            return false;
        }

        $conversionRules = \laabs::newController("digitalResource/conversionRule")->index();

        if ((!is_array($conversionRules)) || count($conversionRules) == 0) {
            return false;
        }

        if (count($archive->digitalResources) > 0) {
            foreach ($archive->digitalResources as $digitalResource) {
                $converted = $this->convertResource($archive, $digitalResource);
            }
        }

        if (count($archive->contents) > 0) {
            foreach ($archive->contents as $archiveObject) {
                $this->checkForConvertion($archiveObject, $filePlanPosition);
            }
        }

        return true;
    }
}
