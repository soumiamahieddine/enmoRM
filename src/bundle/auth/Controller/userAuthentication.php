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
namespace bundle\auth\Controller;

/**
 * user authentication controller
 *
 * @package Auth
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 */
class userAuthentication
{
    /**
     * The password encryption
     *
     * @var string
     **/
    protected $passwordEncryption;

    /**
     * The security policy of the password
     *
     * @var string
     **/
    protected $securityPolicy;

    /**
     * Constructor
     * @param object $sdoFactory         The user model
     * @param string $passwordEncryption The password encryption
     * @param array  $securityPolicy     The security policy
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $passwordEncryption, $securityPolicy)
    {
        $this->sdoFactory = $sdoFactory;
        $this->passwordEncryption = $passwordEncryption;
        $this->securityPolicy = $securityPolicy;
    }

    /**
     * Authenticate a user
     * @param string $userName The user name
     * @param string $password The user password
     *
     * @throws \bundle\auth\Exception\authenticationException
     *
     * @return bool
     */
    public function login($userName, $password)
    {
        // Check userAccount exists
        $currentDate = \laabs::newTimestamp();

        $exists = $this->sdoFactory->exists('auth/account', array('accountName' => $userName));

        if (!$exists) {
            throw \laabs::newException('auth/authenticationException', 'Username not registered or wrong password.', 401);
        }

        $userAccount = $this->sdoFactory->read('auth/account', array('accountName' => $userName));

        // Create user login object
        $userLogin = \laabs::newInstance('auth/userLogin');
        $userLogin->accountId = $userAccount->accountId;
        $userLogin->lastIp = $_SERVER["REMOTE_ADDR"];

        // Hash password
        $encryptedPassword = $password;
        if ($this->passwordEncryption != null) {
            $encryptedPassword = hash($this->passwordEncryption, $password);
        }

        // Check enabled
        if ($userAccount->enabled != true) {
            $e = \laabs::newException('auth/authenticationException', 'User %1$s is disabled', 403, null, array($userName));
            throw $e;
        }

        // Check locked
        if ($userAccount->locked == true) {
            if (!isset($this->securityPolicy['lockDelay']) // No delay while locked
                || $this->securityPolicy['lockDelay'] == 0 // Unlimited delay
                || !isset($userAccount->lockDate)          // Delay but no date for lock so unlimited
                || ($currentDate->getTimestamp() - $userAccount->lockDate->getTimestamp()) < ($this->securityPolicy['lockDelay']) // Date + delay upper than current date
            ) {
                throw \laabs::newException('auth/authenticationException', 'User %1$s is locked', 403, null, array($userName));
            }
        }

        // Check password
        if ($userAccount->password !== $encryptedPassword) {
            // Update bad password count
            $userLogin->badPasswordCount = $userAccount->badPasswordCount + 1;
            $this->sdoFactory->update($userLogin, 'auth/account');

            // If count exceeds max attempts, lock user
            if ($this->securityPolicy['loginAttempts'] && $userLogin->badPasswordCount > $this->securityPolicy['loginAttempts'] - 1) {
                \laabs::callService("auth/userAccount/updateLock_userAccountId_", $userLogin->accountId, true);
                \laabs::callService('audit/event/create', "auth/userAccount/updateLock_userAccountId_", array("accountId" => $userLogin->accountId), null, true, true);
            }

            throw \laabs::newException('auth/authenticationException', 'Username not registered or wrong password.', 403);
        }

        if (!empty($userAccount->lastLogin) && $userAccount->passwordChangeRequired == true && !empty($this->securityPolicy["newPasswordValidity"]) && $this->securityPolicy["newPasswordValidity"] != 0) {
            $interval = \laabs::newDuration("PT".$this->securityPolicy["newPasswordValidity"]."H");
            $limitToChange = $userAccount->passwordLastChange->shift($interval)->getTimestamp();
            $diff = $limitToChange - \laabs::newDateTime()->getTimestamp();

            if ($diff < 0) {
                throw \laabs::newException('auth/authenticationException', 'Username not registered or wrong password.', 403);
            }
        }

        // Login success, update user account values
        $userLogin->badPasswordCount = 0;
        $userLogin->locked = false;
        $userLogin->lockDate = null;
        $userLogin->lastLogin = $currentDate;

        $this->sdoFactory->update($userLogin, 'auth/account');

        if (isset($this->securityPolicy['sessionTimeout'])) {
            $tokenDuration = $this->securityPolicy['sessionTimeout'];
        } else {
            $tokenDuration = 86400;
        }

        $accountToken = new \StdClass();
        $accountToken->accountId = $userAccount->accountId;
        \laabs::setToken('AUTH', $accountToken, $tokenDuration);

        if ($this->securityPolicy['passwordValidity'] && $this->securityPolicy["passwordValidity"] != 0) {
            $diff = ($currentDate->getTimestamp() - $userAccount->passwordLastChange->getTimestamp()) / 86400;
            if ($diff > $this->securityPolicy['passwordValidity']) {
                throw \laabs::newException('auth/userPasswordChangeRequestException');
            }
        }

        if ($userAccount->passwordChangeRequired == true) {
            \laabs::setToken('TEMP-AUTH', $accountToken, $tokenDuration);
            \laabs::unsetToken('AUTH');
            throw \laabs::newException('auth/userPasswordChangeRequestException');
        }

        return $userAccount;
    }

    /**
     * Get form to edit user information
     * @param string $userName    The user's name
     * @param string $oldPassword The user's old password
     * @param string $newPassword The user's new password
     * @param string $requestPath The requested path
     *
     * @return boolean
     */
    public function definePassword($userName, $oldPassword, $newPassword, $requestPath)
    {
        if ($userAccount = $this->sdoFactory->read('auth/account', array('accountName' => $userName))) {
            //validation of security policy
            if ($this->securityPolicy['passwordMinLength'] && strlen($newPassword) < $this->securityPolicy['passwordMinLength']) {
                throw new \core\Exception\ForbiddenException("The password is too short.", 403);
            }

            if ($this->securityPolicy['passwordRequiresSpecialChars'] && ctype_alnum($newPassword)) {
                throw new \core\Exception\ForbiddenException("The password must contain special characters.", 403);
            }

            if ($this->securityPolicy['passwordRequiresDigits'] && !preg_match('~[0-9]~', $newPassword)) {
                throw new \core\Exception\ForbiddenException("The password must contain digits.", 403);
            }

            if ($this->securityPolicy['passwordRequiresMixedCase'] && (!preg_match('~[A-Z]~', $newPassword) || !preg_match('~[a-z]~', $newPassword))) {
                throw new \core\Exception\ForbiddenException("The password must contain upper and lower case.", 403);
            }

            $encryptedPassword = $newPassword;
            if ($this->passwordEncryption != null) {
                $encryptedPassword = hash($this->passwordEncryption, $newPassword);
            }
            if ($userAccount->password == $encryptedPassword) {
                throw new \core\Exception\ForbiddenException("The password is the same as the precedent.", 403);
            }

            $userAccount->password = $encryptedPassword;
            $userAccount->passwordLastChange = \laabs::newTimestamp();
            $userAccount->passwordChangeRequired = false;
            $this->sdoFactory->update($userAccount, 'auth/account');

            $this->login($userAccount->accountName, $newPassword, $requestPath);
            \laabs::unsetToken('TEMP-AUTH');

            return $requestPath;
        }

        return false;
    }

    /**
     * Log out a user
     *
     * @return bool
     */
    public function logout()
    {
        \laabs::clearTokens();
    }
}
