<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle DocumentManagement.
 *
 * Bundle DocumentManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle DocumentManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle DocumentManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Presentation\maarchRM\Presenter\documentManagement;

/**
 * DocumentManagement document html serializer
 *
 * @package DocumentManagement
 * @author  Maarch Alexis ragot <alexis.ragot@maarch.org>
 */
class document
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;

    protected $json;
    protected $translator;

    /**
     * Constuctor of registered mail html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The JSON object
     * @param \dependency\localisation\TranslatorInterface $translator The translator object
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;
        $this->json = $json;
        $this->translator = $translator;
        $this->translator->setCatalog('documentManagement/messages');
        $this->json->status = true;
    }

    /**
     * Search document
     *
     * @return string
     */
    public function search()
    {
        $this->view->addContentFile("documentManagement/search.html");
        $this->view->translate();

        $controlerFormat = \laabs::newController('digitalResource/format');
        $formats = $controlerFormat->index();

        $this->view->setSource("formats", $formats);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Result list of document
     * @param documentManagement/document[] $documents The document
     *
     * @return string
     */
    public function resultList($documents)
    {
        $this->view->addContentFile("documentManagement/list.html");
        
        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setUnsortableColumns(0);
        $dataTable->setUnsortableColumns(7);
        $this->view->translate();

        $this->view->setSource("documents", $documents);
        $this->view->merge();

        return $this->view->saveHtml();
    }
}
