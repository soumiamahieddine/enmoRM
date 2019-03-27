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

    /**
     * Construct the observer
     * @param object $sdoFactory The user model
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
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
        $account = $this->sdoFactory->read('auth/account', $accountToken);

        // Reset auth token to update expiration
        $authConfig = \laabs::configuration("auth");
        if (isset($authConfig['securityPolicy']['sessionTimeout'])) {
            $sessionTimeout = $authConfig['securityPolicy']['sessionTimeout'];
        } else {
            $sessionTimeout = 86400;
        }

        $accountToken = new \StdClass();
        $accountToken->accountId = $account->accountId;
        \laabs::setToken('AUTH', $accountToken, $sessionTimeout);

        $organization = \laabs::getToken("ORGANIZATION");

        $userPositionController = \laabs::newController("organization/userPosition");
        $userPositions = $userPositionController->getMyPositions();

        if (!empty($organization)) {
            $isUserPosition = false;
            $default = null;

            foreach ($userPositions as $position) {
                if ($position->orgId == $organization->orgId) {
                    $isUserPosition = true;
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
        \laabs::kernel()->response->code = 307;
        \laabs::kernel()->response->setHeader('Location', '/user/prompt');
        \laabs::kernel()->sendResponse();
        \laabs::kernel()->end();

        exit;
    }
}
