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
 * sservice privilege definition
 *
 * @package auth
 *
 * @key [accountId, serviceURI]
 * @fkey [accountId] auth/account [accountId]
 */
class servicePrivilege
{
    /**
     * The account identifier
     *
     * @var string
     */
    public $accountId;

    /**
     * The service URI
     *
     * @var string
     */
    public $serviceURI;
}
