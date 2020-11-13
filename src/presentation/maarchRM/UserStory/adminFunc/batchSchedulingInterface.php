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
namespace presentation\maarchRM\UserStory\adminFunc;

/**
 * User story admin organization
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
interface batchSchedulingInterface
{
    /**
     * Index of batch scheduling
     *
     * @uses batchProcessing/scheduling/readSchedulings
     * @return batchProcessing/scheduling/index
     */
    public function readBatchprocessingSchedulings();

    /**
     * Create the scheduling
     *
     * @param object $scheduling
     *
     * @uses batchProcessing/scheduling/create
     * @return batchProcessing/scheduling/create
     */
    public function createBatchprocessingScheduling($scheduling);

    /**
     * Update the scheduling
     *
     * @param object $scheduling
     *
     * @uses batchProcessing/scheduling/update
     * @return batchProcessing/scheduling/update
     */
    public function updateBatchprocessingScheduling_schedulingId_($scheduling);

    /**
     * Delete a scheduling
     *
     * @uses batchProcessing/scheduling/delete_schedulingId_
     * @return batchProcessing/scheduling/delete
     */
    public function deleteBatchprocessingScheduling_schedulingId_();

    /**
     * Execute a scheduling
     *
     * @uses batchProcessing/scheduling/readExecute_schedulingId_
     * @return batchProcessing/scheduling/execute
     */
    public function readBatchprocessing_schedulingId_Execute();

    /**
     * Update status of scheduling
     *
     * @param string $schedulingId
     * @param bool   $status
     *
     * @uses batchProcessing/scheduling/updateChangestatus
     * @return batchProcessing/scheduling/changeStatus
     */
    public function updateBatchprocessingChangestatus($schedulingId, $status);

    /**
     * List service accounts available by scheduling
     *
     * @param  string $serviceUri url route to check right for
     *
     * @uses auth/serviceAccount/readByRoute
     * @return batchProcessing/scheduling/listServiceAccounts
     */
    public function readServiceaccountsbyroute($serviceUri);

    /*******************LOG**********************/

    /**
     * Get log scheduling list
     *
     * @uses batchProcessing/logScheduling/read_schedulingId__logDate_
     * @return batchProcessing/logScheduling/getlogSchedulings
     */
    public function readBatchprocessingLogschedulings_schedulingId__logDate_();

    /**
     * Get log
     *
     * @uses batchProcessing/logScheduling/read_logId_
     * @return batchProcessing/logScheduling/getlogScheduling
     */
    public function readBatchprocessingLogscheduling_logId_();
}
