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
 * Address of contacts 
 *
 * @package Contact
 */
class address
{
    /**
     * The address identifier
     *
     * @var id
     */
    public $addressId;

    /**
     * The address purpose
     *
     * @var string
     * @notempty
     */
    public $purpose;

    /**
     * The address room identification
     *
     * @var string
     */
    public $room;

    /**
     * The address floor identification
     *
     * @var string
     */
    public $floor;

    /**
     * The address building identification
     *
     * @var string
     */
    public $building;

    /**
     * The address number
     *
     * @var string
     */
    public $number;

    /**
     * The address street
     *
     * @var string
     */
    public $street;

    /**
     * The address postBox
     *
     * @var string
     */
    public $postBox;

    /**
     * The address block identification
     *
     * @var string
     */
    public $block;

    /**
     * The address city sub-division
     *
     * @var string
     */
    public $citySubDivision;

    /**
     * The address post code (zip code)
     *
     * @var string
     */
    public $postCode;

    /**
     * The address city name
     *
     * @var string
     */
    public $city;  

    /**
     * The address country name
     *
     * @var string
     */
    public $country;
}