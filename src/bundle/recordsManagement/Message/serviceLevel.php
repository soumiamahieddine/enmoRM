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
 * Class model that represents a service level
 *
 * @package RecordsManagement
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 */
class serviceLevel
{
    /**
     * The service level identifier
     *
     * @var id
     */
    public $serviceLevelId;

    /**
     * The service level reference
     *
     * @pattern #^[A-Za-z_][A-Za-z0-9_]*$#
     * @var string
     */
    public $reference;

    /**
     * The identifier of a cluster for digital ressource storage
     *
     * @var id
     */
    public $digitalResourceClusterId;

    /**
     * The control list of the service level : formatDetection, formatValidation, virusCheck
     *
     * @var string
     */
    public $control;

    /**
     * Default if not specified
     *
     * @var boolean
     */
    public $default;
}
