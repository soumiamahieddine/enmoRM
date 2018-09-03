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

namespace presentation\maarchRM\Presenter\recordsManagement;

/**
 * archive html serializer
 *
 * @package RecordsManagement
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class archive
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    protected $json;
    protected $translator;
    protected $archivalProfiles = [];
    protected $archivalProfilesByReference = [];
    protected $organizations = [];
    protected $getOrgByRegNumber = [];

    /**
     * Constuctor
     * @param \dependency\html\Document                    $view
     * @param \dependency\json\JsonObject                  $json
     * @param \dependency\localisation\TranslatorInterface $translator
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    )
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('recordsManagement/messages');
    }

    /**
     * get a form to search resource
     * @param array $profiles Array of profile
     *
     * @return string
     */
    public function searchForm($profiles)
    {
        $currentService = \laabs::getToken("ORGANIZATION");

        $ownerOriginatorOrgs = [];

        if (!$currentService) {
            $emptyRole = true;
        } else {
            $emptyRole = false;
            $ownerOriginatorOrgs = $this->getOwnerOriginatorsOrgs($currentService);
        }

        $retentionRules = \laabs::callService('recordsManagement/retentionRule/readIndex');

        $this->view->addContentFile("recordsManagement/archive/search.html");

        $this->view->translate();
        
        usort($profiles, array($this, "compareProfiles"));

        $deleteDescription = true;
        if (isset(\laabs::configuration("recordsManagement")['deleteDescription'])) {
            $deleteDescription = (bool) \laabs::configuration("recordsManagement")['deleteDescription'];
        }

        $this->view->setSource("retentionRules", $retentionRules);
        $this->view->setSource("emptyRole", $emptyRole);
        $this->view->setSource("profiles", $profiles);
        $this->view->setSource("organizationsOriginator", $ownerOriginatorOrgs);
        $this->view->setSource("deleteDescription", $deleteDescription);

        $this->view->merge();

        return $this->view->saveHtml();
    }

    private function compareProfiles($a, $b)
    {
        return \laabs::alphabeticalSort($a, $b, "name");
    }

    /**
     * get archives with information
     * @param array $archives Array of archive object
     *
     * @return string
     */
    public function search($archives)
    {
        $this->view->addContentFile("recordsManagement/archive/resultList.html");

        $this->view->translate();

        //access code selector
        $accessRules = \laabs::callService('recordsManagement/accessRule/readIndex');
        foreach ($accessRules as $accessRule) {
            $accessRule->json = json_encode($accessRule);
            if ($accessRule->duration != null) {
                $accessRule->accessRuleDurationUnit = substr($accessRule->duration, -1);
                $accessRule->accessRuleDuration = substr($accessRule->duration, 1, -1);
            }
        }

         //retention code selector
        $retentionRules = \laabs::callService('recordsManagement/retentionRule/readIndex');
        foreach ($retentionRules as $retentionRule) {
            $retentionRule->json = json_encode($retentionRule);
            if ($retentionRule->duration != null) {
                $retentionRule->retentionRuleDurationUnit = substr($retentionRule->duration, -1);
                $retentionRule->retentionRuleDuration = substr($retentionRule->duration, 1, -1);
            }
        }

        $archiveController = \laabs::newController('recordsManagement/archive');
        $orgsByRegNumber = \laabs::callService('organization/organization/readOrgList');

        $currentDate = \laabs::newDate();
        foreach ($archives as $archive) {
            $archive->finalDispositionDesc = $this->view->translator->getText($archive->finalDisposition, false, "recordsManagement/messages");
            $archive->statusDesc = $this->view->translator->getText($archive->status, false, "recordsManagement/messages");

            if (!empty($archive->disposalDate) && $archive->disposalDate <= $currentDate) {
                $archive->disposable = true;
            }

            if (empty($archive->disposalDate) && (empty($archive->retentionRuleCode) || empty($archive->retentionDuration))) {
                $archive->noRetention = true;
            }

            if (isset($orgsByRegNumber[$archive->originatorOrgRegNumber])) {
                $archive->originatorOrgName = $orgsByRegNumber[$archive->originatorOrgRegNumber]->displayName;

                try {
                    $archive->hasRights = $archiveController->checkRights($archive);
                } catch(\Exception $e) {
                    $archive->hasRights = false;
                }
            }
        }
/*
        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        $dataTable->setUnsortableColumns(7);
        $dataTable->setUnsearchableColumns(7);

        $dataTable->setUnsortableColumns(0);
        $dataTable->setUnsearchableColumns(0);
        $dataTable->setSorting(array(array(1, 'desc')));
*/
        $this->readPrivilegesOnArchives();

        $this->view->setSource("accessRules", $accessRules);
        $this->view->setSource("retentionRules", $retentionRules);
        $this->view->setSource('archive', $archives);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Get resource contents
     * @param digitalResource/digitalResource $digitalResource The resource object
     *
     * @return string
     */
    public function getContents($digitalResource = null)
    {
        if (!$digitalResource) {
            // @TODO : throw exception
            $contents = "<h4>This archive does not have any document.</h4>";
            \laabs::setResponseType('text/html');

            return $contents;
        }

        $contents = base64_decode($digitalResource->attachment->data);
        $mimetype = $digitalResource->mimetype;

        \laabs::setResponseType($mimetype);
        $response = \laabs::kernel()->response;
        $response->setHeader("Content-Disposition", "inline; filename=".$digitalResource->attachment->filename."");

        return $contents;
    }

    /**
     * Get archive description
     * @param archive $archive
     *
     * @return string
     */
    public function getArchiveInfo($archive)
    {
        $this->view->addContentFile('dashboard/mainScreen/archiveInformation.html');

        $archiveTree = \laabs::newController("recordsManagement/archive")->listChildrenArchive($archive);

        // Relationships
        $this->setArchiveTree($archive);

        // Managment metadata
        $this->setManagementMetadatas($archive);

        // Descriptive metadata
        $this->getDescriptiveMetadatas($archive);

        $this->view->setSource("archive", $archive);

        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Get archive with children
     * @param archive $archive
     *
     * @return string
     */
    public function getArchiveWithChildren($archive, $archiveRelation, $archiveTree)
    {
        $archive->lifeCycleEvent = $archiveRelation->lifeCycleEvent;
        $archive->relationships = $archiveRelation->relationships;
        $archive->digitalResources = $archiveTree->digitalResources;
        $archive->childrenArchives = $archiveTree->childrenArchives;

        $this->view->addContentFile("recordsManagement/archive/description.html");

        // Relationships
        $this->setArchiveTree($archive);

        $this->setArchive($archive);

        $descriptionFragment = $this->view->createDocumentFragment();
        $descriptionFragment->appendHtmlFile("recordsManagement/archive/archiveInfo/archiveInfo.html");

        $description = $this->view->getElementById("archiveInformationDiv");
        $description->appendChild($descriptionFragment);

        //$this->view->setSource("visible", $visible);

        $this->view->setSource("archive", $archive);

        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    public function getArchive($archive, $archiveRelation)
    {
        $this->view->addContentFile("recordsManagement/archive/archiveInfo/archiveInfo.html");

        $archive->lifeCycleEvent = $archiveRelation->lifeCycleEvent;
        $archive->relationships = $archiveRelation->relationships;

        $syncImportPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/deposit");
        $asyncImportPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/transferImport");
        $archive->depositPrivilege = $syncImportPrivilege || $asyncImportPrivilege;

        $this->setArchive($archive);

        return $this->view->saveHtml();
    }

    private function setArchive($archive)
    {
        $syncImportPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/deposit");
        $asyncImportPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/transferImport");
        $archive->depositPrivilege = $syncImportPrivilege || $asyncImportPrivilege;
        // Archival profile
        if ($archive->depositPrivilege) {
            $this->getChildrenArchivesProfiles($archive);
            $archive->depositPrivilege = $archive->depositPrivilege && (count($archive->archivalProfileList) || $archive->acceptArchiveWithoutProfile);
        }

        // Managment metadata
        $this->setManagementMetadatas($archive);

        // Descriptive metadata
        $this->getDescriptiveMetadatas($archive);

        // Life Cycle event
        $this->setArchiveLifeCycleEvents($archive);

        // Relationships
        $this->setArchiveRelationships($archive);

        // Message
        $this->checkMessage($archive);

        $this->view->setSource("archive", $archive);

        $this->view->translate();
        $this->view->merge();

        if ($this->view->getElementById("lifeCycleTable")) {
            $dataTable = $this->view->getElementById("lifeCycleTable")->childNodes->item(1)->plugin['dataTable'];

            $dataTable->setUnsortableColumns(2);
            $dataTable->setUnsearchableColumns(2);

            $dataTable->setSorting(array(array(0, 'desc')));
        }
    }

    protected function setManagementMetadatas($archive)
    {
        $originatorOrg = \laabs::callService('organization/organization/readByregnumber', $archive->originatorOrgRegNumber);
        $archive->originatorOrgName = $originatorOrg->displayName;

        $archive->depositDate = $archive->depositDate->format('Y-m-d H:i:s');

        if (isset($archive->retentionDuration)) {
            $archive->retentionDurationUnit = substr($archive->retentionDuration, -1);
            $archive->retentionDuration = substr($archive->retentionDuration, 1, -1);
        }

        if (isset($archive->accessRuleDuration)) {
            $archive->accessRuleDurationUnit = substr($archive->accessRuleDuration, -1);
            $archive->accessRuleDuration = substr($archive->accessRuleDuration, 1, -1);
        }

        if (!empty($archive->archivalProfileReference)) {
            $archivalProfile = $this->loadArchivalProfile($archive->archivalProfileReference);
            
            $archive->archivalProfileName = $archivalProfile->name;
        }

        $archive->visible = \laabs::newController("recordsManagement/archive")->accessVerification($archive->archiveId);
        $archive->statusDesc = $this->view->translator->getText($archive->status, false, "recordsManagement/messages");
    }

    /**
     * Get archive description
     * @param archive $archive
     *
     * @return string
     */
    protected function getDescriptiveMetadatas($archive)
    {
        $archivalProfile = $this->loadArchivalProfile($archive->archivalProfileReference);
        if ($archive->originatingDate) {
            $archive->originatingDate = $archive->originatingDate->format('d/m/Y');
        }
        
        $modificationPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveManagement/modifyDescription");
        
        if (!isset($archive->descriptionObject)) {
            return;
        }

        if (!empty($archive->descriptionClass)) {
            $presenter = \laabs::newPresenter($archive->descriptionClass);
            $descriptionHtml = $presenter->read($archive->descriptionObject);
        } else {
            $descriptionHtml = '<table">';

            if (isset($archive->descriptionObject)) {
                $descriptionHtml .= $this->setDescription($archive->descriptionObject, $archivalProfile);
            }

            $descriptionHtml .= '</table>';
        }

        if ($descriptionHtml) {
            $node = $this->view->getElementById("metadata");
            if ($node) {
                $this->view->addContent($descriptionHtml, $node);
            }
        } else {
            unset($archive->descriptionObject);
        }

        $this->view->setSource('modificationPrivilege', $modificationPrivilege);
    }

    protected function setDescription($descriptions, $archivalProfile = null)
    {
        $descriptionHtml = "";
        foreach ($descriptions as $name => $value) {
            if (\gettype($value) !== 'array' && \gettype($value) !== 'object') {
                $label = $type = $archivalProfileField = null;
                if ($archivalProfile) {
                    foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                        if ($archiveDescription->fieldName == $name) {
                            $label = $archiveDescription->descriptionField->label;
                            $archivalProfileField = true;
                            $type = $archiveDescription->descriptionField->type;
                        }
                    }
                }

                if (empty($label)) {
                    $label = $this->view->translator->getText($name, false, "recordsManagement/archive");
                }

                if (empty($type) && $value != "") {
                    $type = 'text';
                    switch (gettype($value)) {
                        case 'boolean':
                            $type = 'boolean';
                            break;

                        case 'integer':
                        case 'double':
                            $type = 'number';
                            break;

                        case 'string':
                            if (preg_match("#\d{4}\-\d{2}\-\d{2}#", $value)) {
                                $type = 'date';
                            }
                            break;
                    }
                }

                if ($archivalProfileField) {
                    $descriptionHtml .= '<tr class="archivalProfileField">';
                } else {
                    $descriptionHtml .= '<tr>';
                }

                $descriptionHtml .= '<th title="' . $label . '" name="' . $name . '" data-type="' . $type . '">' . $label . '</th>';
                if ($type == "date") {
                    $textValue = \laabs::newDate($value);
                    $textValue = $textValue->format("d/m/Y");
                } else {
                    $textValue = $value;

                }
                if ($type == 'boolean') {
                    $textValue = $value ? '<i class="fa fa-check" data-value="1"/>' : '<i class="fa fa-times" data-value="0"/>';
                }
                $descriptionHtml .= '<td title="' . $value . '">' . $textValue . '</td>';
                $descriptionHtml .= '</tr>';
            }
        }

        foreach ($descriptions as $name => $value) {
            if (\gettype($value) === 'array' && \gettype($value) === 'object') {
                $label = $type = $archivalProfileField = null;
                if ($archivalProfile) {
                    foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                        if ($archiveDescription->fieldName == $name) {
                            $label = $archiveDescription->descriptionField->label;
                            $archivalProfileField = true;
                            $type = $archiveDescription->descriptionField->type;
                        }
                    }
                }

                if (empty($label)) {
                    $label = $this->view->translator->getText($name, false, "recordsManagement/archive");
                }

                if (empty($type) && $value != "") {
                    $type = 'text';
                    switch (gettype($value)) {
                        case 'boolean':
                            $type = 'boolean';
                            break;

                        case 'integer':
                        case 'double':
                            $type = 'number';
                            break;

                        case 'string':
                            if (preg_match("#\d{4}\-\d{2}\-\d{2}#", $value)) {
                                $type = 'date';
                            }
                            break;
                    }
                }

                if (!empty($name)) {
                    $id = \laabs::newId();
                    $descriptionHtml .= '
                        <br>
                        <div class="panel panel-info">
                            <div class="panel-heading" role="tab">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" href="#' . $id . '" aria-expanded="true" aria-controls="collapseOne" data-translate-catalog="medona/messages">
                                        ' . $name . '
                                    </a>
                                </h4>
                            </div>
                            <div id="' . $id . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                <div class="panel-body">
                                <dl class="dl dl-horizontal">';
                }

                $descriptionHtml .= $this->setDescription($value);

                if (!empty($name)) {
                    $descriptionHtml .= '</dl></div></div></div>';
                }
            }
        }

        return $descriptionHtml;
    }

    protected function setArchiveTree($archive)
    {
        $childrenByProfiles = [];


        // Digital resources
        $this->setDigitalResources($archive);

        foreach ($archive->childrenArchives as $key => $child) {
            if(!is_null($child->archivalProfileReference)) {
                $archivalProfile = $this->loadArchivalProfile($child->archivalProfileReference);

                if (!isset($childrenByProfiles[$archivalProfile->name])) {
                    $childrenByProfiles[$archivalProfile->name] = [];
                }

                $child->archivalProfileName = $archivalProfile->name;
                $childrenByProfiles[$child->archivalProfileName][] = $child;
            }
            else{
                if(!isset($childrenByProfiles["noProfile"])){
                    $childrenByProfiles["noProfile"] = [];
                }
                $childrenByProfiles["noProfile"][] = $child;
            }
            // Digital resources
            $this->setDigitalResources($archive->childrenArchives[$key]);
            $this->setArchiveTree($archive->childrenArchives[$key]);
        }

        $archive->childrenArchives = $childrenByProfiles;
    }

    protected function setDigitalResources($archive)
    {
        if ($archive->status == "disposed") {
            $archive->digitalResources = null;

        } elseif(isset($archive->digitalResources)) {
            foreach ($archive->digitalResources as $key =>$digitalResource) {
                $archive->digitalResources[$key]->json = json_encode($digitalResource);
                $digitalResource->isConvertible = \laabs::callService("digitalResource/digitalResource/updateIsconvertible", $digitalResource);

                if (!isset($digitalResource->relatedResource)) {
                    $digitalResource->relatedResource = [];
                    continue;
                }

                foreach ($digitalResource->relatedResource as $relatedResource) {
                    $relatedResource->isConvertible = \laabs::callService("digitalResource/digitalResource/updateIsconvertible", $relatedResource);
                    $relatedResource->relationshipType = $this->view->translator->getText($relatedResource->relationshipType, "relationship", "recordsManagement/messages");
                }
            }
        }
    }

    protected function setArchiveLifeCycleEvents($archive)
    {
        foreach ($archive->lifeCycleEvent as $key => $event) {
            $archive->lifeCycleEvent[$key]->timestamp = $event->timestamp->format('Y-m-d H:i:s');
        }
    }

    protected function setArchiveRelationships($archive)
    {
        $childrenRelationships = [];
        $parentRelationships = [];
        $relationshipTypes = [];

        if ($archive->relationships['childrenRelationships']) {
            foreach ($archive->relationships['childrenRelationships'] as $relationship) {
                $childrenRelationships[$relationship->typeCode] = $relationship;
            }
            $archive->childrenRelationships = $childrenRelationships;
            
            $relationshipTypes[$relationship->typeCode]=true;
        }

        if ($archive->relationships['parentRelationships']) {
            foreach ($archive->relationships['parentRelationships'] as $relationship) {
                $parentRelationships[$relationship->typeCode] = $relationship;
            }
            $archive->parentRelationships = $parentRelationships;
         
            $relationshipTypes[$relationship->typeCode]=true;
        }

        $archive->relationshipTypes = array_keys($relationshipTypes);
    }

    protected function loadArchivalProfile($reference)
    {
        if (!isset($this->archivalProfiles[$reference])) {
            try {
                $this->archivalProfiles[$reference] = \laabs::callService('recordsManagement/archivalProfile/readProfiledescription_archivalProfileReference_', $reference);
            } catch(\Exception $e) {
                return null;
            }
        }
        
        return $this->archivalProfiles[$reference];
    }

    protected function checkMessage($archive)
    {
        if(\laabs::hasBundle('medona')) {
            if (isset($archive->messages)) {
                foreach ($archive->messages as $message) {
                    $message->type = $this->view->translator->getText($message->type, false, "recordsManagement/messages");

                    $currentService = \laabs::getToken("ORGANIZATION");

                    $message->isVisible = false;
                    if (isset($currentService->orgRoleCodes) &&  !in_array('owner', $currentService->orgRoleCodes)) {
                        if ($message->senderOrgRegNumber === $currentService->registrationNumber || $message->recipientOrgRegNumber === $currentService->registrationNumber) {
                            $message->isVisible = true;
                        }
                    } else {
                        $message->isVisible = true;
                    }
                }
            }
        }
    }
            
    protected function getChildrenArchivesProfiles($archive)
    {

        $archive->archivalProfileList = [];

        if (!empty($archive->archivalProfileReference)) {
            $archivalProfile = \laabs::callService('recordsManagement/archivalProfile/readByreference_reference_', $archive->archivalProfileReference);
            $archive->archivalProfileName = $archivalProfile->name;
                
            $list = [];

            if (count($archivalProfile->containedProfiles)) {
                 $list = $archivalProfile->containedProfiles;
            }

            if (count($list)) {
                foreach ($list as $profile) {
                    $profileObject = new \stdClass();
                    $profileObject->reference = $profile->reference;
                    $profileObject->name = $profile->name;
                    $profileObject->json = json_encode($profile);

                    $archive->archivalProfileList[] = $profileObject;
                }
            }

            if (!count($archive->archivalProfileList) && !$archivalProfile->acceptArchiveWithoutProfile ) {
                $archive->depositPrivilege = false;
            }

            $archive->acceptArchiveWithoutProfile = $archivalProfile->acceptArchiveWithoutProfile;
            $archive->fileplanLevel = $archivalProfile->fileplanLevel;
            $archive->acceptUserIndex = $archivalProfile->acceptUserIndex;
        } else {
            $archive->acceptArchiveWithoutProfile = true;
            $archive->fileplanLevel = true;
        }
    }

    private function useOrganizations()
    {
        if($this->organizations){
            $this->organizations = \laabs::callService('organization/organization/readIndex');
            
            foreach($this->organizations as $organization){
                $this->orgByRegNumber[$organization->registrationNumber] = $organization;
            }
        }
    }

    private function useArchivalProfile()
    {
        if($this->archivalProfiles){
            $this->archivalProfiles = \laabs::callService('recordsManagement/archivalProfile/readIndex');
            
            foreach($this->archivalProfiles as $profile){
                $this->archivalProfilesByReference[$profile->reference] = $profile;
            }
        }
    }

    private function archiveFormatting($archive)
    {
        $this->useOrganizations();
        $this->useArchivalProfile();

        $archive->originatorOrgName = $this->orgByRegNumber[$archive->originatorOrgRegNumber]['displayName'];

        if($archive->originatorOwnerOrgId) {
            $archive->originatorOwnerOrgName = $this->organizations[$archive->originatorOwnerOrgId]['displayName'];
        }

        if($archive->depositorOrgRegNumber) {
            $archive->depositorOrgName = $this->orgByRegNumber[$archive->depositorOrgRegNumber]['displayName'];
        }

        if($archive->archiverOrgRegNumber) {
            $archive->archiverOrgName = $this->orgByRegNumber[$archive->archiverOrgRegNumber]['displayName'];
        }
        
        if ($archive->archivalProfileReference) {
            $archive->archivalProfileName = $this->archivalProfilesByReference[$archive->archiveProfile]['name'];
        }

        $archive->status = $this->view->translator->getText($archive->status, false, "recordsManagement/messages");
        $archive->finalDisposition = $this->view->translator->getText($archive->finalDisposition, false, "recordsManagement/messages");
    }

    /**
     * Serializer html of verifyIntegrity method
     * @param recordsManagement/archive[] $archives
     *
     * @return type
     */
    public function verifyIntegrity($archives)
    {
        $this->view->addContentFile("recordsManagement/archive/modalIntegrity.html");
        $archives['count'] = count($archives['success']) + count($archives['error']);

        $this->view->setSource("archives", $archives);

        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Access denied exception
     * @param Exception $exception The exception
     *
     * @return string
     */
    public function accessDeniedException($exception)
    {
        $this->view->addContentFile("recordsManagement/archive/accessDenied.html");

        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Access denied exception
     * @param Exception $exception The exception
     *
     * @return string
     */
    public function clusterException($exception)
    {
        $this->view->addContentFile("recordsManagement/archive/clusterException.html");

        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * No orgUnit exception
     *
     * @return string
     */
    public function noOrgUnit()
    {
        //$this->view->addHeaders();
        $this->view->addContentFile("recordsManagement/archive/noOrgUnit.html");

        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * resourceUnavailableException
     * @param object $exception
     *
     * @return string
     */
    public function resourceUnavailableException($exception)
    {
        $this->view->addContentFile("recordsManagement/archive/resourceUnavailable.html");

        $this->view->translate();


        return $this->view->saveHtml();
    }

    //JSON

        /**
     * Show an archive tree content
     * @param object $archive
     *
     * @return string
     */
    public function showArchiveTree($archive)
    {
        if (isset($archive->digitalResources)) {
            $this->json->digitalResources = $archive->digitalResources;
        }

        if (isset($archive->childrenArchives)) {
            $this->json->childrenArchives = $archive->childrenArchives;
        }

        return $this->json->save();
    }

    /**
     * Return archive with his retention rule
     * @param recordsManagement/archiveRetentionRule $retentionRule
     *
     * @return string
     */
    public function editArchiveRetentionRule($retentionRule)
    {
        $this->json->retentionRule = $retentionRule;
        $this->json->retentionRule->startDate = (string) $this->json->retentionRule->retentionStartDate;
        unset($this->json->retentionRule->retentionStartDate);

        return $this->json->save();
    }

    /**
     * Return archive with his access rule
     * @param recordsManagement/archiveAccessRule $accessRule
     *
     * @return string
     */
    public function editArchiveAccessRule($accessRule)
    {
        $this->json->accessRule = $accessRule;
        $this->json->accessRule->startDate = (string) $this->json->accessRule->accessRuleStartDate;
        unset($this->json->accessRule->accessRuleStartDate);

        return $this->json->save();
    }

    /**
     * Serializer JSON for modification method
     * @param recordsManagement/archiveRetentionRule $result The new retention rule
     *
     * @return object JSON object with a status and message parameters
     */
    public function modifyRetentionRule($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s archive(s) modified.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s archive(s) can not be modified.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for modification method
     * @param recordsManagement/archiveAccessRule $result The new retention rule
     *
     * @return object JSON object with a status and message parameters
     */
    public function modifyAccessRule($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s archive(s) modified.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s archive(s) can not be modified.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for freeze method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function freeze($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s archive(s) freezed.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($success == 0) {
            $this->json->status = false;
        }

        if ($echec > 0) {
            $message = '%1$s archive(s) can not be freezed.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for unfreeze method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function unfreeze($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s archive(s) unfreezed.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($success == 0) {
            $this->json->status = false;
        }

        if ($echec > 0) {
            $message = '%1$s archive(s) can not be unfreezed.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }
    
    /**
     * Serializer JSON for metadata method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function metadata($result)
    {
        if ($result) {
             $this->json->message = 'Archive updated';
             
        } else {
             $this->json->message = 'Archive not updated';
        }

        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
    
    /**
     * Return new digital resource for an archive
     * @param digitalResource/digitalResource $digitalResource
     *
     * @return string
     */
    public function newDigitalResource($digitalResource)
    {
        $this->json->digitalResource = $digitalResource;

        return $this->json->save();
    }

    /**
     * Serializer JSON for conversion method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function convert($result)
    {
        if ($result == false) {
            $count = 0;
        } else {
            $count = count($result);
        }
        $this->json->message = '%1$s document(s) converted.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $count);
        $this->json->result = $result;

        return $this->json->save();
    }

    /**
     * Serializer JSON for validateRestitution method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function validateRestitution($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s restitution(s) validated.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s restitution(s) can not be validate(s).';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for cancelRestitution method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function cancelRestitution($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s restitution(s) canceled.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = ' %1$s restitution(s) can not be canceled.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * Serializer JSON for delete method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function dispose($result)
    {
        $echec = 0;
        $success = count($result['success']);
        if (array_key_exists('error', $result)) {
            $echec = count($result['error']);
        }
        
        $this->translator->setCatalog('recordsManagement/messages');
        $this->json->message = '%1$s / %2$s archive(s) flagged for destruction.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success,($echec+$success));

        return $this->json->save();
    }

    /**
     * Serializer JSON for cancelDestruction method
     * @param array $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function cancelDestruction($result)
    {
        $success = count($result['success']);
        $echec = count($result['error']);

        $this->json->message = '%1$s destruction(s) canceled.';
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->message = sprintf($this->json->message, $success);

        if ($echec > 0) {
            $message = '%1$s destruction(s) can not be canceled.';
            $message = $this->translator->getText($message);
            $message = sprintf($message, $echec);

            $this->json->message .= ' '.$message;
        }

        return $this->json->save();
    }

    /**
     * No organization unit exceptions
     * @param string $exception The exception
     *
     * @return string String serialized in JSON
     */
    public function noOrgUnitException($exception)
    {
        // Manage errors
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }

    /**
     * No depositor organization exceptions
     * @param string $exception The exception
     *
     * @return string String serialized in JSON
     */
    public function noDepositorOrgException($exception)
    {
        // Manage errors
        $this->json->status = false;
        $this->translator->setCatalog('recordsManagement/exception');
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }

    /**
     * DigitalResource exceptions
     * @param string $exception The exception
     *
     * @return string String serialized in JSON
     */
    public function formatIdentificationException($exception)
    {
        // Manage errors
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }

    /**
     * DigitalResource exceptions
     * @param string $exception The exception
     *
     * @return string String serialized in JSON
     */
    public function formatValidationException($exception)
    {
        // Manage errors
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }

    /**
     * DigitalResource exceptions
     * @param string $exception The exception
     *
     * @return string String serialized in JSON
     */
    public function unauthorizedDigitalResourceFormatException($exception)
    {
        // Manage errors
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }

    /**
     * Archive doesn't match with profile exceptions
     * @param string $exception The exception
     *
     * @return string String serialized in JSON
     */
    public function archiveDoesNotMatchProfileException($exception)
    {
        // Manage errors
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }

    /**
     * Archive doesn't match with profile exceptions
     * @param string $exception The exception
     *
     * @return string String serialized in JSON
     */
    public function notDisposableArchiveException($exception)
    {
        // Manage errors
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());

        return $this->json->save();
    }

    /**
     * Get digitalResource
     * @param digitalResource/digitalResource $digitalResource
     *
     * @return string
     */
    public function view($digitalResource)
    {
        $this->json->url = $url = \laabs::createPublicResource($digitalResource->getContents());

        return $this->json->save();
    }

    /**
     * Serializer JSON for check if archive exists
     * @param object $result Object with archiveId and a boolean 'exist'
     *
     * @return object JSON object with a status and certificate of deposit
     */
    public function exists($result)
    {
        if (!$result->exist) {
            $this->json->status = $result->exist;
            $this->translator->setCatalog('recordsManagement/exception');
            $this->json->message = $this->translator->getText("Archive with identifier '%s' doesn't exists");
            $this->json->message = sprintf($this->json->message, $result->archiveId);
        }

        return $this->json->save();
    }

    /**
     * Read users privileges on archives
     */
    protected function readPrivilegesOnArchives()
    {
        $hasModificationPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveManagement/modify");
        $hasIntegrityCheckPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveManagement/checkIntegrity");
        $hasDestructionPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "destruction/destructionRequest");

        $this->view->setSource('hasModificationPrivilege', $hasModificationPrivilege);
        $this->view->setSource('hasIntegrityCheckPrivilege', $hasIntegrityCheckPrivilege);
        $this->view->setSource('hasDestructionPrivilege', $hasDestructionPrivilege);
    }

    /**
     * Get the list of owner originators oranizations
     * @param object $currentService The user's current service
     *
     * @return array The list of owner originators orgs
     */
    protected function getOwnerOriginatorsOrgs($currentService)
    {
        $originators = \laabs::callService('organization/organization/readIndex', 'isOrgUnit=true');

        $orgController = \laabs::newController('organization/organization');

        $owner = false;
        $userServices = [];
        $ownerOriginatorOrgs = [];

        // Get all user services,  and check OWNER role on one of them
        $userServiceOrgRegNumbers = array_merge(array($currentService->registrationNumber), \laabs::callService('organization/userPosition/readDescendantservices', (string) $currentService->orgId));
        foreach ($userServiceOrgRegNumbers as $userServiceOrgRegNumber) {
            $userService = \laabs::callService('organization/organization/readByregnumber', $userServiceOrgRegNumber);
            $userServices[] = $userService;
            if (isset($userService->orgRoleCodes)) {
                foreach ($userService->orgRoleCodes as $orgRoleCode) {
                    if ($orgRoleCode == 'owner') {
                        $owner = true;

                        break;
                    }
                }
            }
        }
        foreach ($userServices as $userService) {
            foreach ($originators as $originator) {
                if ($owner || $originator->registrationNumber == $userService->registrationNumber) {
                    if (!isset($ownerOriginatorOrgs[(string) $originator->ownerOrgId])) {
                        $orgObject = \laabs::callService('organization/organization/read_orgId_', (string) $originator->ownerOrgId);
                        $ownerOriginatorOrgs[(string) $orgObject->orgId] = new \stdClass();
                        $ownerOriginatorOrgs[(string) $orgObject->orgId]->displayName = $orgObject->displayName;
                        $ownerOriginatorOrgs[(string) $orgObject->orgId]->orgId = $orgObject->orgId;
                        $ownerOriginatorOrgs[(string) $orgObject->orgId]->originators = [];
                    }
                    $ownerOriginatorOrgs[$originator->ownerOrgId]->originators[] = $originator;
                }
            }
        }

        return $ownerOriginatorOrgs;
    }

    protected function setDescription($descriptions, $archivalProfile = null) {
        $descriptionHtml = "";
        foreach ($descriptions as $name => $value) {
            if (\gettype($value) !== 'array' && \gettype($value) !== 'object') {
                if (isset($archivalProfile)) {
                    foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                        if ($archiveDescription->fieldName == $name) {
                            $name = $archiveDescription->descriptionField->label;
                            break;
                        }
                    }
                }

                if (!empty($value)) {
                    $descriptionHtml .= '<dt title="' . $name . '" name="' . $name . '">' . $name . '</dt>';
                    if (is_array($value)) {
                        foreach ($value as $metadata) {
                            $descriptionHtml .= '<dd>' . $metadata . '</dd>';
                        }
                    } else {

                        $descriptionHtml .= '<dd>' . $value . '</dd>';
                    }
                }
            }
        }

        foreach ($descriptions as $name => $value) {
            if (\gettype($value) === 'array' || \gettype($value) === 'object') {
                if (isset($archivalProfile)) {
                    foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                        if (!empty($name) && $archiveDescription->fieldName == $name) {
                            $name = $archiveDescription->descriptionField->label;
                            break;
                        }
                    }
                }

                if (!empty($name)) {
                    $id = \laabs::newId();
                    $descriptionHtml .= '
                        <br>
                        <div class="panel panel-info">
                            <div class="panel-heading" role="tab">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" href="#' . $id . '" aria-expanded="true" aria-controls="collapseOne" data-translate-catalog="medona/messages">
                                        ' . $name . '
                                    </a>
                                </h4>
                            </div>
                            <div id="' . $id . '" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                <div class="panel-body">
                                <dl class="dl dl-horizontal">';
                }

                if (isset($archivalProfile)) {
                    $descriptionHtml .= $this->setDescription($value, $archivalProfile);
                } else {
                    $descriptionHtml .= $this->setDescription($value);
                }

                if (!empty($name)) {
                    $descriptionHtml .= '</dl></div></div></div>';
                }
            }
        }

        return $descriptionHtml;
    }
}

