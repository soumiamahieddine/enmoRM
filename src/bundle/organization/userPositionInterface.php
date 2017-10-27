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
namespace bundle\organization;
/**
 * Interface for user positions
 * 
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface userPositionInterface
{

    /**
     * Set the user current org unit
     *
     * @action organization/userPosition/setCurrentPosition
     */
    public function updateCurrent_orgId_();

    /**
     * Get the user current org unit
     *
     * @action organization/userPosition/getMyPositions
     */
    public function read();

    /**
     * Get my current organization tree
     *
     * @action organization/userPosition/getCurrentOrgTree
     */
    public function readGetcurrentorgtree();

    /**
     * Get the user org ids
     *
     * @action organization/userPosition/listMyOrgs
     */
    public function readOrgs();

    /**
     * Get the user org ids
     *
     * @action organization/userPosition/listMyServices
     */
    public function readServices();

    /**
     * Get the user descendant services orgRegNumber
     *
     * @action organization/userPosition/listMyCurrentDescendantServices
     */
    public function readDescendantservices();

    /**
     * Get the user descendant org orgRegNumber
     *
     * @action organization/userPosition/listMyCurrentDescendantOrgs
     */
    public function readDescendantorgs();

    /**
     * Get descendant profiles
     * 
     * @action organization/userPosition/getdescendantArchivalProfiles
     */
    public function readDescendantprofiles();

}
