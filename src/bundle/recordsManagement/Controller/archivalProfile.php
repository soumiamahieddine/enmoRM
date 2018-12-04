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
use core\Exception;

/**
 * Class of adminArchivalProfile
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class archivalProfile
{
    protected $sdoFactory;

    protected $lifeCycleJournalController;

    protected $descriptionFields;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->lifeCycleJournalController = \laabs::newController('lifeCycle/journal');

        foreach (\Laabs::newController('recordsManagement/descriptionField')->index() as $descriptionField) {
            if (!empty($descriptionField->enumeration)) {
                $descriptionField->enumeration = json_decode($descriptionField->enumeration);
            }
            $this->descriptionFields[$descriptionField->name] = $descriptionField;
        }
    }

    /**
     * List archival profiles
     *
     * @return recordsManagement/archivalProfile[] The list of archival profiles
     */
    public function index()
    {
        return $this->sdoFactory->find('recordsManagement/archivalProfile');
    }

    /**
     * New empty archival profile with default values
     *
     * @return recordsManagement/archivalProfile The archival profile object
     */
    public function newProfile()
    {
        $archivalProfile = \laabs::newInstance("recordsManagement/archivalProfile");

        return $archivalProfile;
    }

    /**
     * Edit an archival profile
     * @param string $archivalProfileId   The archival profile's identifier
     * @param bool   $withRelatedProfiles Bring back the contents profiles
     * @param bool   $recursively         Get contained archival profiles children
     *
     * @return recordsManagement/archivalProfile The profile object
     */
    public function read($archivalProfileId, $withRelatedProfiles=true, $recursively=false)
    {
        $archivalProfile = $this->sdoFactory->read('recordsManagement/archivalProfile', $archivalProfileId);

        $this->readDetail($archivalProfile);

        if ($withRelatedProfiles) {
            $archivalProfile->containedProfiles = $this->getContentsProfiles($archivalProfileId, $recursively);
        }

        return $archivalProfile;
    }

    /**
     * get an archival profile by reference
     * @param string $reference The archival profile reference
     * @param bool   $withRelatedProfiles      Bring back the contents profiles
     *
     * @return recordsManagement/archivalProfile The profile object
     */
    public function getByReference($reference, $withRelatedProfiles=true)
    {

        try {
            $archivalProfile = $this->sdoFactory->read('recordsManagement/archivalProfile', array('reference' => $archivalProfileReference));

            $this->readDetail($archivalProfile);

            if ($withRelatedProfiles) {
                $archivalProfile->containedProfiles = $this->getContentsProfiles($archivalProfile->archivalProfileId);
            }
        } catch (\Exception $exception) {
            throw new \core\Exception\BadRequestException("Profile %s not found", 200, null, $archivalProfileReference);
        }

        return $archivalProfile;
    }

    /**
     * get array of archival profile by description class
     * @param string $archivalProfileDescriptionClass The archival profile reference
     *
     * @return array $archivalProfiles Array of recordsManagement/archivalProfile object
     */
    public function getByDescriptionClass($archivalProfileDescriptionClass)
    {
        $archivalProfiles = $this->sdoFactory->find('recordsManagement/archivalProfile', "descriptionClass='$archivalProfileDescriptionClass'");

        foreach ($archivalProfiles as $archivalProfile) {
            $this->readDetail($archivalProfile);
        }

        return $archivalProfiles;
    }

    /**
     * Read the agragates
     * @param recordsManagement/archivalProfile $archivalProfile
     */
    public function readDetail($archivalProfile)
    {
        // Read retention rule
        if ($archivalProfile->retentionRuleCode) {
            $archivalProfile->retentionRule = $this->sdoFactory->read('recordsManagement/retentionRule', $archivalProfile->retentionRuleCode);
        }

        // Read access rule
        if (!empty($archivalProfile->accessRuleCode)) {
            $archivalProfile->accessRule = $this->sdoFactory->read('recordsManagement/accessRule', $archivalProfile->accessRuleCode);
        }

        // Read profile description
        $archivalProfile->archiveDescription = $this->sdoFactory->readChildren('recordsManagement/archiveDescription', $archivalProfile, null, 'position');
        if ($archivalProfile->descriptionClass == '') {
            foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                $archiveDescription->descriptionField = $this->descriptionFields[$archiveDescription->fieldName];
            }
        } else {
            $reflectionClass = \laabs::getClass($archivalProfile->descriptionClass);

            foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                if ($reflectionClass->hasProperty($archiveDescription->fieldName)) {
                    $reflectionProperty = $reflectionClass->getProperty($archiveDescription->fieldName);

                    $descriptionField = \laabs::newInstance('recordsManagement/descriptionField');
                    $descriptionField->name = $reflectionProperty->name;
                    if (isset($reflectionProperty->tags['label'])) {
                        $descriptionField->label = $reflectionProperty->tags['label'][0];
                    } else {
                        $descriptionField->label = $reflectionProperty->name;
                    }

                    $descriptionField->type = $reflectionProperty->getType();

                    $descriptionField->enumeration = $reflectionProperty->getEnumeration();
                    if ($descriptionField->enumeration) {
                        $descriptionField->type = 'name';
                    }

                    $archiveDescription->descriptionField = $descriptionField;
                }
            }
        }
    }

    /**
     * Get the contents profiles list
     * @param string $archivalProfileId The parent profile identifier
     * @param bool   $recursively       Get contained archival profiles children
     *
     * @return array The list of contents archival profile
     */
    public function getContentsProfiles($archivalProfileId, $recursively = false)
    {
        $containedProfiles = [];
        $contents = $this->sdoFactory->find('recordsManagement/archivalProfileContents', "parentProfileId ='$archivalProfileId'");

        if (count($contents)) {
            foreach ($contents as $content) {
                $containedProfile = $this->sdoFactory->read('recordsManagement/archivalProfile', $content->containedProfileId);

                $this->readDetail($containedProfile);

                if ($recursively) {
                    $containedProfile->containedProfiles = $this->getContentsProfiles($content->containedProfileId, $recursively);
                }

                $containedProfiles[] = $containedProfile;
            }
        }

        return $containedProfiles;
    }

    /**
     * Get the standard archive field
     * @return array The standard archive field
     */
    public function getArchiveDescriptionFields()
    {
        $descriptionFields = [];
        $nameTypes = [
                'archiveId' => 'name',
                'originatorArchiveId' => 'name',
                'originatorOrgRegNumber' => 'name',
                
                'archiveName' => 'text',

                'depositDate' => 'date',
            ];
        // Read document profiles
        foreach ($nameTypes as $name => $type) {
            $descriptionField = \Laabs::newInstance('recordsManagement/descriptionField');
            $descriptionField->name = $descriptionField->label = $name;
            $descriptionField->type = $type;

            $descriptionFields[$name] = $descriptionField;
        }

        return $descriptionFields;
    }

    /**
     * Get the standard document fields
     * @return array The standard document fields
     */
    public function getDocumentDescriptionFields()
    {
        $descriptionFields = [];
        $nameTypes = [
                'description' => 'text',
                'language' => 'text',
                'purpose' => 'text',
                'title' => 'text',
                'creator' => 'text',
                'publisher' => 'text',
                'contributor' => 'text',
                'spatialCoverage' => 'text',
                'temporalCoverage' => 'text',

                'docId' => 'name',
                'originatorDocId' => 'name',
                'category' => 'name',

                'creation' => 'date',
                'issue' => 'date',
                'receipt' => 'date',
                'response' => 'date',
                'submission' => 'date',
                'available' => 'date',
                'valid' => 'date',
            ];
        // Read document profiles
        foreach ($nameTypes as $name => $type) {
            $descriptionField = \Laabs::newInstance('recordsManagement/descriptionField');
            $descriptionField->name = $descriptionField->label = $name;
            $descriptionField->type = $type;

            $descriptionFields[$name] = $descriptionField;
        }

        return $descriptionFields;
    }

    /**
     * create an archival profile
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     *
     * @return boolean The result of the request
     */
    public function create($archivalProfile)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $archivalProfile->archivalProfileId = \laabs::newId();

            $this->sdoFactory->create($archivalProfile, 'recordsManagement/archivalProfile');

            $this->createDetail($archivalProfile);
            
            // Life cycle journal
            $eventItems = array('archivalProfileReference' => $archivalProfile->reference);
            $this->lifeCycleJournalController->logEvent('recordsManagement/profileCreation', 'recordsManagement/archivalProfile', $archivalProfile->archivalProfileId, $eventItems);
        
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw new \core\Exception("Profile already exist");
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return  $archivalProfile->archivalProfileId;
    }

    /**
     * update an archival profile
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     *
     * @return boolean The request of the request
     */
    public function update($archivalProfile)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            //$archivalProfile = \laabs::cast($archivalProfile, 'recordsManagement/archivalProfile');

            $this->deleteDetail($archivalProfile);
            $this->createDetail($archivalProfile);

            // archival profile
            $this->sdoFactory->update($archivalProfile, "recordsManagement/archivalProfile");

            // Life cycle journal
            $eventItems = array('archivalProfileReference' => $archivalProfile->reference);
            $this->lifeCycleJournalController->logEvent('recordsManagement/archivalProfileModification', 'recordsManagement/archivalProfile', $archivalProfile->archivalProfileId, $eventItems);
        
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }
            throw new \core\Exception("Profile already exist");

        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }
 
    /**
     * delete an archival profile
     * @param string $archivalProfileId The identifier of the archival profile
     *
     * @return boolean The request of the request
     */
    public function delete($archivalProfileId)
    {
        $archivalProfile = $this->sdoFactory->read('recordsManagement/archivalProfile', $archivalProfileId);

        if ($this->isUsed($archivalProfile)) {
            throw new \core\Exception\ForbiddenException("The archival profile %s currently in use.", 403, null, [$archivalProfile->reference]);
        }

        $this->deleteDetail($archivalProfile);

        $archivalProfileContents = $this->sdoFactory->find('recordsManagement/archivalProfileContents', "parentProfileId='$archivalProfileId' OR containedProfileId='$archivalProfileId'");
        if($archivalProfileContents) {
            $this->sdoFactory->deleteCollection($archivalProfileContents, 'recordsManagement/archivalProfileContents');
        }
        
        $organizationController = \laabs::newController('organization/organization');
        $organizationController->deleteArchivalProfileAccess($archivalProfile->reference);

        $this->sdoFactory->delete($archivalProfile);

        // Life cycle journal
        $eventItems = array('archivalProfileReference' => $archivalProfile->reference);
        $this->lifeCycleJournalController->logEvent('recordsManagement/profileDestruction', 'recordsManagement/archivalProfile', $archivalProfile->archivalProfileId, $eventItems);

        return true;
    }

    /**
     * Get form of teh description class
     * @param string $archivalProfileReference The reference of the archival profile
     *
     * @return recordsManagement/descriptionClass Object The description class object parsed with the profile descriptions
     */
    public function descriptionForm($archivalProfileReference)
    {
        $archivalProfile = $this->getByReference($archivalProfileReference);
        $descriptionObject = \laabs::newController($archivalProfile->descriptionClass)->form($archivalProfile->archiveDescription);
        $descriptionObject->descriptionClass =  $archivalProfile->descriptionClass;

        return $descriptionObject;
    }

    /**
     * Check if archival
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     *
     * @return bool The result of the operation
     */
    public function isUsed($archivalProfile)
    {
        return (bool) $this->sdoFactory->count('recordsManagement/archive', "archivalProfileReference = '$archivalProfile->reference'");
    }

    /**
     * Create a detail
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     *
     */
    protected function createDetail($archivalProfile)
    {
        if (!empty($archivalProfile->archiveDescription)) {
            $position = 0;
            foreach ($archivalProfile->archiveDescription as $description) {
                $description->archivalProfileId = $archivalProfile->archivalProfileId;
                $description->position = $position;
                $position++;

                $this->sdoFactory->create($description, 'recordsManagement/archiveDescription');
            }
        }

        // Contents profiles

        
        if (!empty($archivalProfile->containedProfiles)) {

            foreach ($archivalProfile->containedProfiles as $containedProfileId) {
                try {
                    $profile = $this->sdoFactory->read("recordsManagement/archivalProfile", $containedProfileId);

                } catch (\Exception $e) {
                    throw new \core\Exception\NotFoundException("%s can't be found.", 404, null, [$containedProfileId]);
                }
                
                $archivalProfileContents = \laabs::newInstance('recordsManagement/archivalProfileContents');
                $archivalProfileContents->parentProfileId = $archivalProfile->archivalProfileId; 
                $archivalProfileContents->containedProfileId = $containedProfileId;

                $this->sdoFactory->create($archivalProfileContents, 'recordsManagement/archivalProfileContents');
            }
        }

    }

    /**
     * Delete a detail
     * @param recordsManagement/archivalProfile $archivalProfile The archival profile object
     *
     */
    protected function deleteDetail($archivalProfile)
    {
        $this->sdoFactory->deleteChildren('recordsManagement/archiveDescription', $archivalProfile, 'recordsManagement/archivalProfile');

        $this->sdoFactory->deleteChildren('recordsManagement/archivalProfileContents', $archivalProfile, 'recordsManagement/archivalProfile');
    }



}
