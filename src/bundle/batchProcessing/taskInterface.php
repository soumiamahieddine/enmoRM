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
namespace bundle\batchProcessing;
/**
 * Standard interface for a task class
 */
interface taskInterface
{
    /**
     *  List the task
     *
     * @action batchProcessing/task/index The list of task
     *
     */
    public function readIndex();

    /**
     * Edit a task
     *
     * @action batchProcessing/task/edit
     */
    public function read_taskId_();

    /**
     * create a task
     * @param batchProcessing/task $task The task
     *
     * @action batchProcessing/task/create
     */
    public function create($task);

    /**
     * update a task
     * @param batchProcessing/task $task The task
     *
     * @action batchProcessing/task/update
     */
    public function update_taskId_($task);

    /**
     * delete a task
     *
     * @action batchProcessing/task/delete
     */
    public function delete_taskId_();
}
