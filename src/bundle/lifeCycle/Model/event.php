<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle lifeCycle.
 *
 * Bundle lifeCycle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle lifeCycle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle lifeCycle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\lifeCycle\Model;

/**
 * Class model that represents an event of a journal
 *
 * @package lifeCycle
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 *
 * @pkey [eventId]
 */
class event
{
    /**
     * The universal identifier
     *
     * @var id
     */
    public $eventId;

    /**
     * The name of the event type
     *
     * @var string
     * @enumeration [deposit, compliance, modification, disposal, restitution, profileAdded, profileModified, profileDeleted]
     */
    public $eventType;

    /**
     * The application instance name
     *
     * @var string
     */
    public $instanceName;

    /**
     * The timestamp of the event
     *
     * @var timestamp
     */
    public $timestamp;

    /**
     * The user organization registration number
     *
     * @var string
     */
    public $orgRegNumber;

    /**
     * The user orgUnit registration number
     *
     * @var string
     */
    public $orgUnitRegNumber;

    /**
     * The account identifier
     *
     * @var id
     */
    public $accountId;

    /**
     * The class of the aimed object
     *
     * @var string
     */
    public $objectClass;

    /**
     * The identifier of the aimed object
     *
     * @var id
     */
    public $objectId;

    /**
     * The operation result
     *
     * @var bool
     */
    public $operationResult;

    /**
     * The description of the event
     *
     * @var string
     */
    public $description;

    /**
     * The event extra information
     *
     * @var string
     */
    public $eventInfo;
}
