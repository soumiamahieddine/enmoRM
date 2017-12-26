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

namespace bundle\auth\Controller;

/**
 * userAccount  controller
 *
 * @package Auth
 * @author  Alexandre Morin <alexandre.morin@maarch.org>
 */
class userAccount
{

    protected $sdoFactory;
    protected $passwordEncryption;
    protected $securityPolicy;
    protected $adminUsers;
    protected $currentAccount;
    protected $accountPrivileges;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory         The dependency Sdo Factory object
     * @param string                  $passwordEncryption The password encryption algorythm
     * @param array                   $securityPolicy     The array of security policy parameters
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory = null, $passwordEncryption = 'md5', $securityPolicy = [], $adminUsers = [])
    {
        $this->sdoFactory = $sdoFactory;
        $this->passwordEncryption = $passwordEncryption;
        $this->securityPolicy = $securityPolicy;
        $this->adminUsers = $adminUsers;
        $this->currentAccount = \laabs::getToken('AUTH');
    }

    /**
     * List all users to display
     * @param string $query
     *
     * @return array The array of stdClass with dislpay name and user identifier
     */
    public function index($query = null)
    {
        if ($query) {
            $query .= "AND accountType='user'";
        } else {
            $query .= "accountType='user'";
        }

        $userAccounts = $this->sdoFactory->find('auth/account', $query);
        $userAccounts = \laabs::castMessageCollection($userAccounts, 'auth/userAccountIndex');

        return $userAccounts;
    }

    /**
     * List all users to display
     * @param string $query
     *
     * @return array The array of stdClass
     */
    public function userList($query = null)
    {
        $accountId = \laabs::getToken("AUTH")->accountId;

        $queryAssert = [];
        $queryAssert[] = "accountType='user'";

        if ($query) {
            $queryAssert[] = "$query";
        }

        $account = $this->sdoFactory->read("auth/account", array("accountId" => $accountId));

        if (!empty($this->adminUsers) && !in_array($account->accountName, $this->adminUsers)) {
            $queryAssert[] = "accountId!=['".\laabs\implode("','", $this->adminUsers)."']";
        }

        $userAccounts = $this->sdoFactory->find('auth/account', \laabs\implode(" AND ", $queryAssert));

        return $userAccounts;
    }

    /**
     * List all users to display
     * @param string $query
     *
     * @return array The array of stdClass with dislpay name and user identifier
     */
    public function search($query = null)
    {
        if ($query) {
            $query .= "AND accountType='user'";
        } else {
            $query .= "accountType='user'";
        }

        $userAccounts = $this->sdoFactory->find('auth/account', $query);

        return $userAccounts;
    }

    /**
     * Prepare an empty user object
     *
     * @return auth/account The user object
     */
    public function newUser()
    {
        return \laabs::newInstance('auth/account');
    }

    /**
     * Record a new user & role members
     * @param auth/account $userAccount The user object
     *
     * @return string The user identifier
     */
    public function add($userAccount)
    {
        $userAccountId = $this->addUserAccount($userAccount);

        if (is_array($userAccount->roles) && !empty($userAccount->roles[0])) {
            foreach ($userAccount->roles as $roleId) {
                \laabs::callService("auth/roleMember/create", $roleId, $userAccountId);
            }
        }

        return $userAccountId;
    }

    /**
     * Record a new user account
     * @param auth/account $userAccount The user object
     *
     * @throws \bundle\auth\Exception\userAlreadyExistException
     * @throws \bundle\auth\Exception\invalidUserInformationException
     *
     * @return string The user identifier
     */
    public function addUserAccount($userAccount)
    {

        $organizations = $userAccount->organizations;
        $userAccount = \laabs::cast($userAccount, "auth/account");
        $userAccount->accountId = \laabs::newId();
        $userAccount->accountType = 'user';

        if(!$organizations) {
            throw \laabs::newException('auth/noOrganizationException', "No organization chosen");
        }

        if ($this->sdoFactory->exists('auth/account', array('accountName' => $userAccount->accountName))) {
            throw \laabs::newException("auth/userAlreadyExistException","User already exist");
        }

        if (!\laabs::validate($userAccount, 'auth/account')) {
            $validationErrors = \laabs::getValidationErrors();
            throw \laabs::newException("auth/invalidUserInformationException", $validationErrors);
        }

        $encryptedPassword = $userAccount->password;
        if ($this->passwordEncryption != null) {
            $encryptedPassword = hash($this->passwordEncryption, $userAccount->password);
        }

        $userAccount->password = $encryptedPassword;
        $userAccount->passwordChangeRequired = true;
        $userAccount->passwordLastChange = \laabs::newTimestamp();

        $userAccount->badPasswordCount = 0;
        $userAccount->lastLogin = null;
        $userAccount->lastIp = null;

        $this->sdoFactory->create($userAccount, 'auth/account');
        $organizationController = \laabs::newController("organization/organization");

        foreach ($organizations as $orgId){
            $organizationController->addUserPosition($userAccount->accountId ,$orgId);
        }

        return $userAccount->accountId;
    }

    /**
     * Get a user object
     * @param id $userAccountId The user unique identifier
     *
     * @return auth/account The user object
     */
    public function get($userAccountId)
    {
        return $this->sdoFactory->read('auth/account', $userAccountId);
    }

    /**
     * Prepare a user object for update
     * @param id $userAccountId The user unique identifier
     *
     * @return auth/account The user object
     */
    public function edit($userAccountId)
    {
        $userAccount = $this->sdoFactory->read('auth/account', $userAccountId);
        $roleMembers = $this->sdoFactory->find("auth/roleMember", "userAccountId='$userAccountId'");
        $userAccount = \laabs::castMessage($userAccount, 'auth/userAccount');

        if (empty($roleMembers)) {
            return $userAccount;
        }

        foreach ($roleMembers as $roleMember) {
            $role = \laabs::callService('auth/role/read_roleId_', $roleMember->roleId);
            $userRole = \laabs::newMessage('auth/userRole');
            $userRole->roleId = $role->roleId;
            $userRole->roleName = $role->roleName;

            $userAccount->roles[] = $userRole;
        }

        return $userAccount;
    }

    /**
     * Prepare a user object for update profile
     *
     * @return auth/account The user object
     */
    public function editProfile()
    {
        $accountToken = \laabs::getToken('AUTH');

        return $this->edit($accountToken->accountId);
    }

    /**
     * Modify a  user & role members
     * @param string       $userAccountId The user account id
     * @param auth/account $userAccount   The user object
     */
    public function update($userAccountId, $userAccount)
    {
        $userAccount = $this->updateUserInformation($userAccount);

        $this->sdoFactory->deleteChildren("auth/roleMember", $userAccount, "auth/account");

        if (is_array($userAccount->roles) && !empty($userAccount->roles)) {
            foreach ($userAccount->roles as $roleId) {
                if ($roleId == null) {
                    continue;
                }

                \laabs::callService("auth/roleMember/create", $roleId, $userAccount->accountId);
            }
        }
    }

    /**
     * Modify userAccount information
     * @param auth/accountInformation $userAccount The user object
     *
     * @throws \bundle\auth\Exception\unknownUserException
     *
     * @return boolean The result of the request
     */
    public function updateUserInformation($userAccount = null)
    {
        if (!$this->sdoFactory->exists('auth/account', array('accountId' => $userAccount->accountId))) {
            throw \laabs::newException("auth/unknownUserException");
        }

        $allowUserModification = true;
        if (isset(\laabs::configuration('auth')['allowUserModification'])) {
            $allowUserModification = (bool) \laabs::configuration('auth')['allowUserModification'];
        }

        if (!$allowUserModification) {
            $user = $this->sdoFactory->read('auth/account', $userAccount->accountId);
            $userAccount->displayName = $user->displayName;
            $userAccount->lastName = $user->lastName;
            $userAccount->firstName = $user->firstName;
            $userAccount->title = $user->title;
            $userAccount->emailAddress = $user->emailAddress;
        }

        if(isset($userAccount->modificationRight)) {
            if($userAccount->organizations == null) {
                throw new \core\Exception\BadRequestException('No service chosen', 400);
            }

            $organizationController = \laabs::newController("organization/organization");
            $organizationController->updateUserPosition($userAccount->accountId,$userAccount->organizations );
        }


        $this->sdoFactory->update($userAccount, "auth/account");

        return $userAccount;
    }

    /**
     * get user account information
     * @param id $userAccountId The user account identifier
     *
     * @return auth/accountInformation $userAccount User account information object
     */
    public function getUserAccountInformation($userAccountId)
    {
        $userAccount = $this->sdoFactory->read("auth/accountInformation", $userAccountId);

        return $userAccount;
    }

    /**
     * Change a user password
     * @param string $userAccountId The identifier of the user
     * @param string $newPassword   The new password
     *
     * @return boolean The result of the request
     */
    public function setPassword($userAccountId, $newPassword)
    {
        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);

        $userAuthenticationController = \laabs::newController("auth/userAuthentication");
        $userAuthenticationController->checkPasswordPolicies($newPassword);

        $encryptedPassword = $newPassword;
        if ($this->passwordEncryption != null) {
            $encryptedPassword = hash($this->passwordEncryption, $newPassword);
        }

        $userAccount->password = $encryptedPassword;
        $userAccount->accountId = $userAccountId;
        $userAccount->passwordLastChange = \laabs::newDateTime();

        return $this->sdoFactory->update($userAccount);
    }

    /**
     * Genrate a new password
     * @param string $username The username
     * @param string $email    The email of the user
     *
     * @throws \bundle\auth\Exception\authenticationException
     *
     * @return boolean The result of the request
     */
    public function generatePassword($username, $email)
    {
        if (!$this->sdoFactory->exists("auth/account", array("accountName" => $username))) {
             throw \laabs::newException('auth/authenticationException', 'Invalid username or email.', 401);
        }

        $userAccount = $this->sdoFactory->read("auth/account", array("accountName" => $username));

        /*if ($userAccount->locked == true) {
            if (!isset($this->securityPolicy['lockDelay']) // No delay while locked
                || $this->securityPolicy['lockDelay'] == 0 // Unlimited delay
                || !isset($userAccount->lockDate)          // Delay but no date for lock so unlimited
                || \laabs::newTimestamp()->diff($userAccount->lockDate)->s < $this->securityPolicy['lockDelay'] // Date + delay upper than current date
            ) {
                throw \laabs::newException('auth/authenticationException', 'User %1$s is locked', 403, null, array($username));
            }
        }

        if ($userAccount->enabled != true) {
            throw \laabs::newException('auth/authenticationException', 'User %1$s is disabled', 403, null, array($userName));
        }*/

        if ($email != $userAccount->emailAddress) {
            throw \laabs::newException('auth/authenticationException', 'Invalid username or email.', 401);
        }

        $newPassword = \laabs::newId();
        $this->setPassword($userAccount->accountId, $newPassword);
        $this->requirePasswordChange($userAccount->accountId);

        $title = "Maarch RM - user information";
        $message = 'Your password has been reset. Your new password is  %1$s';
        
        if (!empty($this->securityPolicy["newPasswordValidity"]) && $this->securityPolicy["newPasswordValidity"] != 0) {
            $message .= '  and you have %2$d hour to change it';
            $message = sprintf($message, $newPassword, $this->securityPolicy["newPasswordValidity"]);
        } else {
            $message = sprintf($message, $newPassword);
        }

        $notificationDependency = \laabs::newService("dependency/notification/Notification");
        $result = $notificationDependency->send($title, $message, array($userAccount->emailAddress));

        return $result;
    }

    /**
     * Required password change
     * @param string $userAccountId The identifier of the user
     *
     * @return boolean The result of the request
     */
    public function requirePasswordChange($userAccountId)
    {
        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);
        $userAccount->badPassword = 0;
        $userAccount->passwordChangeRequired = true;

        return $this->sdoFactory->update($userAccount);
    }

    /**
     * Lock a user
     * @param string $userAccountId The identifier of the user
     * @param bool   $setLockDate   Set true to set the lock date
     *
     * @return boolean The result of the request
     */
    public function lock($userAccountId, $setLockDate = false)
    {
        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);
        $userAccount->locked = true;

        if ($setLockDate) {
            $userAccount->lockDate = \laabs::newTimestamp();
        }

        return $this->sdoFactory->update($userAccount);
    }

    /**
     * Unlock a user
     * @param string $userAccountId The identifier of the user
     *
     * @return boolean The result of the request
     */
    public function unlock($userAccountId)
    {
        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);
        $userAccount->locked = false;

        return $this->sdoFactory->update($userAccount);
    }

    /**
     * Enable a user
     * @param string $userAccountId The identifier of the user
     *
     * @return boolean The result of the request
     */
    public function enable($userAccountId)
    {
        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);
        $userAccount->enabled = true;
        $userAccount->replacingUserAccountId = null;

        return $this->sdoFactory->update($userAccount);
    }

    /**
     * Disable a user
     * @param string $userAccountId          The identifier of the user
     * @param string $replacingUserAccountId The identifier of the replacing user
     *
     * @return boolean The result of the request
     */
    public function disable($userAccountId, $replacingUserAccountId)
    {
        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);
        $userAccount->enabled = false;
        $userAccount->replacingUserAccountId = $replacingUserAccountId;

        return $this->sdoFactory->update($userAccount);
    }

    /**
     * Get list of user story
     * @param string $userAccountId The identifier of the user account
     *
     * @return array The user story names
     */
    public function getPrivilege($userAccountId)
    {
        $userAccountId = (string) $userAccountId;
        if (!isset($this->accountPrivileges[$userAccountId])) {

            $roleMemberController = \laabs::newController("auth/roleMember");
            $roles = $roleMemberController->readByUseraccount($userAccountId);

            $userStories = \laabs::configuration('auth')['publicUserStory'];

            foreach ($roles as $role) {
                $privileges = $this->sdoFactory->find("auth/privilege", "roleId='$role->roleId'");

                foreach ($privileges as $privilege) {
                    $userStories[] = $privilege->userStory;
                }
            }

            $this->accountPrivileges[$userAccountId] = array_unique($userStories);
        }

        return $this->accountPrivileges[$userAccountId];
    }

    /**
     * Check the user account has a privilege
     * @param string $userStory The user story name
     *
     * @return boolean
     */
    public function hasPrivilege($userStory)
    {
        $accountToken = $this->currentAccount;

        if (!$accountToken) {
            return \laabs::configuration('auth')['publicUserStory'];
        }

        $userPrivileges = $this->getPrivilege($accountToken->accountId);
        foreach ($userPrivileges as $userPrivilege) {
            if (fnmatch($userPrivilege, $userStory)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get user positions
     * @param string $accountId The user identifier
     *
     * @return organization/userPositionTree[] The list of user position
     */
    public function readUserPositions($accountId)
    {
        $users = $this->sdoFactory->find("organization/userPosition", "accountId = '$accountId'");
        $users = \laabs::castMessageCollection($users, 'organization/userPositionTree');

        foreach ($users as $user) {
            $user->displayName = $this->sdoFactory->read("organization/organization", $user->orgId)->displayName;
        }

        return $users;
    }

    /**
     * Search user account
     * @param string $query The query
     *
     * @return array The list of founded users
     */
    public function queryUserAccounts($query = "")
    {
        $queryTokens = \laabs\explode(" ", $query);
        $queryTokens = array_unique($queryTokens);

        $userAccountQueryProperties = array("displayName");
        $userAccountQueryPredicats = array();
        foreach ($userAccountQueryProperties as $userAccountQueryProperty) {
            foreach ($queryTokens as $queryToken) {
                $userAccountQueryPredicats[] = $userAccountQueryProperty."="."'*".$queryToken."*'";
            }
        }
        $userAccountQueryString = implode(" OR ", $userAccountQueryPredicats);
        if (!$userAccountQueryString) {
            $userAccountQueryString = "1=1";
        }
        $userAccountQueryString .= "(".$userAccountQueryString.") AND accountType='user'";

        $userAccounts = $this->sdoFactory->find('auth/account', $userAccountQueryString);

        return $userAccounts;
    }
}
