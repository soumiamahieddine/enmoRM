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
/*
 * Copyright (C) 2015 Alexis Ragot <alexis.ragot@maarch.org>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
     * @param \dependency\html\Document $view The view
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

        return $this->view->saveHtml();
    }
    
    /**
     * Show result log search
     * 
     * @return string
     */
    public function find($logs)
    {
        $this->view->addContentFile('recordsManagement/log/result.html');
        $this->view->translate();

        $dataTable = $this->view->getElementsByClass("dataTable")->item(0)->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(5);
        $dataTable->setSorting(array(array(1, 'desc')));
        
        foreach ($logs as $log) {
            $log->type = $this->view->translator->getText($log->type, false, 'recordsManagement/log');
        }
        
        $this->view->setSource("logs", $logs);
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
     * @param recordsManagement/log $log The log object
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
     * @param recordsManagement/log $log The log object
     * 
     * @return string
     */
    public function jounalException($exception)
    {
        $this->json->message = "failure";

        return $this->json->save();

    }

    
}
