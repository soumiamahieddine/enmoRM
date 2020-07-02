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
    protected $config;
    protected $whiteList;

    protected $accountId;
    protected $account;
    protected $accountAuth;
    protected $accountToken;

    protected $requestToken;
    protected $requestTokenTime;

    protected $responseToken;
    protected $responseTokenTime;

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
        $accountToken->accountId = $account->accountId;
        // \laabs::setToken('AUTH', $accountToken, $sessionTimeout);
        $this->accountToken = $accountToken;
        $this->persistenceCookie($this->accountToken, $account);

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

    /******************** Persistence cookie part ********************/

    protected function persistenceCookie()
    {
        // Do not process base uri or whitelisted URIs
        if (empty(\laabs::kernel()->request->uri) || in_array(\laabs::kernel()->request->uri, $this->whiteList)) {
            return;
        }

        // Do not process if no user account to retrieve or store csrf tokens
        if (!$this->account) {
            return;
        }

        // Get auth object from json, init data structures if necessary
        $this->getAccountAuth();

        // Remove expired csrf tokens from security object
        $this->discardExpiredTokens();

        $this->checkRequestToken();
        // Save auth information to user account
        $this->updateAccount();

        return true;
    }

    /**
     * Observer for the CSRF protection
     * @param \core\Response\HttpResponse
     *
     * @subject LAABS_RESPONSE
     */
    public function setResponseToken(&$response)
    {
        // // Do not process base uri or whitelisted URIs
        // if (empty(\laabs::kernel()->request->uri) || in_array(\laabs::kernel()->request->uri, $this->whiteList)) {
        //     return;
        // }

        // Do not process if no account was loaded
        if (empty($this->account)) {
            return;
        }

        // Get auth object from json, init data structures if necessary
        $this->getAccountAuth();

        // If a token was received, discard it and all the previous tokens
        if (isset($this->requestTokenTime)) {
            $this->discardUsedTokens();
        }


        // if (empty($this->accountAuth->auth)) {
            // Generate a new one for next write operations
            // $responseToken = $this->addToken();
        // } else {
            // Select the latest unused token
            // $responseToken = $this->getLastToken();
        // }

        \laabs::setToken('AUTH', $this->accountToken, null, false);
        // Save auth information to user account
        $this->updateAccount();
    }

    /**
     * Retrieves the current account auth object
     * containing the auth tokens
     */
    private function getAccountAuth()
    {
        // Decode authentication object from JSON
        $this->accountAuth = json_decode($this->account->authentication);

        // Create authentication object if not set
        if (empty($this->accountAuth)) {
            $this->accountAuth = new \stdClass();
            $this->accountAuth->auth = [];

            return;
        }

        // Create CSRF token list if not set
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
     * Adds a token to the current array of tokens
     * @return string The generated token
     */
    private function addToken()
    {
        $tokenLength = 32;

        if (!empty($this->config["cookieName"])) {
            $tokenLength = $this->config["tokenLength"];
        }

        if (function_exists("openssl_random_pseudo_bytes")) {
            $token = bin2hex(openssl_random_pseudo_bytes($this->accountToken));
        } elseif (function_exists("random_bytes")) {
            $token = bin2hex(random_bytes($this->accountToken));
        } else {
            $token = \laabs::newId();
        }

        $time = (string) \laabs::newTimestamp();
        $this->accountAuth->auth = [];

        return $this->accountAuth->auth[$time] = $token;
    }

    /**
     * Returns the latest token on the auth crsf list
     * @return string
     */
    private function getLastToken()
    {
        ksort($this->accountAuth->auth);

        return end($this->accountAuth->auth);
    }

    /**
     * Checks wthat a token has been sent with request
     * and that it can be found on account auth object
     *
     * @throws Exception If no token or not found
     */
    private function checkRequestToken()
    {
        $this->requestToken = $_COOKIE['LAABS-AUTH'];

        if (empty($this->requestToken)) {
            throw new \core\Exception('Attempt to access without a valid token', 412);
        }

        $this->requestTokenTime = array_search($this->requestToken, $this->accountAuth->auth);

        if (empty($this->requestTokenTime)) {
            throw new \core\Exception('Attempt to access without a valid token', 412);
        }
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
        $time = (string) \laabs::newTimestamp();
        $this->accountAuth->auth = [];
        $this->accountAuth->auth[$time] = $_COOKIE['LAABS-AUTH'];
        $this->account->authentication = json_encode($this->accountAuth);

        $this->sdoFactory->update($this->account, "auth/account");
    }
}
