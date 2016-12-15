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

namespace Presentation\maarchRM\Presenter\recordsManagement;

/**
 * Bundle recordsManagement html serializer
 * @author Prosper De Laure <prosper.delaure@maarch.org>
 * @package recordsManagement
 */
class archiveImport
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $sdoFactory;
    protected $json;

    /**
     * Constructor of import archive presenter
     *
     * @param \dependency\html\Document   $view       A new ready-to-use empty view
     * @param \dependency\sdo\Factory     $sdoFactory The Sdo Factory for data access
     * @param \dependency\json\JsonObject $json       The json object
     */
    public function __construct(\dependency\html\Document $view, \dependency\sdo\Factory $sdoFactory, \dependency\json\JsonObject $json)
    {
        $this->view = $view;
        $this->view->translator->setCatalog("recordsManagement/archive");
        $this->sdoFactory = $sdoFactory;

        $this->json = $json;
        $this->json->status = true;
    }

    /**
     * Get import form
     *
     * @return string The html string
     */
    public function form()
    {
        $this->view->addContentFile("recordsManagement/archiveImport/import.html");

        $archivalProfiles = \laabs::callService('recordsManagement/archivalProfile/readIndex', true);

        $retentionRules = \laabs::callService('recordsManagement/retentionRule/readIndex');


        //var_dump($retentionRules);
        for ($i = 0, $count = count($archivalProfiles); $i < $count; $i++) {
            $archivalProfiles[$i] = \laabs::callService('recordsManagement/archivalProfile/read_archivalProfileId_', $archivalProfiles[$i]->archivalProfileId);
            $archivalProfiles[$i]->json = json_encode($archivalProfiles[$i]);
        }

        foreach ($retentionRules as $retentionRule) {
            $retentionRule->durationText = (string) $retentionRule->duration;
        }

        $this->view->translate();
        $this->view->setSource('archivalProfiles', $archivalProfiles);
        $this->view->setSource('retentionRules', $retentionRules);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    //JSON
    /**
     * Serializer JSON for import method
     *
     * @return object JSON object with a status and message parameters
     */
    public function import()
    {
        $this->json->message = "Archive imported";
        $this->json->message = $this->view->translator->getText($this->json->message);

        return $this->json->save();
    }
}
