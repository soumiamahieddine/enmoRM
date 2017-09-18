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

namespace bundle\recordsManagement\Message;

/**
 * Class model that represents a retentionRule applied to an archive
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 */
class archiveRetentionRule
{
    /**
     * The concerned archive's identifier
     *
     * @var id
     */
    public $archiveId;

    /**
     * The status
     *
     * @var string
     */
    public $status = "preserved";

    /**
     * The starting date of the retention rule calculation 
     *
     * @var date
     */
    public $retentionStartDate;

    /**
     * The duration of retention 
     *
     * @var duration
     */
    public $retentionDuration;

    /**
     * The action to execute when the retention rule is over 
     *
     * @var string
     */
    public $finalDisposition;

    /**
     * Disposal date
     *
     * @var date
     */
    public $disposalDate;

}