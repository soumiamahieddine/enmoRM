<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle user.
 *
 * Bundle user is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle user is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle user.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\app;
/**
 * Interface for authentication
 * 
 * @access public
 */
interface authenticationInterface
{
    /**
     * authenticate a user
     * @param string $userName  The user name
     * @param string $password  The user password
     * @param string $actionUri The requested action
     * 
     * @return auth/authentication/login
     * @uses auth/authentication/createUserlogin
     */
    public function createUserLogin($userName, $password, $actionUri);

    /**
     * Authenticate a user
     *
     * @return auth/authentication/prompt
     */
    public function readUserPrompt();

    /**
     * Log off a user
     *
     * @return auth/authentication/logout
     * @uses auth/authentication/deleteUserlogin
     * 
     */
    public function readUserLogout();

    /**
     * Get form to edit user information
     * @param object $passwordInformation
     * 
     * @return auth/authentication/definePassword
     * @uses auth/authentication/update_userName_Password
     */
    public function updateUser_userName_Password($passwordInformation);

     /**
     * Generate a new password
     * @param string $username The username
     * @param string $email    The email of the user
     *
     * @uses auth/userAccount/updateGeneratepassword
     * @return auth/user/generatePassword
     */
    public function updateUserGeneratepassword($username, $email);
}