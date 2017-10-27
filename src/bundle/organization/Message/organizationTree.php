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
namespace bundle\organization\Message;
/**
 * Organization tree
 *
 * @package Organization
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org> 
 */
class organizationTree
{
    /**
     * The organization's identifier
     *
     * @var id
     */
    public $orgId;

    /**
     * The displayed name of the object
     *
     * @var string
     * @notempty
     */
    public $displayName;
    
    /**
     * The registration number
     *
     * @var string
     * @notempty
     */
    public $registrationNumber;

    /**
     * The organization's parent orgId
     *
     * @var string
     */
    public $parentOrgId;

    /**
     * The organization owner orgId
     *
     * @var string
     */
    public $ownerOrgId;

    /**
     * The organization is a service
     *
     * @var bool
     */
    public $isOrgUnit;

    /**
     * The children organizations
     *
     * @var organization/organization[]
     */
    public $organization;

    /**
     * The users list attached to the organization
     *
     * @var organization/userPositionList[]
     */
    public $userPosition;

    /**
     * The service list attached to the organization
     *
     * @var organization/servicePositionList[]
     */
    public $servicePosition;

}