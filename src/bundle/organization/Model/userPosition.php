<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\organization\Model;

/**
 * Model of the userPosition
 *
 * @package Organization
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org> 
 *
 * @pkey [userAccountId, orgId]
 * @fkey [orgId] organization/organization [orgId]
 */
class userPosition
{
    /**
     * The user account of the person
     *
     * @var id
     */
    public $userAccountId;

    /**
     * The organization's identifier
     *
     * @var id
     */
    public $orgId;

    /**
     * The person's function
     *
     * @var string
     */
    public $function;
    
    /**
     * The user's default organization
     *
     * @var boolean
     */
    public $default;

}
