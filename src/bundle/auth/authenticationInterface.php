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

namespace bundle\auth;

/**
 * Interface for userAccount authentication
 */
interface authenticationInterface
{
    
    /**
     * Authenticate a user account
     * @param string $userName
     * @param string $password
     * 
     * @action auth/userAuthentication/login
     */
    public function createUserlogin($userName, $password);

    /**
     * Log off the user account
     * 
     * @action auth/userAuthentication/logout
     */
    public function deleteUserlogin();

    /**
     * Change a user Account password
     * @param string $oldPassword The user's old password
     * @param string $newPassword The user's new password
     * @param string $requestPath The requested path
     *
     * @action auth/userAuthentication/definePassword
     */
    public function update_userName_Password($oldPassword, $newPassword, $requestPath);
}