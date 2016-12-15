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

namespace bundle\batchProcessing\Model;

/**
 *
 * @package BatchProcessing
 * @author  Alexandre Morin <alexandre.morin@maarch.org>
 *
 * @pkey [schedulingId]
 * @fkey [task] batchProcessing/task [taskId]
 */
class scheduling
{
    /**
     * The scheduling identifier
     *
     * @var string
     * @notempty
     */
    public $schedulingId;

    /**
     * The name
     *
     * @var string
     * @notempty
     */
    public $name;
    
    /**
     * The task identifier
     *
     * @var string
     * @notempty
     */
    public $taskId;

    /**
     * The frequency of task
     *
     * @var string
     */
    public $frequency;
    
    /**
     * The parameters
     * 
     * @var tokenlist
     */
    public $parameters;

    /**
     * Identifier service 
     *
     * @var string
     */
    public $executedBy;
    
    /**
     * The last execution of task
     *
     * @var timestamp
     */
    public $lastExecution;

    /**
     * The next execution of task
     *
     * @var timestamp
     */
    public $nextExecution;

    /**
     * The status
     *
     * @var string
     */
    public $status;
}
