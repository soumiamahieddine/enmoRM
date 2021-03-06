<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle audit.
 *
 * Bundle audit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle audit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle audit.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\audit\Model;

/**
 * audit event definition
 *
 * @package Audit
 *
 * @pkey [eventId]
 *
 */
class event
{

    /**
     * @var id
     */
    public $eventId;

    /**
     * @var timestamp
     */
    public $eventDate;

    /**
     * The current user account if available
     * @var id
     */
    public $accountId;

    /**
     * The service path <bundle>/<api>/<path>
     * @var string
     */
    public $path;

    /**
     * The organization registration number
     * @var string
     */
    public $orgRegNumber;

    /**
     * The orgUnit registration number
     * @var string
     */
    public $orgUnitRegNumber;

    /**
     * The revealant input data
     * @var json
     */
    public $input;

    /**
     * Variables
     * @var json
     */
    public $variables;

    /**
     * The revealant output data
     * @var string
     */
    public $output;

    /**
     * The success or failure of requested action. Depends on the return (normal or budsiness exception)
     * @var boolean
     */
    public $status;

    /**
     * The information associated with the event: remote IP address, process id, name, system user id...
     * @var string
     */
    public $info;

    /**
     * The instance name
     * @var string
     */
    public $instanceName;
}
