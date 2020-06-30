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
namespace presentation\maarchRM\Observer;

/**
 * Service for authentication check2
 *
 * @package User
 * @author  Maarch Cyril  VAZQUEZ <cyril.vazquez@maarch.org>
 */
class authentication
{
    protected $sdoFactory;
    protected $accountId;
    protected $accountAuth;
    protected $account;
    protected $requestToken;
    protected $requestTokenTime;

    /**
     * Construct the observer
     * @param object $sdoFactory The user model
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->config = \laabs::configuration("auth")["csrfConfig"];
        $this->whiteList = ['user/prompt'];
    }

    /**
     * Observer for user authentication
     * @param \core\Reflection\Command $userCommand
     * @param array                    $args
     *
     * @return auth/account
     * @subject LAABS_USER_COMMAND
     */
    public function check(&$userCommand, array &$args = null)
    {
        $account = null;

        // Check user story access
        $userStory = \laabs::presentation()->getUserStory($userCommand->userStory);

        if ($userStory->isPublic()) {
            return true;
        }

        if ($userStory->isPrivate()) {
            return false;
        }

        // Check authentication
        switch (true) {
            case ($accountToken = \laabs::getToken('TEMP-AUTH')):
                if (!$this->sdoFactory->exists('auth/account', $accountToken->accountId)) {
                    $this->redirectToLogin();
                }
                if (!isset($userCommand->service[0]) || ($userCommand->service[0] != "auth/authentication/update_userName_Password")) {
                    $this->redirectToLogin();
                }
                break;

            // Token authentication
            case ($accountToken = \laabs::getToken('AUTH')):
                if (!$this->sdoFactory->exists('auth/account', $accountToken->accountId)) {
                    $this->redirectToLogin();
                }
                break;

            // Request authentication
            case ($requestAuth = \core\Kernel\abstractKernel::get()->request->authentication):
                switch ($requestAuth::$mode) {
                    case LAABS_BASIC_AUTH:
                        try {
                            $accountToken = \laabs::callService('auth/authentication/createUserlogin', $requestAuth->username, $requestAuth->password);
                        } catch (\Exception $e) {
                            throw $e;
                        }
                        break;

                    /*case LAABS_DIGEST_AUTH:
                        if ($this->authenticationService->logIn($requestAuth->username, $requestAuth->nonce, $requestAuth->uri, $requestAuth->response, $requestAuth->qop, $requestAuth->nc, $requestAuth->cnonce)) {
                            $token = $this->encrypt($_SESSION['dependency']['authentication']['credential']);
                        }
                        break;

                    case LAABS_APP_AUTH:
                        if (isset($_SERVER['LAABS_AUTH_TOKEN'])) {
                            $token = $_SERVER['LAABS_AUTH_TOKEN'];

                            $credential = $this->decrypt($token);
                            $_SESSION['dependency']['authentication']['credential'] = $credential;
                        }
                        break;
                    */
                }
                break;
        }

        if (!$accountToken) {
            $this->redirectToLogin();
        }

        // Read user account information
        $account = $this->account = $this->sdoFactory->read('auth/account', $accountToken);

        // Reset auth token to update expiration
        $authConfig = \laabs::configuration("auth");
        if (isset($authConfig['securityPolicy']['sessionTimeout'])) {
            $sessionTimeout = $authConfig['securityPolicy']['sessionTimeout'];
        } else {
            $sessionTimeout = 86400;
        }

        $accountToken = new \StdClass();
        $accountToken->accountId = $this->accountId = $account->accountId;

        $this->checkPersistenceToken();

        \laabs::setToken('AUTH', $accountToken, $sessionTimeout);
        $this->updateAccount();
        $organization = \laabs::getToken("ORGANIZATION");

        $userPositionController = \laabs::newController("organization/userPosition");
        $userPositions = $userPositionController->getMyPositions();

        if (!empty($organization)) {
            $isUserPosition = false;
            $default = null;

            foreach ($userPositions as $position) {
                if ($position->orgId == $organization->orgId) {
                    $isUserPosition = true;

                    if (!$default) {
                        $default = $position;
                    }
                }
                if ($position->default) {
                    $default = $position;
                }
            }

            if (!$default) {
                \laabs::newException("auth/authenticationException", "Missing authentication credential", 403);

                $this->redirectToLogin();
            }

            if (!$isUserPosition) {
                \laabs::newException("auth/authenticationException", "Missing authentication credential", 403);
                \laabs::setToken("ORGANIZATION", $default->organization, \laabs::configuration("auth")['securityPolicy']['sessionTimeout']);
            }
        }

        return $account;
    }

