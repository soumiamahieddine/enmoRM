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

    protected $accountId;
    protected $accountAuth;

    protected $accountId;
    protected $accountAuth;

    /**
     * Construct the observer
     * @param object $sdoFactory The user model
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->whiteList = ['user/prompt', 'user/changePassword'];
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
        // Check user story access
        $userStory = \laabs::presentation()->getUserStory($userCommand->userStory);

        if ($userStory->isPublic()) {
            return true;
        }

        if ($userStory->isPrivate()) {
            return false;
        }

        $this->checkRequestToken($userCommand);

        $this->checkSessionToken();

        $this->checkOrgToken();

        return $this->account;
    }

    protected function checkRequestToken(&$userCommand)
    {
        // Check authentication
        switch (true) {
            case ($this->requestToken = \laabs::getToken('TEMP-AUTH')):
                if (!$this->sdoFactory->exists('auth/account', $this->requestToken->accountId)) {
                    $this->redirectToLogin();
                }
                if (!isset($userCommand->service[0]) || ($userCommand->service[0] != "auth/authentication/update_userName_Password")) {
                    $this->redirectToLogin();
                }
                break;

            // Token authentication
            case ($this->requestToken = \laabs::getToken('AUTH')):
                if (!$this->sdoFactory->exists('auth/account', $this->requestToken->accountId)) {
                    $this->redirectToLogin();
                }
                break;

            // Request authentication
            case ($requestAuth = \core\Kernel\abstractKernel::get()->request->authentication):
                switch ($requestAuth::$mode) {
                    case LAABS_BASIC_AUTH:
                        try {
                            $this->requestToken = \laabs::callService('auth/authentication/createUserlogin', $requestAuth->username, $requestAuth->password);
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

        if (!$this->requestToken) {
            $this->redirectToLogin();
        }

        // Read user account information
        $this->accountId = $this->requestToken->accountId;
        $this->account = $this->sdoFactory->read('auth/account', $this->requestToken);
    }

    protected function checkOrgToken()
    {
        // SET ORG TOKEN
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
    }

    protected function checkSessionToken()
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

        // Check request token equals persisted session token
        $encryptedToken = $this->getEncryptedToken();
        if (empty($encryptedToken) || $encryptedToken !== $this->accountAuth->token) {
            $this->redirectToLogin();
        }

        return true;
    }

    protected function getEncryptedToken()
    {
        if (isset($_COOKIE['LAABS-AUTH'])) {
            return $_COOKIE['LAABS-AUTH'];
        }

        if (isset($_COOKIE['LAABS-TEMP-AUTH'])) {
            return $_COOKIE['LAABS-TEMP-AUTH'];
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

    /******************** Persistence cookie part ********************/
    /**
     * Observer for the authentication
     * @param \core\Response\HttpResponse
     *
     * @subject LAABS_RESPONSE
     */
    public function setResponseToken(&$response)
    {
        if (\laabs::kernel()->request->uri == 'user/logout') {
            return;
        }

        // Do not process if no account was loaded
        if (empty($this->accountId)) {
            return;
        }

        $this->account = $this->sdoFactory->read('auth/account', $this->accountId);

        // Reset auth token to update expiration
        $authConfig = \laabs::configuration("auth");
        if (isset($authConfig['securityPolicy']['sessionTimeout'])) {
            $sessionTimeout = $authConfig['securityPolicy']['sessionTimeout'];
        } else {
            $sessionTimeout = 86400;
        }

        $responseToken = new \StdClass();
        $responseToken->accountId = $this->accountId;
        $encodedToken = \laabs::setToken('AUTH', $responseToken, $sessionTimeout, false);

        $this->getAccountAuth();

        $this->accountAuth->token = $encodedToken;
        $this->account->authentication = json_encode($this->accountAuth);

        $this->sdoFactory->update($this->account, "auth/account");
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
        }

        // Create authentication object if not set
        if (!isset($this->accountAuth->token) || !is_scalar($this->accountAuth->token)) {
            $this->accountAuth->token = null;
        }
    }
}
