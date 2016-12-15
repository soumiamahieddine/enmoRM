<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\organization;

/**
 * Bundle organization type presenter
 *
 * @package Organization
 */
class orgType
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    public $view;
    public $sdoFactory;

    /**
     * __construct
     *
     * @param \dependency\html\Document   $view       A new ready-to-use empty view
     * @param \dependency\json\JsonObject $jsonObject The json base object
     * @param \dependency\sdo\Factory     $sdoFactory The Sdo Factory for data access
     */
    public function __construct(
        \dependency\html\Document $view, 
        \dependency\json\JsonObject $jsonObject, 
        \dependency\sdo\Factory $sdoFactory)
    {
        $this->view = $view;
        
        $this->json = $jsonObject;
        $this->json->status = true;

        $this->sdoFactory = $sdoFactory;
        
        $this->translator = $this->view->translator;
        $this->translator->setCatalog('organization/messages');
        
    }

    /**
     * index
     * @param array $orgTypes Array of organization type
     * 
     * @return view
     */
    public function index(array $orgTypes)
    {
        $this->view->addContentFile("organization/orgType/orgType.html");
        
        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(2);

        $this->view->translate();
        
        $this->view->setSource("orgTypes", $orgTypes);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    // JSON
    /*
     * Serializer JSON for seting createOrganizationtype method
     * 
     * @return object JSON object with a status and message parameters
     */
    public function createOrganizationtype()
    {
        $this->json->message = "Organization type created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /*
     * Serializer JSON for seting updateOrganizationtype method
     * 
     * @return object JSON object with a status and message parameters
     */
    public function updateOrganizationtype()
    {
        $this->json->message = "Organization type updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

}