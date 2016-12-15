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
 * Serializer html for access code
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE <prosper.delaure@maarch.com>
 */
class accessRule
{

    public $view;
    
    protected $json;

    protected $translator;

    /**
     * Constuctor
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The json base object
     * @param \dependency\localisation\TranslatorInterface $translator The translator object
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator) 
    {
        
        $this->view = $view;
        
        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('recordsManagement/accessRule');
    }

    /**
     * Get access codes
     * @param array $accessRules
     * 
     * @return string
     */
    public function index($accessRules) 
    {
        $this->view->addContentFile('recordsManagement/accessRule/index.html');
        $orgUnits = \laabs::callService('organization/organization/readOrgunitList');

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(3);
        $dataTable->setUnsearchableColumns(3);

        foreach ($accessRules as $accessRule) {
            $accessRule->accessRuleDurationUnit = substr($accessRule->duration, -1);
            $accessRule->accessRuleDuration = substr($accessRule->duration, 1, -1);
            $accessRule->accessRuleDurationUnit = $this->view->translator->getText($accessRule->accessRuleDurationUnit, "duration", "recordsManagement/accessRule");
        }
        
        $this->view->translate();
        $this->view->setSource("orgUnits", $orgUnits);
        $this->view->setSource("accessRule", $accessRules);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * The view to edit a new access code
     * @param recordsManagement/accessRule $accessRule The access code
     * 
     * @return string
     */
    public function edit($accessRule) 
    {


        $accessRule->accessRuleDurationUnit = substr($accessRule->duration, -1);
        $accessRule->accessRuleDuration = substr($accessRule->duration, 1, -1);
        return json_encode($accessRule);
    }

    //JSON
    /**
     * Serializer JSON for create method
     * @param bool $result
     * 
     * @return object JSON object with a status and message parameters
     */
    public function create($result)
    {
        $this->json->status = $result;

        $this->json->message = "New access code created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for update method
     * @param object $result
     * 
     * @return object JSON object with a status and message parameters
     */
    public function update($result)
    {
        $this->json->status = $result;
        $this->json->message = "Access code updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    } 
   
    /**
     * Exception
     * @param recordsManagement/Exception/accessRuleException $accessRuleException
     * 
     * @return string
     */
    public function accessRuleException($accessRuleException)
    {
        $this->json->load($accessRuleException);
        $this->json->status = false;

        return $this->json->save();
    }
}
