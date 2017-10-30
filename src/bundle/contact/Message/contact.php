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
 * Contact
 *
 * @package Contact
 */
class contact
{

    /**
     * The contact's identifier
     *
     * @var id
     */
    public $contactId;

    /**
     * The contact's identifier
     *
     * @var string
     * @enumeration [person, organization]
     */
    public $contactType;

    /**
     * The contact displayed name
     *
     * @notempty
     * @var string
     */
    public $displayName;

    /**
     * The contact organization name
     *
     * @var string
     */
    public $orgName;

    /**
     * The contact first name if person
     *
     * @var string
     */
    public $firstName;

    /**
     * The contact last name if person
     *
     * @var string
     */
    public $lastName;

    /**
     * The contact title if person
     *
     * @var string
     */
    public $title;

    /**
     * The contact function
     *
     * @var string
     */
    public $function;

    /**
     * The contact service
     *
     * @var string
     */
    public $service;

    /**
     * The contact addresses
     *
     * @var contact/address[]
     */
    public $address;

    /**
     * The contact communication
     *
     * @var contact/communication[]
     */
    public $communication;
}
