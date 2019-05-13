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
 * Class model that represents a description field of the data dictionnary
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 */
class descriptionField
{
    /**
     * The name of the property
     *
     * @var string
     * @pattern #^[A-Za-z0-9_]*[A-Za-z0-9]$#
     * @notempty
     */
    public $name;

    /**
    * @var string The label for users
    * @notempty
    */
    public $label;

    /**
    * @var string The type iof data : name, string, integer, float, boolean, number, date, timestamp, datetime
    * @notempty
    */
    public $type;

    /**
    * @var string The default value
    */
    public $default;

    /**
    * @var integer
    */
    public $minLength;

    /**
    * @var integer
    */
    public $maxLength;

    /**
    * @var float
    */
    public $minValue;

    /**
    * @var float
    */
    public $maxValue;

    /**
    * @var string[]
    */
    public $enumeration;

    /**
     * @var json
    */
    public $facets;
    /**
    * @var string
    */
    public $pattern;

    /**
     * @var boolean
     */
    public $isArray;
}
