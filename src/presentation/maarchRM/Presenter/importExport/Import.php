<?php
/*
 * Copyright (C) 2020 Maarch
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
namespace presentation\maarchRM\Presenter\importExport;

/**
 * Import/export serializer html
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class Import
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $json;
    protected $translator;

    protected $userArchivalProfiles = [];

    /**
     * Constuctor of welcomePage html serializer
     * @param \dependency\html\Document   $view The view
     * @param \dependency\json\JsonObject $json Json utility
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $translator;
        $this->translator->setCatalog('importExport/messages');
    }

    public function home()
    {
        $this->view->addContentFile('importExport/index.html');

        $title = 'Import referentiels';
        $this->view->setSource("isExport", false);
        $this->view->merge();

        $this->view->translate();
        return $this->view->saveHtml();
    }

    public function import()
    {
        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Exception
     * @param Exception $exception exception
     *
     * @return string
     */
    public function exception($exception)
    {
        $this->json->load($exception);

        $this->json->setMessage($this->translator->getText($this->json->getMessage()));
        $this->json->status = false;

        return $this->json->save();
    }
}
