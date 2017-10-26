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
namespace bundle\recordsManagement\Model;
/**
 * Class for log archive description
 *
 * @package recordsManagement
 * @author  Cyril VAZQUEZ (MAARCH) <cyril.vazquez@maarch.org>
 * 
 * @pkey [archiveId]
 */
class log
{
    /**
     * The universal identifier
     *
     * @var id
     */
    public $archiveId;

    /**
     * The timestamp of the creation
     *
     * @var timestamp
     */
    public $fromDate;

    /**
     * The timestamp of the creation
     *
     * @var timestamp
     */
    public $toDate;

    /**
     * The type
     *
     * @var string
     * @enumeration [lifeCycle, application, system]
     */
    public $type;

    /**
     * The process name
     *
     * @var string
     */
    public $processName;

    /**
     * The owner registration number
     *
     * @var string
     */
    public $ownerOrgRegNumber;
}