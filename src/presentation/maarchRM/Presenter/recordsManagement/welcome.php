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
     * @param array $archives
     *
     * @return string
     */
    public function folderContents($archives)
    {
        $this->json->archives = $archives;

        return $this->json->save();

        /*$this->view->addContentFile("dashboard/mainScreen/folderContents.html");
        $this->view->translate();
        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setSorting(array(array(1, 'asc')));
        $dataTable->setUnsortableColumns(0);
        $dataTable->setUnsortableColumns(5);
        $dataTable->setUnsearchableColumns(0);
        $dataTable->setUnsearchableColumns(5);
        $this->view->setSource('archives', $archives);
        $this->view->merge();

        return $this->view->saveHtml();
*/
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
     * Display error
     * @param object $error
     *
     * @return string
     */
    public function error($error)
    {
        //$this->view->addHeaders();
        //$this->view->useLayout();
        $this->view->addContentFile("dashboard/error.html");

        $this->view->translate();

        $this->view->setSource('error', $error);
        $this->view->merge();

        return $this->view->saveHtml();
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
