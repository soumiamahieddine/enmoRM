<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle contact.
 *
 * Bundle contact is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle contact is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle contact.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\contact\Message;

/**
 * Communications of contacts
 *
 * @package Contact
 */
class communication
{
    /**
     * The communication identifier
     *
     * @var id
     */
    public $communicationId;

    /**
     * The communication purpose
     *
     * @var string
     * @notempty
     */
    public $purpose;

    /**
     * The communication mean code
     *
     * @var string
     * @notempty
     */
    public $comMeanCode;

    /**
     * The communication value
     *
     * @var string
     */
    public $value;

    /**
     * Some more info
     *
     * @var string
     */
    public $info;
}
