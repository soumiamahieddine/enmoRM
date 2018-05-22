<?php
/*
 * Copyright (C) 2018 Maarch
 *
 * This file is part of bundle Auth.
 *
 * Bundle Auth is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle Auth is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle Auth.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Observer;

/**
 * Service for Cross Site Request Forgery protection
 *
 * @package Auth
 * @author Maarch Alexis Ragot <alexis.ragot@maarch.org>
 */
class csrf
{
    protected $sdoFactory;
    protected $config;
    protected $whiteList;
    protected $token;

    /**
     * Construct the observer
     * @param object $sdoFactory The user model
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->config = \laabs::configuration("auth")["csrfConfig"];
        $this->whiteList = \laabs::configuration("auth")["csrfWhiteList"];
    }

    /**
     * Observer for the CSRF protection
     * @param \core\Request\HttpRequest $request
     *
     * @subject LAABS_REQUEST
     */
    public function getToken(&$request)
    {
        $this->token = \laabs::getToken("Csrf", true);
        
    }

    /**
     * Observer for the CSRF protection
     * @param \core\Reflection\Command $userCommand
     * @param array                    $args
     *
     * @subject LAABS_USER_COMMAND
     */
    public function check(&$userCommand, array &$args = null)
    {

        if (empty(\laabs::kernel()->request->uri) || in_array(\laabs::kernel()->request->uri, $this->whiteList)) {
            return;
        }

        switch ($userCommand->method) {
            case "create":
            case "update":
            case "delete":
                if (empty($this->token)) {
                    // todo log csrf attack
                    $userCommand->reroute('app/authentication/readUserPrompt');
                    break;
                }

                $valid = $this->isValidToken($this->token);

                if ($valid) {
                    $this->token = null;
                    break;
                }

                // todo log invalid token
                $userCommand->reroute('app/authentication/readUserPrompt');
                break;
            default:
                break;
        }

        if (empty($this->token)) {
            $this->generateToken();
        }

        return;
    }

    /**
     * Observer for the CSRF protection
     * @param \core\Response\HttpResponse
     *
     * @subject LAABS_RESPONSE
     */
    public function setResponse(&$response)
    {
        \laabs::setToken($this->config["cookieName"], $this->token, null, false);
    }

    private function generateToken()
    {
        $tokenLength = 32;

        if (!empty($this->config["cookieName"])) {
            $tokenLength = $this->config["tokenLength"];
        }

        if (function_exists("openssl_random_pseudo_bytes")) {
            $this->token = bin2hex(openssl_random_pseudo_bytes($tokenLength));
        } elseif (function_exists("random_bytes")) {
            $this->token = bin2hex(random_bytes($tokenLength));
        } else {
            $this->token = \laabs::newId();
        }

        $accountToken = \laabs::getToken('AUTH');
        $account = $this->sdoFactory->read('auth/account', $accountToken);

        if (empty($account->authentication->csrf)) {
            if (empty($account->authentication)) {
                $account->authentication = new \Stdclass();
            }
            $account->authentication->csrf = new \Stdclass();
        }

        $account->authentication->csrf = $this->token;
        $account->authentication = json_encode($account->authentication);

        $this->sdoFactory->update($account, "auth/account");
    }

    private function isValidToken($token)
    {
        $accountToken = \laabs::getToken('AUTH');
        $account = $this->sdoFactory->read('auth/account', $accountToken);

        $account->authentication = json_decode($account->authentication);

        if ($account->authentication->csrf != $token) {
            return false;
        }

        return true;
    }
}
