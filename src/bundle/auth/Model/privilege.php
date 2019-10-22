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
 * auth privilege definition
 *
 * @package auth
 *
 * @key [userStory, roleId]
 * @fkey [roleId] auth/role [roleId]
 */
class privilege
{
    /**
     * The user story route
     *
     * @var string
     */
    public $userStory;

    /**
     * The role identifier
     *
     * @var id
     */
    public $roleId;
}
