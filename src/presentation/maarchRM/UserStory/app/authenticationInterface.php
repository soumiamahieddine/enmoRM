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
     * Generate reset token
     * @param string $username The username
     * @param string $email    The email of the user
     *
     * @uses auth/userAccount/updateForgotaccount
     * @return auth/user/forgotaccount
     */
    public function updateUserGenerateresettoken($username, $email);

     /**
     * Get form to reset the password
     * @param string $token The token
     *
     * @return auth/user/formChangePassword
     */
    public function readUserChangepassword($token);

     /**
     * Reset the password
     * @param string $newPassword The new password
     * @param string $token       The token
     *
     * @uses auth/userAccount/updateResetpassword
     * @return auth/user/resetPassword
     */
    public function updateUserResetpassword($newPassword, $token);

    /**
     * Get form to edit user information
     * @param object $userName    The user name
     * @param object $oldPassword The old password
     * @param string $newPassword The new password
     * @param object $requestPath The request path
     *
     * @return auth/authentication/definePassword
     * @uses auth/authentication/updatePassword
     */
    public function updateUserPassword($userName, $oldPassword, $newPassword, $requestPath);
}