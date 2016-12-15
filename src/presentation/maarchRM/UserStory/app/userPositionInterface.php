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
namespace presentation\maarchRM\UserStory\app;
/**
 * User position in org
 * 
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface userPositionInterface
{

    /**
     * Set the user current org unit
     *
     * @return organization/userPosition/setCurrentPosition
     * 
     * @uses organization/userPosition/updateCurrent_orgId_
     */
    public function updateCurrentposition_orgId_();


    /**
     * Get the user orgs
     *
     * @return organization/userPosition/getMyPositions
     * 
     * @uses organization/userPosition/read
     */
    public function readOrganizationPositions();
}
