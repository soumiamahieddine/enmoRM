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

namespace bundle\batchProcessing\Controller;

/**
 * Class task
 *
 * @package batchProcessing
 * @author  Alexandre Morin <alexandre.morin@maarch.org>
 */
class task
{
    /* Properties */

    public $sdoFactory;

    /**
     * Constructor of access control class
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }
    
    /**
     * List the tasks
     * 
     * @return batchProcessing/task List task
     */
    public function index()
    {
        $taskList = $this->sdoFactory->find("batchProcessing/task");
        return $taskList;
    }
    
    /**
     * Create the requested task
     * @param object $task 
     *
     * @return boolean status of the query
     */
    public function create($task)
    {
        
        $task->taskId = \laabs::newId();
        try {
            $this->sdoFactory->create($task,"batchProcessing/task");
        } catch (\Exception $e) {
            throw \laabs::newException("batchProcessing/taskException", "Task not created.");
        }

        return $task->taskId;
    }
    
    /**
     * Edit a task
     * @param string $taskId The task id
     *
     * @return batchProcessing/task The task object
     */
    public function edit($taskId)
    {
        $task = $this->sdoFactory->read('batchProcessing/task', $taskId);

        return $task;
    }
    
    /**
     * Update a task
     * @param object $task 
     *
     * @return bool
     */
    public function update($task)
    {
        try {
            $this->sdoFactory->update($task,"batchProcessing/task");
        } catch (\Exception $e) {
            throw \laabs::newException("batchProcessing/taskException", "Task not updated.");
        }
        
        return true;
    }
    
    /**
     * Delete a task
     * @param string $taskId Task identifiant
     *
     * @return boolean
     */
    public function delete($taskId)
    {
        try {
            $this->sdoFactory->delete($taskId, "batchProcessing/task");
        } catch (\Exception $e) {
            throw \laabs::newException("batchProcessing/taskException", "Task not deleted.");
        }
        
        return true;
    }
}
