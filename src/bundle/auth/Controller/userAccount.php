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
    use ForgotAccountTrait;

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
    }

    /**
     * List all users to display
     *
     * @param integer $limit Max limit of info to return
     * @param string  $query sdo query
     *
     * @return array The array of stdClass with dislpay name and user identifier
     */
    public function index($limit = null, $query = null)
    {
        if ($query) {
            $query .= "AND accountType='user'";
        } else {
            $query .= "accountType='user'";
        }

        $userAccounts = $this->sdoFactory->find('auth/account', $query, null, null, null, $limit);
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
        $organizationController = \laabs::newController('organization/organization');
        $accountId = \laabs::getToken("AUTH")->accountId;

        $queryAssert = [];
        $queryAssert[] = "accountType='user'";

        if ($query) {
            $queryAssert[] = "$query";
        }

        $account = $this->sdoFactory->read("auth/account", array("accountId" => $accountId));
        $queryAssert[] = "accountId!=['".$accountId."']";

        if (!empty($this->adminUsers) && !in_array($account->accountName, $this->adminUsers)) {
            $queryAssert[] = "accountId!=['".\laabs\implode("','", $this->adminUsers)."']";
        }

        switch ($account->getSecurityLevel()) {
            case $account::SECLEVEL_GENADMIN:
                $queryAssert[] = "(isAdmin=TRUE AND ownerOrgId!=null)";
                break;

            case $account::SECLEVEL_FUNCADMIN:
                $organization = $this->sdoFactory->read('organization/organization', $account->ownerOrgId);
                $organizations = $organizationController->readDescendantOrg($organization->orgId);
                $organizations[] = $organization;
                $organizationsIds = [];
                foreach ($organizations as $key => $organization) {
                    $organizationsIds[] = (string) $organization->orgId;
                }

                $queryAssert[] = "((ownerOrgId= ['" .
                    implode("', '", $organizationsIds) .
                    "']) OR (isAdmin!=TRUE AND ownerOrgId=null))
                    ";
                break;

            case $account::SECLEVEL_USER:
                $queryAssert[] = "((isAdmin!=TRUE AND ownerOrgId='". $account->ownerOrgId."')";
                break;
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
                $roleMemberController = \laabs::newController('auth/roleMember');
                $roleMemberController->create($roleId, $userAccountId);
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
        $this->isAuthorized(['gen_admin', 'func_admin']);

        $organizations = $userAccount->organizations;
        $userAccount = \laabs::cast($userAccount, "auth/account");
        $userAccount->accountId = \laabs::newId();
        $userAccount->accountType = 'user';

        $accountToken = \laabs::getToken('AUTH');
        $account = $this->sdoFactory->read("auth/account", $accountToken->accountId);

        $securityLevel = $account->getSecurityLevel();
        if ($securityLevel == $account::SECLEVEL_GENADMIN) {
            if (!$userAccount->ownerOrgId || !$userAccount->isAdmin) {
                throw new \core\Exception\UnauthorizedException("You are not allowed to do this action");
            }
        } elseif ($securityLevel == $account::SECLEVEL_FUNCADMIN) {
            if (!$organizations || $userAccount->isAdmin) {
                throw new \core\Exception\UnauthorizedException("You are not allowed to do this action");
            }
        }

        $organizationController = \laabs::newController('organization/organization');
        if (!is_null($organizations)) {
            $organization = $organizationController->read($organizations[0]);
            $userAccount->ownerOrgId = $organization->ownerOrgId;
        }

        if ($userAccount->ownerOrgId) {
            try {
                $organizationController->read($userAccount->ownerOrgId);
            } catch (\Exception $e) {
                throw new \core\Exception\UnauthorizedException($userAccount->ownerOrgId . " does not exist.");
            }
        }

        if ($this->sdoFactory->exists('auth/account', array('accountName' => $userAccount->accountName))) {
            throw \laabs::newException("auth/userAlreadyExistException", "User already exist");
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

        if (!$userAccount->isAdmin) {
            foreach ($organizations as $orgId) {
                $organizationController->addUserPosition($userAccount->accountId, $orgId);
            }
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
        $userAccountModel = $this->sdoFactory->read('auth/account', $userAccountId);
        $roleMembers = $this->sdoFactory->find("auth/roleMember", "userAccountId='$userAccountId'");
        $userAccount = \laabs::castMessage($userAccountModel, 'auth/userAccount');

        $userAccount->securityLevel = $userAccountModel->getSecurityLevel();

        if (empty($roleMembers)) {
            return $userAccount;
        }

        foreach ($roleMembers as $roleMember) {
            $roleController = \laabs::newController('auth/role');
            $role = $roleController->edit($roleMember->roleId);
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
        $this->isAuthorized(['gen_admin', 'func_admin']);

        $accountToken = \laabs::getToken('AUTH');
        $account = $this->sdoFactory->read("auth/account", $accountToken->accountId);

        $securityLevel = $account->getSecurityLevel();
        if ($securityLevel == $account::SECLEVEL_GENADMIN) {
            if (!$userAccount->ownerOrgId || !$userAccount->isAdmin) {
                throw new \core\Exception\UnauthorizedException("You are not allowed to do this action");
            }
        } elseif ($securityLevel == $account::SECLEVEL_FUNCADMIN) {
            if (!$userAccount->organizations || $userAccount->isAdmin) {
                throw new \core\Exception\UnauthorizedException("You are not allowed to do this action");
            }
        }

        $organizationController = \laabs::newController('organization/organization');
        if (!is_null($userAccount->organizations)) {
            $organization = $organizationController->read($userAccount->organizations[0]);
            $userAccount->ownerOrgId = $organization->ownerOrgId;
        }

        if ($userAccount->ownerOrgId) {
            try {
                $organizationController->read($userAccount->ownerOrgId);
            } catch (\Exception $e) {
                throw new \core\Exception\UnauthorizedException($userAccount->ownerOrgId . " does not exist.");
            }
        }

        $userAccount = $this->updateUserInformation($userAccount);

        $oldUserAccount = $this->sdoFactory->read('auth/account', $userAccount->accountId);
        if ($oldUserAccount->ownerOrgId && $oldUserAccount->ownerOrgId != $userAccount->ownerOrgId ||
            !$oldUserAccount->ownerOrgId && $userAccount->ownerOrgId
        ) {
            throw new \core\Exception\UnauthorizedException("The owner org id cannot be modified");
        }

        $this->sdoFactory->deleteChildren("auth/roleMember", $userAccount, "auth/account");

        if (is_array($userAccount->roles) && !empty($userAccount->roles)) {
            foreach ($userAccount->roles as $roleId) {
                if ($roleId == null) {
                    continue;
                }

                $roleMemberController = \laabs::newController('auth/roleMember');
                $roleMemberController->create($roleId, $userAccount->accountId);
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

        if (!$userAccount->isAdmin) {
            if (isset($userAccount->modificationRight)) {
                if ($userAccount->organizations == null) {
                    throw new \core\Exception\BadRequestException('No service chosen', 400);
                }

                $organizationController = \laabs::newController("organization/organization");
                $organizationController->updateUserPosition($userAccount->accountId, $userAccount->organizations);
            }
        }

        $this->sdoFactory->update($userAccount, "auth/account");

        return $userAccount;
    }

    /**
     * Modify userAccount information
     * @param auth/ownAccountUpdate $userAccount The user object
     *
     * @throws \bundle\auth\Exception\unknownUserException
     *
     * @return boolean The result of the request
     */
    public function updateOwnUserInformation($userAccount)
    {
        $this->sdoFactory->update($userAccount, "auth/account", \laabs::getToken('AUTH')->accountId);

        return $userAccount;
    }

    /**
     * get user account information
     * @param id $userAccountId The user account identifier
     *
     * @return auth/userAccount $userAccount User account information object
     */
    public function getUserAccountInformation($userAccountId)
    {
        $userAccount = $this->sdoFactory->read("auth/userAccount", $userAccountId);

        return $userAccount;
    }

    /**
     * Change a user password
     * @param string $userAccountId The identifier of the user
     * @param string $newPassword   The new password
     * @param string $oldPassword          The old password
     *
     * @return boolean The result of the request
     */
    public function setPassword($userAccountId, $newPassword,$oldPassword)
    {
        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);
        $oldPasswordHash = hash($this->passwordEncryption, $oldPassword);

        if ($userAccount->password != $oldPasswordHash) {
            throw new \core\Exception\UnauthorizedException("User password error.");
        }

        if ($oldPassword == $newPassword) {
            throw new \core\Exception\ForbiddenException("The password is the same as the precedent.", 403);
        }

        return $this->updatePassword($userAccount, $newPassword);
    }

    protected function updatePassword($userAccount, $newPassword)
    {
        $userAuthenticationController = \laabs::newController("auth/userAuthentication");
        $userAuthenticationController->checkPasswordPolicies($newPassword);

        $encryptedPassword = $newPassword;
        if ($this->passwordEncryption != null) {
            $encryptedPassword = hash($this->passwordEncryption, $newPassword);
        }

        $userAccount->password = $encryptedPassword;
        $userAccount->passwordLastChange = \laabs::newTimestamp();
        $userAccount->badPasswordCount = 0;
        $userAccount->passwordChangeRequired = false;

        return $this->sdoFactory->update($userAccount);
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
        $this->isAuthorized(['gen_admin', 'func_admin']);

        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);

        $accountToken = \laabs::getToken('AUTH');
        $account = $this->sdoFactory->read("auth/account", $accountToken->accountId);

        $securityLevel = $account->getSecurityLevel();
        if ($securityLevel == $account::SECLEVEL_GENADMIN) {
            if (!$userAccount->ownerOrgId || !$userAccount->isAdmin) {
                throw new \core\Exception\UnauthorizedException("You are not allowed to do this action");
            }
        } elseif ($securityLevel == $account::SECLEVEL_FUNCADMIN) {
            if (!$userAccount->organizations || $userAccount->isAdmin) {
                throw new \core\Exception\UnauthorizedException("You are not allowed to do this action");
            }
        }

        $userAccount->enabled = true;
        $userAccount->replacingUserAccountId = null;

        return $this->sdoFactory->update($userAccount);
    }

    /**
     * Disable a user
     * @param string $userAccountId          The identifier of the user
     *
     * @return boolean The result of the request
     */
    public function disable($userAccountId)
    {
        $this->isAuthorized(['gen_admin', 'func_admin']);

        $userAccount = $this->sdoFactory->read("auth/account", $userAccountId);
        $accountToken = \laabs::getToken('AUTH');
        $account = $this->sdoFactory->read("auth/account", $accountToken->accountId);

        $securityLevel = $account->getSecurityLevel();
        if ($securityLevel == $account::SECLEVEL_GENADMIN) {
            if (!$userAccount->ownerOrgId || !$userAccount->isAdmin) {
                throw new \core\Exception\UnauthorizedException("You are not allowed to do this action");
            }
        } elseif ($securityLevel == $account::SECLEVEL_FUNCADMIN) {
            if (!$userAccount->organizations || $userAccount->isAdmin) {
                throw new \core\Exception\UnauthorizedException("You are not allowed to do this action");
            }
        }

        $userAccount->enabled = false;

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
     * @return boolean True if the user has the privilege, false if he doesn't
     */
    public function hasPrivilege($userStory)
    {
        if (!\laabs::presentation()->hasUserStory($userStory)) {
            return false;
        }

        $accountToken = \laabs::getToken('AUTH');

        if (!$accountToken) {
            $userPrivileges = \laabs::configuration('auth')['publicUserStory'];
        } else {
            $userPrivileges = $this->getPrivilege($accountToken->accountId);
        }

        if (empty($userPrivileges)) {
            return false;
        }

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
     * @param string $securityLevel The security level
     *
     * @return array The list of found users
     */
    public function queryUserAccounts($query = "", $securityLevel = null)
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

        $organizationController = \laabs::newController('organization/organization');
        $accountId = \laabs::getToken("AUTH")->accountId;

        $queryAssert = [];
        $queryAssert[] = "accountType='user'";

        if ($query) {
            $queryAssert[] = "$userAccountQueryString";
        }

        $account = $this->sdoFactory->read("auth/account", array("accountId" => $accountId));
        $queryAssert[] = "accountId!=['".$accountId."']";

        if (!empty($this->adminUsers) && !in_array($account->accountName, $this->adminUsers)) {
            $queryAssert[] = "accountId!=['".\laabs\implode("','", $this->adminUsers)."']";
        }

        if ($securityLevel) {
            switch ($securityLevel) {
                case \bundle\auth\Model\role::SECLEVEL_GENADMIN:
                    $queryAssert[] = "(isAdmin=TRUE AND ownerOrgId=null)";
                    break;
                case \bundle\auth\Model\role::SECLEVEL_FUNCADMIN:
                    $queryAssert[] = "(isAdmin=TRUE AND ownerOrgId!=null)";
                    break;
                case \bundle\auth\Model\role::SECLEVEL_USER:
                    $organization = $this->sdoFactory->read('organization/organization', $account->ownerOrgId);
                    $organizations = $organizationController->readDescendantOrg($organization->orgId);
                    $organizations[] = $organization;
                    $organizationsIds = [];
                    foreach ($organizations as $key => $organization) {
                        $organizationsIds[] = (string) $organization->orgId;
                    }

                    $queryAssert[] = "((isAdmin!=TRUE
                AND ownerOrgId=['" .
                        implode("', '", $organizationsIds) .
                        "'])";
            }
            $userAccounts = $this->sdoFactory->find('auth/account', \laabs\implode(" AND ", $queryAssert));
        } else {
            $userAccounts = $this->userList($queryAssert[1]);
        }


        return $userAccounts;
    }

    public function isAuthorized($securitiesLevel)
    {
        if (!is_array($securitiesLevel)) {
            $securitiesLevel = [$securitiesLevel];
        }

        $accountToken = \laabs::getToken('AUTH');
        $account = $this->sdoFactory->read("auth/account", $accountToken->accountId);

        if (!$account->isAdmin && !$account->ownerOrgId) {
            return true;
        }

        foreach ($securitiesLevel as $securityLevel) {
            switch ($securityLevel) {
                case $account::SECLEVEL_GENADMIN:
                    if ($account->isAdmin && !$account->ownerOrgId) {
                        return true;
                    }
                    break;
                case $account::SECLEVEL_FUNCADMIN:
                    if ($account->isAdmin && $account->ownerOrgId) {
                        return true;
                    }
                    break;
                case $account::SECLEVEL_USER:
                    if (!$account->isAdmin && $account->ownerOrgId) {
                        return true;
                    }
                    break;
            }
        }

        throw new \core\Exception\UnauthorizedException("You are not allowed to do this action");
    }

    public function export($limit = null) {
        $userAccounts = $this->sdoFactory->find('auth/account', "accountType='user'", null, null, null, $limit);
        $userAccounts = \laabs::castMessageCollection($userAccounts, 'auth/userAccountImportExport');

        $userPositionController = \laabs::newController('organization/userPosition');
        $roleMemberController = \laabs::newController('auth/roleMember');
        $organizationController = \laabs::newController('organization/organization');
        foreach ($userAccounts as $userAccount) {
            $roleMembers = $roleMemberController->readByUserAccount($userAccount->accountId);
            if (!empty($roleMembers)) {
                foreach ($roleMembers as $roleMember) {
                    $userAccount->roles = $roleMember->roleId;

                    if (end($roleMembers) !== $roleMember) {
                        $userAccount->roles .= ";";
                    }
                }
            }

            $positions = $userPositionController->listPositions($userAccount->accountId);
            if (!empty($positions)) {
                foreach ($positions as $position) {
                    $organization = $organizationController->read($position->orgId);
                    $userAccount->organizations .= $organization->registrationNumber;

                    if (end($positions) !== $position) {
                        $userAccount->organizations .= ";";
                    }
                }
            }
        }

        return $userAccounts;
    }

    public function import ($data, $isReset = false)
    {

    }
}
