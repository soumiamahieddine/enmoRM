<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle batchProcessing.
 *
 * Bundle batchProcessing is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle batchProcessing is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle batchProcessing.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\Presenter\batchProcessing;

/**
 * search batchProcessing html serializer
 *
 * @package batchProcessing
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
class task
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $json;
    protected $translator;

    /**
     * Constuctor of batchProcessing html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The Json object
     * @param \dependency\localisation\TranslatorInterface $translator The translator object
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    ) {
        $this->view = $view;
        $this->json = $json;
        $this->translator = $translator;
        $this->translator->setCatalog("batchProcessing/messages");
    }

    /**
     * Get access tasks
     * @param array $tasks
     *
     * @return string
     */
    public function index($tasks)
    {
        $this->view->addContentFile('batchProcessing/task/index.html');

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(2);
        $dataTable->setUnsearchableColumns(2);

        $this->view->translate();
        $this->view->setSource("task", $tasks);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * The view to edit a new task
     * @param batchProcessing/task $task The task
     *
     * @return string
     */
    public function edit($task = null)
    {
        $this->view->addContentFile('batchProcessing/task/edit.html');

        $this->view->translate();
        $this->view->setSource("task", $task);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * The view to create a new task
     *
     * @return string
     */
    public function newTask()
    {
        $this->view->addContentFile('batchProcessing/task/edit.html');

        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
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

        $this->json->message = "Task created";
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
        $this->json->message = "Task updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for delete method
     * @param object $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function delete($result)
    {
        $this->json->status = $result;
        $this->json->message = "Task deleted";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }
}
