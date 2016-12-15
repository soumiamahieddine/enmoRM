<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle user.
 *
 * Bundle user is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle user is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle user.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\auth\Model;
/**
 * User login update
 * 
 * @package Auth
 * 
 */
final class userLogin
{
    /**
     * @var id
     */
    public $accountId;

    /**
     * @var boolean
     */
    public $locked = false;

    /**
     * @var timestamp
     */
    public $lockDate;

    /**
     * @var int
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

}
