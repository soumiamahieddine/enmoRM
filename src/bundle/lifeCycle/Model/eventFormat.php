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
 * Class model that represents an event format
 *
 * @package lifeCycle
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 *
 * @pkey [type]
 */
class eventFormat
{
    /**
     * The type of event
     *
     * @var string
     * @notempty
     */
    public $type;

    /**
     * The list of event's column
     *
     * @var string
     * @notempty
     */
    public $format;

    /**
     * The message of event
     *
     * @var string
     * @notempty
     */
    public $message;

    /**
     * The list of event's column
     *
     * @var boolean
     * @notempty
     */
    public $notification;
}
