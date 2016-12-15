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
 * Standard interface for a batchProcessing class
 */
interface schedulingInterface
{
    /**
     * Create the scheduling
     *
     * @param batchProcessing/scheduling $scheduling 
     *
     * @action batchProcessing/scheduling/create
     */
    public function create($scheduling);

    /**
     * Read all scheduling
     * 
     * @action batchProcessing/scheduling/index
     */
    public function readSchedulings();
    
    /**
     * Read all task
     * 
     * @action batchProcessing/scheduling/readTaskList
     */
    public function readTasks();

    /**
     * Update a scheduling
     *
     * @param batchProcessing/scheduling $scheduling 
     * 
     * @action batchProcessing/scheduling/update
     */
    public function update($scheduling);

    /**
     * Delete a scheduling
     *
     * @action batchProcessing/scheduling/delete
     */
    public function delete_schedulingId_();

    /**
     * Execute a scheduling
     *
     * @action batchProcessing/scheduling/execute
     */
    public function readExecute_schedulingId_();
    
    /**
     * Process scedulings
     *
     * @action batchProcessing/scheduling/process
     */
    public function updateProcess();
    
    /**
     * Update status of scheduling
     * 
     * @action batchProcessing/scheduling/changeStatus
     */
    public function updateChangestatus($schedulingId, $status);
}
