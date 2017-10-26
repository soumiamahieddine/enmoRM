<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\adminTech;

/**
 * User story task 
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
interface taskInterface
{
    /**
     *  List the task
     *
     * @return batchProcessing/task/index The list of task
     * @uses batchProcessing/task/readIndex
     */
    public function readBatchprocessingTasks();

    /**
     * New empty task
     *
     * @return batchProcessing/task/newTask
     * 
     */
    public function readBatchprocessingTask();

    /**
     * Create a task
     * @param batchProcessing/task $task The task
     *
     * @return batchProcessing/task/create
     * @uses batchProcessing/task/create
     */
    public function createBatchprocessingTask($task);

    /**
     * Edit a task
     * @param string $taskId
     *
     * @return batchProcessing/task/edit
     * @uses batchProcessing/task/read_taskId_
     */
    public function readBatchprocessingTask_taskId_();

    /**
     * update a task
     * @param batchProcessing/task $task The task
     *
     * @return batchProcessing/task/update
     *
     * @uses batchProcessing/task/update_taskId_
     */
    public function updateBatchprocessingTask_taskId_($task);

    /**
     * delete a task
     *
     * @return batchProcessing/task/delete
     *
     * @uses batchProcessing/task/delete_taskId_
     */
    public function deleteBatchprocessingTask_taskId_();
}