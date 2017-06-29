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

namespace bundle\auth\Observer;

/**
 * Service for authorization check observer
 *
 * @package Auth
 * @author  Maarch Cyril  VAZQUEZ <cyril.vazquez@maarch.org>
 */
class authorization
{
    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Check user privilege against requested route
     * @param \core\Reflection\Route &$servicePath The reflection of requested route
     * @param array                  &$args        The arguments
     * 
     * @return boolean
     *
     * @subject LAABS_SERVICE_PATH
     */
    public function checkPrivilege(&$servicePath, array &$args=null)
    {
        if (!\laabs::isServiceClient()) {
            return true;
        }
        $serviceName = strtolower($servicePath->getName());

        if ($accountToken = \laabs::getToken('AUTH')) {
            $account = $this->sdoFactory->read("auth/account", $accountToken->accountId);
            switch ($account->accountType) {
                case 'service':
                    $accountController = \laabs::newController('auth/serviceAccount');
                    $privileges = $accountController->getPrivileges($accountToken->accountId);

                    foreach ($privileges as $privilege) {
                        if (fnmatch(strtolower($privilege->serviceURI), $serviceName)) {
                            return true;
                        }
                    }
                    break;

                case 'user':
                    return true;
            }
        }

        throw \laabs::newException("auth/forbiddenException", "Forbidden", 403);
    }
}
