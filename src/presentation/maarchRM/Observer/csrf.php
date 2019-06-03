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
    protected $accountTokens = [];
    
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

        $requestToken = \laabs::getToken("Csrf", LAABS_IN_HEADER);

        // Get account with LOCK
        $this->sdoFactory->beginTransaction();
        
        $account = $this->getAccount(true);
        if (!$account) {
            $this->sdoFactory->rollback();

            throw new \core\Exception('Attempt to access without a valid token 2', 412);
        }
        $accountTokens = $account->authentication->csrf;

        
        switch ($userCommand->method) {
            case "create":
            case "update":
            case "delete":
                if (empty($requestToken)) {
                    throw new \core\Exception('Attempt to access without a valid token 1', 412);
                }

                $requestTokenTime = $this->checkToken($requestToken, $accountTokens);
                $accountTokens = $this->shiftTokens($requestTokenTime, $accountTokens);
                $accountTokens = $this->addToken($accountTokens);
                break;

            default:
                if (empty($accountTokens)) {
                    $accountTokens = $this->addToken([]);
                }
                break;
        }

        $account->authentication->csrf = $accountTokens;

        // Set account and COMMIT
        try {
            $this->updateAccount($account);
            $this->sdoFactory->commit();
        } catch (\Exception $exception) {
            $this->sdoFactory->rollback();
        }

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
        $account = $this->getAccount(false);
        if (!$account) {
            return;
        }

        $accountTokens = $account->authentication->csrf;
        
        $responseToken = $this->getLastToken($accountTokens);
        
        \laabs::setToken($this->config["cookieName"], $responseToken, null, false);
    }

    /**
     * Retrieves the account information with a LOCK on database
     * @param bool $lock Lock user
     *
     * @return auth/userAccount
     */
    private function getAccount($lock = false)
    {
        $accountToken = \laabs::getToken('AUTH');

        if (!$accountToken) {
            $accountToken = \laabs::getToken('TEMP-AUTH');

            if (!$accountToken) {
                return false;
            }
        }

        $account = $this->sdoFactory->read('auth/account', $accountToken->accountId, $lock);
        $account->authentication = json_decode($account->authentication);

        if (empty($account->authentication)) {
            $account->authentication = new \stdClass();
            $account->authentication->csrf = [];

            return $account;
        }

        if (!is_object($account->authentication->csrf)) {
            $account->authentication->csrf = [];
            return $account;
        }

        $account->authentication->csrf = get_object_vars($account->authentication->csrf);

        $lifetime = '3600';
        if (isset($this->config['lifetime'])) {
            $lifetime = $this->config['lifetime'];
        }
        $duration = \laabs::newDuration('PT'.$lifetime.'S');
        $now = \laabs::newTimestamp();

        foreach ($account->authentication->csrf as $time => $token) {
            $timestamp = \laabs::newTimestamp($time);
            $expiration = $timestamp->add($duration);

            if ($now->diff($expiration)->invert == 1) {
                unset($account->authentication->csrf[$time]);
            }
        }


        return $account;
    }

    /**
     * Adds a token to the current array of tokens
     * @param array $accountTokens
     * 
     * @return array
     */
    private function addToken($accountTokens)
    {
        $tokenLength = 32;

        if (!empty($this->config["cookieName"])) {
            $tokenLength = $this->config["tokenLength"];
        }

        if (function_exists("openssl_random_pseudo_bytes")) {
            $token = bin2hex(openssl_random_pseudo_bytes($tokenLength));
        } elseif (function_exists("random_bytes")) {
            $token = bin2hex(random_bytes($tokenLength));
        } else {
            $token = \laabs::newId();
        }

        $time = (string) \laabs::newTimestamp();

        $accountTokens[$time] = $token;

        return $accountTokens;
    }

    private function getLastToken($accountTokens)
    {
        ksort($accountTokens);

        return end($accountTokens);
    }

    private function checkToken($requestToken, $accountTokens)
    {
        $requestTokenTime = array_search($requestToken, $accountTokens);

        if (empty($requestTokenTime)) {
            $requestToken = null;

            $e = new \core\Exception('Attempt to access without a valid token', 412);

            throw $e;
        }

        return $requestTokenTime;
    }

    private function shiftTokens($requestTokenTime, $accountTokens)
    {
        foreach ($accountTokens as $time => $token) {
            if ($time <= $requestTokenTime) {
                unset($accountTokens[$time]);
            }
        }

        return $accountTokens;
    }

    private function updateAccount($account)
    {
        $account->authentication = json_encode($account->authentication);

        $this->sdoFactory->update($account, "auth/account");
    }
}
