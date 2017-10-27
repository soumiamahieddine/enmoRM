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
namespace bundle\contact\Model;
/**
 * Address of contacts 
 *
 * @package Contact
 *
 * @pkey [addressId]
 * @key [contactId, purpose]
 * @fkey [contactId] contact/contact[contactId]
 * 
 * @xmlns contact maarch.org:laabs:contact
 */
class address
{
    /**
     * The address identifier
     *
     * @var id
     * @notempty
     */
    public $addressId;

    /**
     * The address contact identifier
     *
     * @var id
     * @notempty
     */
    public $contactId;

    /**
     * The address purpose
     *
     * @var string
     * @notempty
     * @xpath contact:Purpose
     */
    public $purpose;

    /**
     * The address room identification
     *
     * @var string
     * @xpath contact:Room
     */
    public $room;

    /**
     * The address floor identification
     *
     * @var string
     * @xpath contact:Floor
     */
    public $floor;

    /**
     * The address building identification
     *
     * @var string
     * @xpath contact:Building
     */
    public $building;

    /**
     * The address number
     *
     * @var string
     * @xpath contact:Number
     */
    public $number;

    /**
     * The address street
     *
     * @var string
     * @xpath contact:Street
     */
    public $street;

    /**
     * The address postBox
     *
     * @var string
     * @xpath contact:PostBox
     */
    public $postBox;

    /**
     * The address block identification
     *
     * @var string
     * @xpath contact:Block
     */
    public $block;

    /**
     * The address city sub-division
     *
     * @var string
     * @xpath contact:CitySubDivision
     */
    public $citySubDivision;

    /**
     * The address post code (zip code)
     *
     * @var string
     * @xpath contact:PostCode
     */
    public $postCode;

    /**
     * The address city name
     *
     * @var string
     * @xpath contact:CityName
     */
    public $city;  

    /**
     * The address country name
     *
     * @var string
     * @xpath contact:Country
     */
    public $country;
}