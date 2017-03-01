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

    /**
     * Constuctor of welcomePage html serializer
     * @param \dependency\html\Document   $view The view
     * @param \dependency\json\JsonObject $json
     */
    public function __construct(\dependency\html\Document $view,\dependency\json\JsonObject $json)
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

        $accountToken = \laabs::getToken('AUTH');
        $user = \laabs::newController('auth/userAccount')->get($accountToken->accountId);

        // File plan tree
        $filePlan = \laabs::callService('filePlan/filePlan/readTree');
        if ($filePlan) {
            $this->getOrgUnitArchivalProfiles($filePlan);

            $filePlan = [$filePlan];
            $this->markTreeLeaf($filePlan);

            $this->view->setSource("filePlan", $filePlan);
        }

        // Retention
        $retentionRules = \laabs::callService('recordsManagement/retentionRule/readIndex');
        for ($i = 0, $count = count($retentionRules); $i < $count; $i++) {
            $retentionRules[$i]->durationText = (string) $retentionRules[$i]->duration;
        }

        // archival profiles for search form
        $archivalProfileController = \laabs::newController("recordsManagement/archivalProfile");
        $archivalProfiles = $archivalProfileController->index(true);

        foreach ($archivalProfiles as $archivalProfile) {
            $archivalProfileController->readDetail($archivalProfile);
            $archivalProfile->searchFields = [];
            foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                switch ($archiveDescription->descriptionField->type) {
                    case 'name':
                    case 'date':
                    case 'number':
                    case 'boolean':
                        $archivalProfile->searchFields[] = $archiveDescription->descriptionField;
                }
            }
        }

        $this->view->translate();

        $this->view->setSource("userArchivalProfiles", $archivalProfiles);

        foreach ($this->view->getElementsByClass('dateRangePicker') as $dateRangePickerInput) {
            $this->view->translate($dateRangePickerInput);
        }

        $this->view->setSource('retentionRules', $retentionRules);
        $this->view->setSource('user', $user);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    protected function getOrgUnitArchivalProfiles($orgUnit)
    {
        $orgUnit->archivalProfiles = \laabs::callService('recordsManagement/archivalProfile/readOrgunitprofiles', $orgUnit->registrationNumber);

        if (!empty($orgUnit->organization)) {
            foreach ($orgUnit->organization as $subOrgUnit) {
                $this->getOrgUnitArchivalProfiles($subOrgUnit);
            }
        }
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

        $this->view->translate();
        $this->view->setSource("filePlan", [$filePlan]);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show an archive information
     * @param array $archives
     *
     * @return string
     */
    public function archiveInfo($archive)
    {
        $this->view->addContentFile('dashboard/mainScreen/archiveInformation.html');

        $archive->depositDate = $archive->depositDate->format('Y-m-d H:i:s');
        $this->view->translate();

        $this->getDescription($archive);
        $this->view->setSource("archive", $archive);
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
        $this->view->addContentFile('dashboard/mainScreen/docuementInformation.html');

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
        $this->json->digitalResources = $archive->digitalResources;
        $this->json->childrenArchives = $archive->childrenArchives;

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
        if (isset($archive->descriptionObject)) {
            if (!empty($archive->descriptionClass)) {
                $presenter = \laabs::newPresenter($archive->descriptionClass);
                $descriptionHtml = $presenter->read($archive->descriptionObject);
            } else {
                $archivalProfile = [];
                if (!empty($archive->archivalProfileReference)) {
                    $archivalProfile = \laabs::callService('recordsManagement/archivalProfile/readByreference_reference_', $archive->archivalProfileReference);
                }

                $descriptionHtml = '<table">';
                
                foreach ($archive->descriptionObject as $name => $value) {
                    foreach ($archivalProfile->archiveDescription as $archiveDescription) {
                        if ($archiveDescription->fieldName == $name) {
                            $label = $archiveDescription->descriptionField->label;
                        }
                    }

                    if (!isset($label)) {
                        $label = $this->view->translator->getText($name, false, "recordsManagement/archive");
                    }

                    $descriptionHtml .= '<th name="'.$name.'">'.$label.'</th>';
                    $descriptionHtml .= '<td>'.$value.'</td>';
                }
                
                $descriptionHtml .='</table>';
            }

            if ($descriptionHtml) {
                $node = $this->view->getElementById("metadata");
                $this->view->addContent($descriptionHtml, $node);
            } else {
                unset($archive->descriptionObject);
            }
        }
    }

    /**
     * Mark leaf for html merging
     * @param object $tree The tree
     *
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
     *
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
}
