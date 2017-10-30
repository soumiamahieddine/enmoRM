<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle auth.
 *
 * Bundle auth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle auth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle auth.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\auth\Model;
/**
 * auth userAccount definition
 *
 * @package Auth
 *
 * @pkey [accountId]
 * @key [accountName]
 */
class account
{
    /**
     * The account identifier
     *
     * @var id
     * @notempty
     */
    public $accountId;

    /**
     * @var string
     * @notempty
     */
    public $accountName;

    /**
     * The displayed name
     *
     * @var string
     * @notempty
     */
    public $displayName;

    /**
     * @var string
     */
    public $emailAddress;

    /**
     * @var string
     * @enumeration [user, service]
     */
    public $accountType;

    /**
     * @var bool
     */
    public $enabled = true;

    /* **********************************************
        USER ACCOUNT PROPERTIES
    *********************************************** */
    /**
     * @var string
     */
    public $password;

    /**
     * @var timestamp
     */
    public $passwordLastChange;

    /**
     * @var bool
     */
    public $passwordChangeRequired = false;

    /**
     * @var bool
     */
    public $locked = false;

    /**
     * @var timestamp
     */
    public $lockDate;   

    /**
     * @var integer
     */
    public $badPasswordCount = 0;

    /**
     * @var timestamp
     */
    public $lastLogin;

    /**
     * @var string
     */
    public $lastIp;

    /**
     * @var id
     */
    public $replacingUserAccountId;

    /**
     * The user fisrt name (given name)
     *
     * @var string
     */
    public $firstName;

    /**
     * The user last name (family name, surname)
     *
     * @var string
     */
    public $lastName;

    /**
     * The person title (civility)
     *
     * @var string
     */
    public $title;

    /* **********************************************
        SERVICE ACCOUNT PROPERTIES
    *********************************************** */
    /**
     * @var string
     */
    public $salt;

    /**
     * @var timestamp
     */
    public $tokenDate;
}
