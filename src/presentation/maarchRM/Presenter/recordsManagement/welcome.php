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

    /**
     * Constuctor of welcomePage html serializer
     * @param \dependency\html\Document $view The view
     */
    public function __construct(\dependency\html\Document $view)
    {
        $this->view = $view;
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
            $this->view->setSource("filePlan", [$filePlan]);
        } 

        // Profiles

        $archivalProfiles = \laabs::callService('recordsManagement/archivalProfile/readIndex', true);
        for ($i = 0, $count = count($archivalProfiles); $i < $count; $i++) {
            $archivalProfiles[$i] = \laabs::callService('recordsManagement/archivalProfile/read_archivalProfileId_', $archivalProfiles[$i]->archivalProfileId);
            $archivalProfiles[$i]->json = json_encode($archivalProfiles[$i]);
        }
        $this->view->setSource('archivalProfiles', $archivalProfiles);

        // Retention
        $retentionRules = \laabs::callService('recordsManagement/retentionRule/readIndex');
        for ($i = 0, $count = count($retentionRules); $i < $count; $i++) {
            $retentionRules[$i]->durationText = (string) $retentionRules[$i]->duration;
        }

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setSorting(array(array(1, 'asc')));
        $dataTable->setUnsortableColumns(0);
        $dataTable->setUnsortableColumns(4);
        $dataTable->setUnsearchableColumns(0);
        $dataTable->setUnsearchableColumns(4);

        $this->view->setSource('retentionRules', $retentionRules);
        $this->view->setSource('user', $user);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * @param array $archives
     * 
     * @return string
     */
    public function folderContents($archives)
    {
        $this->view->addContentFile("dashboard/mainScreen/folderContents.html");

        $this->view->setSource('archives', $archives);
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
}
