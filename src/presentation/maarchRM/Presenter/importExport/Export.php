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
class Export
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $json;
    public $maxResults;

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

        $this->maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
    }

    public function home()
    {
        $this->view->addContentFile('importExport/index.html');
        $this->view->translate();
        $title = 'Export referentiels';
        $this->view->setSource("title", $title);
        $this->view->setSource("maxResults", $this->maxResults);

        $this->view->merge();

        return $this->view->saveHtml();
    }

    public function listCsv()
    {
        $csv = ob_get_contents();
        ob_end_clean();

        $lines = explode("\n", $csv);

        $keys = str_getcsv($lines[0]);
        unset($lines[0]);

        //remove last null line
        array_pop($lines);

        $rows = [];
        foreach ($lines as $lineNumber => $line) {
            $rows[] = str_getcsv($line, $delimiter = ',', $enclosure = '"');
        }

        $this->view->addContentFile('importExport/dataTableTemplate.html');

        $dataTable = $this->view->getElementsByClass("table")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        $this->view->setSource("keys", $keys);
        $this->view->setSource("rows", $rows);
        $this->view->setSource("csv", $csv);

        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    public function export()
    {
        $csv = ob_get_contents();
        ob_end_clean();

        \laabs::setResponseType("text/csv");
        $response = \laabs::kernel()->response;
        $response->setHeader('Content-Disposition', 'attachment; filename="export.csv"');

        return $csv;
    }
}