    protected function checkPersistenceToken()
    {
        $this->getAccount();

        $this->createSaveToken();

        // Remove expired csrf tokens from security object
        $this->discardExpiredTokens();

        $this->checkRequestToken();
    }

    protected function createSaveToken()
    {
        // Decode authentication object from JSON
        $this->accountAuth = json_decode($this->account->authentication);

        // Create authentication object if not set
        if (empty($this->accountAuth)) {
            $this->accountAuth = new \stdClass();
            $this->accountAuth->auth = [];

            return;
        }

        if (isset($this->accountAuth) && !isset($this->accountAuth->auth)) {
            $this->accountAuth->auth = [];

            return;
        }

        // Create auth token list if not set
        if (!is_object($this->accountAuth->auth)) {
            $this->accountAuth->auth = [];

            return;
        }

        // Convert object to array of timestamp => token
        $this->accountAuth->auth = get_object_vars($this->accountAuth->auth);
    }

    /**
     * Remove tokens which date is expired
     */
    private function discardExpiredTokens()
    {
        // Get lifetime from config, defaults 1h
        $lifetime = '3600';
        if (isset($this->config['lifetime'])) {
            $lifetime = $this->config['lifetime'];
        }
        $duration = \laabs::newDuration('PT'.$lifetime.'S');

        // Current timestamp
        $now = \laabs::newTimestamp();

        // Loop and discard expired tokens
        foreach ($this->accountAuth->auth as $time => $token) {
            $timestamp = \laabs::newTimestamp($time);
            $expiration = $timestamp->add($duration);

            if ($now->diff($expiration)->invert == 1) {
                unset($this->accountAuth->auth[$time]);
            }
        }
    }

    /**
     * Checks wthat a token has been sent with request
     * and that it can be found on account auth object
     *
     * @throws Exception If no token or not found
     */
    private function checkRequestToken()
    {
        $this->requestToken = \laabs::getToken("auth", LAABS_IN_HEADER);
        if (empty($this->requestToken)) {
            throw new \core\Exception('Attempt to access without a valid token', 412);
        }

        $this->requestTokenTime = array_search($this->requestToken, $this->accountAuth->auth);

        if (empty($this->requestTokenTime)) {
            throw new \core\Exception('Attempt to access without a valid token', 412);
        }
    }

    protected function redirectToLogin()
    {
        \laabs::unsetToken("AUTH");
        \laabs::unsetToken("ORGANIZATION");

        \laabs::kernel()->response->code = 307;
        \laabs::kernel()->response->setHeader('Location', '/user/prompt');
        \laabs::kernel()->sendResponse();
        \laabs::kernel()->end();

        exit;
    }

     /**
     * Retrieves the current user accout
     */
    private function getAccount()
    {
        $this->account = $this->sdoFactory->read('auth/account', $this->accountId, $lock = true);
    }

    /**
     * Removes the used token AND the older tokens
     * from auth object
     */
    private function discardUsedTokens()
    {
        foreach ($this->accountAuth->auth as $time => $token) {
            if ($time <= $this->requestTokenTime) {
                unset($this->accountAuth->auth[$time]);
            }
        }
    }

    /**
     * Persists modifications on account auth object
     */
    private function updateAccount()
    {
        $this->account->authentication = json_encode($this->accountAuth);

        $this->sdoFactory->update($this->account, "auth/account");
    }
}
