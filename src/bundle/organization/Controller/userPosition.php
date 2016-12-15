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
 * Control of the organization types
 *
 * @package Organization
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org> 
 */
class userPosition extends abstractPosition
{
    /**
     * Get user postions list
     *
     * @return array The list of positions
     */
    protected function listPositions()
    {
        $accountToken = \laabs::getToken('AUTH');
        $currentOrg = \laabs::getToken('ORGANIZATION');

        if (!$accountToken) {
            return array();
        }

        return $this->sdoFactory->find('organization/userPosition', "userAccountId = '".$accountToken->accountId."'");
    }
}
