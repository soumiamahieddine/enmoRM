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
 * Presenter of description fields
 *
 * @package RecordsManagement
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class descriptionField
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;

    protected $json;

    protected $translator;

    /**
     * Constuctor of description field presenter
     * @param \dependency\html\Document                    $view       The document HTML service obect
     * @param \dependency\json\JsonObject                  $json       The JSON service object
     * @param \dependency\localisation\TranslatorInterface $translator The translator service object
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('recordsManagement/descriptionField');
    }

    /**
     * Get description fields
     *
     * @return string The HTML result
     */
    public function index()
    {
        $baseDescriptionFields = \laabs::callService('recordsManagement/descriptionField/readIndex');
        $extendedDescriptionFields = \laabs::callService('recordsManagement/descriptionScheme/read_name_descriptionFields');

        $descriptionFields = array_map(
            function ($field) use ($baseDescriptionFields) {
                $field->extended = !isset($baseDescriptionFields[$field->name]);
                return $field;
            },
            $extendedDescriptionFields
        );

        $this->view->addContentFile('recordsManagement/descriptionField/index.html');

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(3);

        $this->view->translate();

        foreach ($descriptionFields as $descriptionField) {
            $descriptionField->translateType = $this->translator->getText($descriptionField->type);
        }

        $this->view->setSource("descriptionFields", $descriptionFields);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Get a description field
     * @param recordsManagement/descriptionField $descriptionField The description field object
     *
     * @return string The JSON result with a status and the description field parameters
     */
    public function edit($descriptionField)
    {
        $this->json->descriptionField = $descriptionField;

        return $this->json->save();
    }

    /**
     * The create response
     *
     * @return string The JSON result with with a status and message parameters
     */
    public function create()
    {

        $this->json->message = "Description field created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * The update response
     *
     * @return string The JSON result with a status and message parameters
     */
    public function update()
    {
        $this->json->message = "Description field updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * The delete response
     *
     * @return string The JSON result with a status and message parameters
     */
    public function delete()
    {
        $this->json->message = "Description field deleted";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
}
