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
 * Description of log
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class log
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    protected $json;

    /**
     * Constuctor of archival Agreement html serializer
     * @param \dependency\html\Document   $view The view
     * @param \dependency\json\JsonObject $json The view
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json)
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;
    }

    /**
     * Show the log search form
     *
     * @return string
     */
    public function search()
    {
        $this->view->addContentFile('recordsManagement/log/searchForm.html');
        $this->view->translate();

        $maxResults = null;
        if (isset(\laabs::configuration('presentation.maarchRM')['maxResults'])) {
            $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        }
        $this->view->setSource("maxResults", $maxResults);
        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Show result log search
     *
     * @param array   $logs         The arry of object
     * @param integer $totalResults Max number of total results from query
     *
     * @return string
     */
    public function find($logs, $totalResults)
    {
        $this->view->addContentFile('recordsManagement/log/result.html');
        $this->view->translate();

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(5);
        $dataTable->setSorting(array(array(1, 'desc')));

        foreach ($logs as $log) {
            $log->typeTranslate = $this->view->translator->getText($log->type, false, 'recordsManagement/log');

            $log->resId = \laabs::callService('recordsManagement/archives/readArchivecontents_archive_', (string) $log->archiveId)->digitalResources[0]->resId;
        }

        $hasReachMaxResults = false;
        if (isset(\laabs::configuration('presentation.maarchRM')['maxResults'])
            && $totalResults >= \laabs::configuration('presentation.maarchRM')['maxResults']) {
            $hasReachMaxResults = true;
        }

        $this->view->setSource('hasReachMaxResults', $hasReachMaxResults);
        $this->view->setSource('totalResults', $totalResults);
        $this->view->setSource("logs", $logs);
        $this->view->merge();

        return $this->view->saveHtml();
    }


    /**
     *
     */
    public function contents($res)
    {
        $journal = explode(PHP_EOL, $res);
        $id = str_getcsv($journal[1]);
        $head = str_getcsv($journal[2]);

        $events = [];
        for ($i = 3; $i < count($journal) - 1; $i++) {
            $events[] = str_getcsv($journal[$i]);
        }

        $type = $journal[0];
        $typeTranslate = $this->view->translator->getText($type, false, 'recordsManagement/log');

        $this->view->addContentFile("recordsManagement/log/view.html");
        $this->view->translate();

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setSorting(array(array(0, 'desc')));

        if ($type === "lifeCycle") {
            $dataTable->setUnsortableColumns(2);
        } else if ( $type === "application") {
            for ($i = 0; $i < count($events);  $i++) {
                $events[$i][2] = $this->view->translator->getText($events[$i][2], false, 'audit/messages');
            }
        }

        $this->view->setSource("archiveId", $id[0]);
        $this->view->setSource("resourceId", $id[1]);
        $this->view->setSource("type", $type);
        $this->view->setSource("typeTranslate", $typeTranslate);
        $this->view->setSource("head", $head);
        $this->view->setSource("events", $events);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * View log
     * @param recordsManagement/log $log The log object
     *
     * @return string
     */
    public function read($log)
    {
        $this->view->addContentFile("recordsManagement/log/log.table.html");
        $this->view->translate();

        $this->view->setSource("log", $log);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Chech integrity
     * @param object $chainEvent The chain event
     *
     * @return string
     */
    public function checkIntegrity($chainEvent)
    {
        $this->json->message = "success";

        return $this->json->save();

    }

    /**
     * Chech integrity
     * @param object $exception The exception
     *
     * @return string
     */
    public function jounalException($exception)
    {
        $this->json->message = "failure";

        return $this->json->save();

    }
}
