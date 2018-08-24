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
namespace presentation\maarchRM\Presenter\recordsManagement;

/**
 * welcomePage serializer html
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class welcome
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $json;

    protected $userArchivalProfiles = [];

    /**
     * Constuctor of welcomePage html serializer
     * @param \dependency\html\Document   $view The view
     * @param \dependency\json\JsonObject $json Json utility
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json)
    {
        $this->view = $view;
        $this->view->translator->setCatalog('recordsManagement/messages');

        $this->json = $json;
        $this->json->status = true;
    }

    /**
     * Get a welcome page
     *
     * @return string
     */
    public function welcomePage()
    {
        //$this->view->addHeaders();
        //$this->view->useLayout();
        $this->view->addContentFile("dashboard/mainScreen/main.html");

        $this->view->translate();

        $currentOrganization = \laabs::getToken("ORGANIZATION");
        $accountToken = \laabs::getToken('AUTH');
        $userAccountController = \laabs::newController('auth/userAccount');
        $user = $userAccountController->get($accountToken->accountId);
        
        // File plan tree
        $filePlanPrivileges = \laabs::callService('auth/userAccount/readHasprivilege', "archiveManagement/filePlan");

        $syncImportPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/deposit");
        $asyncImportPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/transferImport");

        $filePlan = \laabs::callService('filePlan/filePlan/readTree');
        if ($filePlan) {
            $this->getOrgUnitArchivalProfiles($filePlan);

            $filePlan = [$filePlan];
            $this->markTreeLeaf($filePlan);

            $this->view->setSource("filePlan", $filePlan);
            $this->view->setSource("filePlanPrivileges", $filePlanPrivileges);
            $this->view->merge($this->view->getElementById('filePlanTree'));
            $this->view->translate();

        }

        // Retention
        $retentionRules = \laabs::callService('recordsManagement/retentionRule/readIndex');
        for ($i = 0, $count = count($retentionRules); $i < $count; $i++) {
            $retentionRules[$i]->durationText = (string) $retentionRules[$i]->duration;
        }

        // archival profiles for search form
        foreach ($this->userArchivalProfiles as $archivalProfile) {
            /*if ($archivalProfile == "*") {
                $currentOrganization->acceptArchiveWithoutProfile = true;
            }

            $archivalProfileController->readDetail($archivalProfile);*/

            $archivalProfile->searchFields = [];
            foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                switch ($archiveDescription->descriptionField->type) {
                    case 'text':
                    case 'name':
                    case 'date':
                    case 'number':
                    case 'boolean':
                        $archivalProfile->searchFields[] = $archiveDescription->descriptionField;
                }
            }
        }
        
        $depositPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/deposit");
        $this->view->translate();

        $this->view->setSource("userArchivalProfiles", $this->userArchivalProfiles);
        $this->view->setSource("depositPrivilege", $depositPrivilege);
        $this->view->setSource("syncImportPrivilege", $syncImportPrivilege);
        $this->view->setSource("asyncImportPrivilege", $asyncImportPrivilege);
        $this->view->setSource("filePlanPrivileges", $filePlanPrivileges);
        

        foreach ($this->view->getElementsByClass('dateRangePicker') as $dateRangePickerInput) {
            $this->view->translate($dateRangePickerInput);
        }

        $this->view->setSource('retentionRules', $retentionRules);
        $this->view->setSource('user', $user);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show the events search form
     * @param object $filePlan The root orgUnit of user with sub-orgUnits and folders
     *
     * @return string
     */
    public function showTree($filePlan)
    {
        $this->view->addContentFile('filePlan/filePlanTree.html');
        $this->markTreeLeaf([$filePlan]);

        $this->view->setSource("filePlan", [$filePlan]);
        $this->view->merge();
        $this->view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Show an archive information
     * @param recordsManagement/archive $archive The archive
     *
     * @return string
     */
    public function archiveInfo($archive)
    {
        $this->view->addContentFile('dashboard/mainScreen/archiveInformation.html');

        // Archive
        $originatorOrg = \laabs::callService('organization/organization/readByregnumber', $archive->originatorOrgRegNumber);
        $archive->originatorOrgName = $originatorOrg->displayName;

        $archive->depositDate = $archive->depositDate->format('Y-m-d H:i:s');
        if ($archive->originatingDate) {
            $archive->originatingDate = $archive->originatingDate;
        }

        // Retention
        $retentionRules = \laabs::callService('recordsManagement/retentionRule/readIndex');
        for ($i = 0, $count = count($retentionRules); $i < $count; $i++) {
            $retentionRules[$i]->durationText = (string) $retentionRules[$i]->duration;
        }

        $archivalProfileList = [];
        $acceptArchiveWithoutProfile = $acceptUserIndex = false;

        // Add a sub archive
        $depositPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveDeposit/deposit");
        $fileplanLevel = false;
        if ($depositPrivilege) {
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

                        $archivalProfileList[] = $profileObject;
                    }
                }

                if ((!count($archivalProfileList) && !$archivalProfile->acceptArchiveWithoutProfile) || $archivalProfile->fileplanLevel == 'file') {
                    $depositPrivilege = false;
                }

                $acceptArchiveWithoutProfile = $archivalProfile->acceptArchiveWithoutProfile;
                $fileplanLevel = $archivalProfile->fileplanLevel;
                $acceptUserIndex = $archivalProfile->acceptUserIndex;
            } else {
                $acceptArchiveWithoutProfile = true;
                $fileplanLevel = true;
            }
        }

        $this->view->translate();

        $this->view->setSource("status", $archive->status);

        $archive->status = $this->view->translator->getText($archive->status, false, "recordsManagement/messages");
        $archive->finalDisposition = $this->view->translator->getText($archive->finalDisposition, false, "recordsManagement/messages");

        $this->getDescription($archive);
        $this->view->setSource('retentionRules', $retentionRules);
        $this->view->setSource("archive", $archive);
        $this->view->setSource("depositPrivilege", $depositPrivilege);
        $this->view->setSource("archivalProfileList", $archivalProfileList);
        $this->view->setSource("fileplanLevel", $fileplanLevel);
        $this->view->setSource("acceptArchiveWithoutProfile", $acceptArchiveWithoutProfile);
        $this->view->setSource("acceptUserIndex", $acceptUserIndex);
        $this->view->setSource('managementPrivilege', \laabs::callService('auth/userAccount/readHasprivilege', "archiveManagement/modify"));

        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show a document information
     *
     * @return string
     */
    public function documentInfo()
    {
        $this->view->addContentFile('dashboard/mainScreen/documentInformation.html');
        $this->view->translate();

        if (isset(\laabs::configuration('presentation.maarchRM')['displayableFormat'])) {
            $this->view->setSource("displayableFormat", json_encode(\laabs::configuration('presentation.maarchRM')['displayableFormat']));
        } else {
            $this->view->setSource("displayableFormat", json_encode(array()));
        }

        $this->view->merge();

        return $this->view->saveHtml();
    }
    /**
     * Display error
     * @param object $error
     *
     * @return string
     */
    public function error($error)
    {
        $this->view->addContentFile("dashboard/error.html");

        $this->view->translate();

        $this->view->setSource('error', $error);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show a folder content
     * @param array $archives
     *
     * @return string
     */
    public function folderContents($archives)
    {
        $organizations = \laabs::callService('organization/organization/readIndex');
        $orgsName = [];

        foreach ($organizations as $organization) {
            $orgsName[$organization->registrationNumber] = $organization->displayName;
        }

        foreach ($archives as $archive) {
            $archive->originatorOrgName = $orgsName[$archive->originatorOrgRegNumber];
        }

        $this->json->archives = $archives;

        return $this->json->save();
    }

    /**
     * Show an archive content
     * @param object $archive
     *
     * @return string
     */
    public function archiveContent($archive)
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
     * Show the result of movin²g an archive into a folder
     * @param int $result
     *
     * @return string
     */
    public function moveArchivesToFolder($result)
    {
        $this->view->translator->setCatalog('filePlan/messages');
        if ($result == 1) {
            $this->json->message = "The archive was moved.";
            $this->json->message = $this->view->translator->getText($this->json->message);
            
        } else {
            $this->json->message = '%1$s archives were moved.';
            $this->json->message = $this->view->translator->getText($this->json->message);
            $this->json->message = sprintf($this->json->message, $result);
        }

        return $this->json->save();
    }

    /**
     * Get archive description
     * @param archive $archive
     *
     * @return string
     */
    protected function getDescription($archive)
    {
        $archivalProfile = null;
        $modificationPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', "archiveManagement/modifyDescription");

        if (!empty($archive->archivalProfileReference)) {
            $archivalProfile = \laabs::callService('recordsManagement/archivalProfile/readByreference_reference_', $archive->archivalProfileReference);
            $archive->archivalProfileName = $archivalProfile->name;
        }

        if (!empty($archive->descriptionClass)) {
            $presenter = \laabs::newPresenter($archive->descriptionClass);
            $descriptionHtml = $presenter->read($archive->descriptionObject);
            $modificationPrivilege = false;

        } else {
            $descriptionHtml = '<table">';

            if (isset($archive->descriptionObject)) {
                foreach ($archive->descriptionObject as $name => $value) {
                    $isImmutable = false;
                    $label = $type = $archivalProfileField = null;
                    if ($archivalProfile) {
                        foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                            if ($archiveDescription->fieldName == $name) {
                                $label = $archiveDescription->descriptionField->label;
                                $archivalProfileField = true;
                                $type = $archiveDescription->descriptionField->type;
                                $isImmutable = $archiveDescription->isImmutable;
                            }
                        }
                    }

                    if (empty($label)) {
                        $label = $this->view->translator->getText($name, false, "recordsManagement/archive");
                    }

                    if (empty($type)) {
                        $type = 'text';
                        if (!empty($value)) {
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
                    }
                    if(!is_array($value)){
                        if ($archivalProfileField) {
                            $descriptionHtml .= '<tr class="archivalProfileField">';
                        } else {
                            $descriptionHtml .= '<tr>';
                        }

                        $descriptionHtml .= '<th title="'.$label.'" name="'.$name.'" data-type="'.$type.'"'.'data-Immutable="'.$isImmutable.'">'.$label.'</th>';
                        if ($type == "date") {
                                $textValue = \laabs::newDate($value);
                        } else {
                            $textValue = $value;
                        }
                        if ($type == 'boolean') {
                            $textValue = $value ? '<i class="fa fa-check" data-value="1"/>' : '<i class="fa fa-times" data-value="0"/>';
                        }

                        $descriptionHtml .= '<td title="'.$value.'">'.$textValue.'</td>';
                        $descriptionHtml .= '</tr>';
                    }
                }

            }
            $descriptionHtml .= '</table>';
        }

        if ($descriptionHtml) {
            $node = $this->view->getElementById("metadata");
            $this->view->addContent($descriptionHtml, $node);
        } else {
            unset($archive->descriptionObject);
        }

        $this->view->setSource('modificationPrivilege', $modificationPrivilege);
    }

    /**
     * Mark leaf for html merging
     * @param object $tree The tree
     */
    protected function markTreeLeaf($tree)
    {
        foreach ($tree as $node) {
            if (!isset($node->organization) && !isset($node->folder)) {
                $node->isLeaf = true;
            } else {
                if (isset($node->organization)) {
                    $this->markTreeLeaf($node->organization);
                }
                if (isset($node->folder)) {
                    $this->updateFolderPath($node->folder, $node->displayName);
                }
            }
        }
    }

    /**
     * Add owner organization name in folder path
     * @param object $tree      The tree
     * @param string $ownerName The owner organizaiton name
     */
    protected function updateFolderPath($tree, $ownerName)
    {
        foreach ($tree as $node) {
            $node->path = $ownerName.'/'.$node->path;
            if ($node->subFolders) {
                $this->updateFolderPath($node->subFolders, $ownerName);
            }
        }
    }

    protected function getOrgUnitArchivalProfiles($orgUnit)
    {
        $orgUnit->archivalProfiles = \laabs::callService('organization/organization/readOrgunitprofiles', $orgUnit->registrationNumber);

        foreach ($orgUnit->archivalProfiles as $i => $archivalProfile) {
            if ($archivalProfile == "*") {
                $orgUnit->acceptArchiveWithoutProfile = true;
                unset($orgUnit->archivalProfiles[$i]);
            } else {
                $this->userArchivalProfiles[$archivalProfile->reference] = $archivalProfile;
            }
        }

        $orgUnit->archivalProfiles = array_values($orgUnit->archivalProfiles);

        if (!empty($orgUnit->organization)) {
            foreach ($orgUnit->organization as $subOrgUnit) {
                $this->getOrgUnitArchivalProfiles($subOrgUnit);
            }
        }
    }
}
