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

    protected $account;
    
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
        $this->whiteList = \laabs::configuration("auth")["csrfWhiteList"];
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

        $this->getAccount();

        $this->requestToken = \laabs::getToken("Csrf", LAABS_IN_HEADER);
        header('X-Laabs-requestToken: '.json_encode($this->requestToken));

        switch ($userCommand->method) {
            case "create":
            case "update":
            case "delete":
                if (!$this->isValidToken()) {
                    $userCommand->reroute('app/authentication/readUserPrompt');
                    
                    return false;
                }

                $this->shiftTokens();
                break;

            default:
                if (empty($this->account->authentication->csrf)) {
                    $this->addToken();
                } else {
                    $this->resendToken();
                }
                break;
        }       

        $this->updateAccount();

        return true;
    }

    /**
     * Observer for the CSRF protection
     * @param \core\Response\HttpResponse
     *
     * @subject LAABS_RESPONSE
     */
    public function setResponse(&$response)
    {
        header('X-Laabs-responseToken: '.json_encode($this->responseToken));
        \laabs::setToken($this->config["cookieName"], $this->responseToken, null, false);
    }

    /**
     * Gets the account information with a LOCK on database
     */
    private function getAccount()
    {
        $accountToken = \laabs::getToken('AUTH');

        $this->sdoFactory->beginTransaction();
        $this->account = $this->sdoFactory->read('auth/account', $accountToken, $lock=true);
        $this->account->authentication = json_decode($this->account->authentication);

        if (empty($this->account->authentication)
            || !isset($this->account->authentication->csrf) 
            || empty($this->account->authentication->csrf)
            || !is_object($this->account->authentication->csrf)) {
            return;
        }

        header('X-Laabs-accountTokens: '.json_encode($this->account->authentication->csrf));

        $this->account->authentication->csrf = get_object_vars($this->account->authentication->csrf);
    }

    private function addToken()
    {
        $tokenLength = 32;

        if (!empty($this->config["cookieName"])) {
            $tokenLength = $this->config["tokenLength"];
        }

        if (function_exists("openssl_random_pseudo_bytes")) {
            $this->responseToken = bin2hex(openssl_random_pseudo_bytes($tokenLength));
        } elseif (function_exists("random_bytes")) {
            $this->responseToken = bin2hex(random_bytes($tokenLength));
        } else {
            $this->responseToken = \laabs::newId();
        }

        $this->responseTokenTime = (string) \laabs::newTimestamp();

        if (!isset($this->account->authentication->csrf) || !is_array($this->account->authentication->csrf)) {
            $this->account->authentication->csrf = [];
        }

        $this->account->authentication->csrf[$this->responseTokenTime] = $this->responseToken;

        return true;
    }

    private function resendToken()
    {
        ksort($this->account->authentication->csrf);

        $this->responseToken = end($this->account->authentication->csrf);
        $this->responseTokenTime = key($this->account->authentication->csrf);
    }

    private function isValidToken()
    {
        if (empty($this->requestToken)) {
            $e = new \core\Exception('Attempt to access without a valid token', 412);
            $e->errors[] = "Empty token";

            throw $e;

            return false;
        }

        $this->requestTokenTime = array_search($this->requestToken, $this->account->authentication->csrf);

        if (empty($this->requestTokenTime)) {
            $this->requestToken = null;

            $e = new \core\Exception('Attempt to access without a valid token', 412);
            $e->errors[] = "Token not found";

            throw $e;
        }

        $this->responseToken = $this->requestToken;

        return true;
    }

    private function shiftTokens()
    {
        if (!isset($this->account->authentication->csrf) || !is_array($this->account->authentication->csrf)) {
            $this->account->authentication->csrf = [];
        }

        foreach ($this->account->authentication->csrf as $time => $token) {
            if ($time < $this->requestTokenTime) {
                unset($this->account->authentication->csrf[$time]);
            }
        }
    }

    private function updateAccount()
    {
        if (empty($this->account->authentication)) {
            $this->account->authentication = new \Stdclass();
        }

        if (empty($this->account->authentication->csrf)) {
            $this->account->authentication->csrf = [];
        }

        $this->account->authentication = json_encode($this->account->authentication);

        $this->sdoFactory->update($this->account, "auth/account");

        $this->sdoFactory->commit();
    }
}
