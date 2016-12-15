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
 * Interface for service positions
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface servicePositionInterface
{

    /**
     * Set the user current org unit
     *
     * @action organization/servicePosition/setCurrentPosition
     */
    public function updateCurrent_orgId_();

    /**
     * Get the user current org unit
     *
     * @action organization/servicePosition/getMyPositions
     */
    public function read();

    /**
     * Get the user current org unit
     *
     * @action organization/servicePosition/getPosition
     */
    public function read_serviceAccountId_();

    /**
     * Get the user org ids
     *
     * @action organization/servicePosition/listMyOrgs
     */
    public function readOrgs();

    /**
     * Get the user org ids
     *
     * @action organization/servicePosition/listMyServices
     */
    public function readServices();
}
