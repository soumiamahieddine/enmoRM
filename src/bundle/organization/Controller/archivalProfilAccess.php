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
namespace bundle\organization\Controller;

/**
 * Control of the organization
 *
 * @package Organization
 * @author  Prosper De Laure <prosper.delaure@maarch.org> 
 */
class archivalProfilAccess
{
	protected $sdoFactory;

    /**
     * Constructor
     * @param object $sdoFactory The model for organization
     *
     * @return void
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        
        $this->sdoFactory = $sdoFactory;

    }

    /**
     * get children archival profile access
     * @param string $orgId The organization id
     *
     * @return void
     */
    public function getOrgProfilAccess($orgId)
    {

    	return $this->sdoFactory->find('organization/archivalProfilAccess', "orgId = '$orgId'");
    }

    /**
     * get children archival profile access
     * @param organization/archivalProfileAccess $profilAccess The profil access
     *
     * @return void
     */
    public function getOrgProfilAccess($profilAccess)
    {
        
    }

}
