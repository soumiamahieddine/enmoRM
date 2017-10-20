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
 * Interface for userAccount
 */
interface userAccountInterface
{
    /**
     * List the user account information
     * @param string $query
     *
     * @action auth/userAccount/index
     */
    public function readIndex($query = null);

    /**
     * List the user account
     * @param string $query
     *
     * @action auth/userAccount/userList
     */
    public function readUserlist($query = null);

    /**
     * List the user account detail
     * @param string $query
     *
     * @action auth/userAccount/search
     */
    public function readSearch($query = null);

    /**
     * Search the user account for typehead
     * @param string $query The query string
     *
     * @action auth/userAccount/queryUserAccounts
     */
    public function readQuery_query_($query = null);

    /**
     * Prepare an empty user Account object
     *
     * @action auth/userAccount/newUser
     *
     */
    public function readNew();

    /**
     * Add a new user & role members
     * @param auth/newUserAccount $userAccount The new user Account information
     *
     * @action auth/userAccount/add
     */
    public function create($userAccount);

    /**
     * Add a new user account
     * @param auth/userAccount $userAccount The new user Account information
     *
     * @action auth/userAccount/addUserAccount
     */
    public function createUseraccount($userAccount);

    /**
     * Prepare a user Account object for update
     * @return auth/userAccount
     * 
     * @action auth/userAccount/edit
     *
     */
    public function read_userAccountId_();
    
    /**
     * Prepare a user Account object for update
     * @return auth/userAccount
     * 
     * @action auth/userAccount/editProfile
     *
     */
    public function readProfile();
    
    /**
     * Update my profile
     * @return auth/userAccount
     * 
     * @action auth/userAccount/updateUserInformation
     *
     */
    public function updateProfile($userAccount);

    /**
     * Get the user Account personal information
     * @param id $userAccountId
     *
     * @action auth/userAccount/getUserAccountInformation
     *
     */
    public function read_userAccountId_Information($userAccountId);

    /**
     * Allow to modify user Account & role memebers
     * @param auth/userAccountUpdate $userAccount The new user Account information
     *
     * @action auth/userAccount/update
     */
    public function update_userAccountId_($userAccount = null);

    /**
     * Get list of user story
     * @param string $userAccountId The user account identifier
     *
     * @action auth/userAccount/getPrivilege
     */
    public function read_userAccountId_Privileges($userAccountId);

    /**
     * Check if user account has privilege on the user story
     * @param qname $userStory The user story
     *
     * @action auth/userAccount/hasPrivilege
     */
    public function readHasprivilege($userStory);

    /**
     * Get list of user access rules of current user
     *
     * @action auth/userAccount/getAccessRule
     */
    public function readAccessrule_objectClass_();

    /**
     * Disable a user Account
     * @param string $replacingUserAccountId The replacing user acocount id
     *
     * @action auth/userAccount/disable
     */
    public function updateDisable_userAccountId_($replacingUserAccountId);

    /**
     * Enable a user Account
     *
     * @action auth/userAccount/enable
     */
    public function updateEnable_userAccountId_();

    /**
     * Lock a user Account
     *
     * @action auth/userAccount/lock
     */
    public function updateLock_userAccountId_();

    /**
     * Unlock a user Account
     *
     * @action auth/userAccount/unlock
     *
     */
    public function updateUnlock_userAccountId_();

    /**
     * Change a user Account password
     * @param string $newPassword
     *
     * @action auth/userAccount/setPassword
     */
    public function updatePassword_userAccountId_($newPassword);

    /**
     * Generate a new password
     * @param string $username The username
     * @param string $email    The email of the user
     *
     * @action auth/userAccount/generatePassword
     */
    public function updateGeneratePassword($username, $email);

    /**
     * Required password change
     *
     * @action auth/userAccount/requirePasswordChange
     */
    public function updatePasswordchangerequest_userAccountId_();
}
