<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\adminFunc;

/**
 * User story admin authorization
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface adminUseraccountInterface
{
    /**
     * List all users for administration
     *
     * @return auth/user/indexHtml
     */
    public function readUseraccounts();

    /**
     * List all users for administration
     *
     * @uses auth/userAccount/readUserlist
     * @return auth/user/indexDatatable
     */
    public function readUseraccountsList($query = null);

    /**
     * Prepare an empty user object
     *
     * @return auth/user/newUser
     *
     * @uses auth/userAccount/readNew
     */
    public function readUseraccount();

    /**
     * Record a new user account
     * @param auth/account $userAccount The user account object to record
     *
     * @uses auth/userAccount/create
     *
     * @return auth/user/addUser
     */
    public function createUseraccount($userAccount);

    /**
     * Prepare a user object for update
     *
     * @uses auth/userAccount/read_userAccountId_
     *
     * @return auth/user/edit
     */
    public function readUseraccount_userAccountId_();

    /**
     * Allow to modify user information
     * @param auth/roleMember[] $roleMembers Array of role member object
     *
     * @uses auth/userAccount/update_userAccountId_
     * @return auth/user/updateUserInformation
     */
    public function updateUseraccount_userAccountId_($roleMembers);

    /**
     * Disable a user
     *
     * @return auth/user/disable
     * @uses auth/userAccount/updateDisable_userAccountId_
     */
    public function updateUseraccount_userAccountId_Disable();

    /**
     * Enable a user
     *
     * @return auth/user/enable
     * @uses auth/userAccount/updateEnable_userAccountId_
     */
    public function updateUseraccount_userAccountId_Enable();

    /**
     * Lock a user
     *
     * @uses auth/userAccount/updateLock_userAccountId_
     * @return auth/user/lock
     */
    public function updateUseraccount_userAccountId_Lock();

    /**
     * Unlock a user
     *
     * @uses auth/userAccount/updateUnlock_userAccountId_
     * @return auth/user/unlock
     */
    public function updateUseraccount_userAccountId_Unlock();

    /**
     * Required password change
     *
     * @uses auth/userAccount/updatePasswordchangerequest_userAccountId_
     * @return auth/user/requirePasswordChange
     */
    public function updateUseraccount_userAccountId_Requirepasswordchange();


    /**
     * Get the list of available users
     *
     * @param string $securityLevel The security level
     *
     * @uses auth/userAccount/readQuery_query_
     */
    public function readUseraccounts_query_($securityLevel = null);

    /**
     * List all users to display
     *
     * @uses auth/userAccount/readUserlist
     */
    public function readUserTodisplay();
}