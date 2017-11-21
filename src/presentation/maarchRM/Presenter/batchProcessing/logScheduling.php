<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle audit.
 *
 * Bundle audit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle audit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle audit.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\Presenter\batchProcessing;

/**
 * Bundle audit html serializer
 *
 * @package batchProcessing
 */
class logScheduling
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    protected $json;

    /**
     * __construct
     *
     * @param \dependency\html\Document   $view A new ready-to-use empty view
     * @param \dependency\json\JsonObject $json A new ready-to-use empty json
     *
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json)
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;
    }

    /**
     * Get form to search events
     *
     * @return string view
     */
    public function index()
    {
        $schedulings = \laabs::callService('batchProcessing/scheduling/readSchedulings');
        $tasks = \laabs::callService('batchProcessing/task/readIndex');

        $this->view->addContentFile("batchProcessing/logScheduling/search.html");
        $this->view->translate();

        $this->view->setSource('schedulings', $schedulings);
        $this->view->setSource('tasks', $tasks);
        $this->view->merge();
        $this->view->addScriptSrc(
<<<EOD
    $.ajaxSetup({
        headers: { 'X-Laabs-Max-Count': 300}
    });
EOD
        );

        return $this->view->saveHtml();
    }

    /**
     * Get event
     * @param batchProcessing/logScheduling $logSchedulings
     *
     * @return string view
     */
    public function getlogSchedulings($logSchedulings)
    {
        $this->view->addContentFile("batchProcessing/logScheduling/result.html");

        $this->view->translate();
        $this->view->setSource("logSchedulings", $logSchedulings);
        $this->view->merge();

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(1);
        $dataTable->setUnsortableColumns(2);
        $dataTable->setSorting(array(array(0, 'desc')));

        return $this->view->saveHtml();
    }

    /**
     * Get event
     * @param batchProcessing/logScheduling $logScheduling
     *
     * @return string view
     */
    public function getlogScheduling($logScheduling)
    {
        $this->view->addContentFile("batchProcessing/logScheduling/modalEvent.html");

        $this->view->translate();
        $this->view->setSource("logScheduling", $logScheduling);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    //JSON
    /**
     * Exception
     * @param audit/Exception/eventException $eventException
     *
     * @return string
     */
    public function eventException($eventException)
    {
        $this->json->load($eventException);
        $this->json->status = false;

        return $this->json->save();
    }
}
