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
 * serviceAccount  controller
 *
 * @package Auth
 * @author  Alexandre Morin <alexandre.morin@maarch.org>
 */
class serviceAccount
{

    protected $sdoFactory;
    protected $passwordEncryption;
    protected $securityPolicy;

    /**
     * Constructor
     * @param array                   $securityPolicy     The array of security policy parameters
     * @param \dependency\sdo\Factory $sdoFactory         The dependency Sdo Factory object
     * @param string                  $passwordEncryption The password encryption algorythm
     */
    public function __construct($securityPolicy, \dependency\sdo\Factory $sdoFactory = null, $passwordEncryption = 'md5')
    {
        $this->sdoFactory = $sdoFactory;
        $this->passwordEncryption = $passwordEncryption;
        $this->securityPolicy = $securityPolicy;
    }

    /**
     * List all services for administration
     *
     * @return auth/account[] The array of services
     */
    public function index()
    {
        return $this->sdoFactory->find('auth/account', "accountType='service'");
    }

    /**
     * List all service to display
     *
     * @return array The array of stdClass with dislpay name and service identifier
     */
    public function search()
    {
        $serviceAccounts = $this->sdoFactory->find('auth/account', "accountType='service'");

        return $serviceAccounts;
    }

    /**
     *  Prepare an empty service object
     *
     * @return auth/account The service object
     */
    public function newService()
    {
        $account = \laabs::newInstance('auth/account');

        $account->accountType = 'service';
    }

    /**
     * Enable a service Account
     * @param string $serviceAccountId The service account identifier
     *
     * @return auth/account The service object
     */
    public function enableService($serviceAccountId)
    {
        $serviceAccount = $this->sdoFactory->read("auth/account", $serviceAccountId);
        $serviceAccount->enabled = true;

        return $this->sdoFactory->update($serviceAccount, "auth/account");
    }

    /**
     * Disabled a service Account
     * @param string $serviceAccountId The service account identifier
     *
     * @return auth/account The service object
     */
    public function disableService($serviceAccountId)
    {
        $serviceAccount = $this->sdoFactory->read("auth/account", $serviceAccountId);
        $serviceAccount->enabled = false;

        return $this->sdoFactory->update($serviceAccount, "auth/account");
    }

    /**
     * Record a new service
     * @param auth/account $serviceAccount The service object
     * @param string       $orgId          The organization identifier
     * @param array        $servicesURI    Array of service URI
     *
     * @return auth/account The service object
     */
    public function addService($serviceAccount, $orgId, $servicesURI = [])
    {
        $serviceAccount = \laabs::cast($serviceAccount, 'auth/account');
        $serviceAccount->accountId = \laabs::newId();

        if ($this->sdoFactory->exists('auth/account', array('accountName' => $serviceAccount->accountName))) {
            throw \laabs::newException("auth/serviceAlreadyExistException");
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $this->sdoFactory->create($serviceAccount, 'auth/account');
            $this->createServicePrivilege($servicesURI, $serviceAccount->accountId);
            \laabs::callService("organization/organization/createServiceposition_orgId__userAccountId_", $orgId, $serviceAccount->accountId);
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $serviceAccount;
    }

    /**
     * Prepare a service object for update
     * @param id $serviceAccountId The service unique identifier
     *
     * @return auth/account The service object
     */
    public function edit($serviceAccountId)
    {
        $serviceAccount = $this->sdoFactory->read('auth/account', $serviceAccountId);
        $servicePosition = \laabs::callService("organization/servicePosition/read_serviceAccountId_", $serviceAccountId);

        if (isset($servicePosition->organization)) {
            $serviceAccount->orgId = $servicePosition->organization->orgId;
        }

        $serviceAccount->servicePrivilege = $this->sdoFactory->find('auth/servicePrivilege', "accountId='$serviceAccountId'");

        return $serviceAccount;
    }

    /**
     * Prepare a service object for update
     * @param id $serviceAccountId The service unique identifier
     *
     * @return auth/account The service object
     */
    public function read($serviceAccountId)
    {
        $serviceAccount = $this->sdoFactory->read('auth/account', $serviceAccountId);

        return $serviceAccount;
    }

    /**
     * Modify serviceAccount information
     * @param auth/accountInformation $serviceAccount The service object
     * @param string                  $orgId          The organization identifier
     * @param array                   $servicesURI    Array of service URI
     *
     * @return boolean The result of the request
     */
    public function updateServiceInformation($serviceAccount, $orgId = null, $servicesURI = [])
    {
        $serviceAccount = \laabs::castMessage($serviceAccount, 'auth/serviceAccount');

        if (!$this->sdoFactory->exists('auth/account', array('accountId' => $serviceAccount->accountId))) {
            throw \laabs::newException("auth/unknownServiceException");
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            if ($orgId) {
                $servicePosition = \laabs::callService("organization/servicePosition/read_serviceAccountId_", $serviceAccount->accountId);

                if (isset($servicePosition->organization)) {
                    \laabs::callService("organization/organization/deleteServiceposition_orgId__serviceAccountId_", $servicePosition->organization->orgId, $serviceAccount->accountId);
                }

                \laabs::callService("organization/organization/createServiceposition_orgId__userAccountId_", $orgId, $serviceAccount->accountId);
            }

            $this->sdoFactory->update($serviceAccount, 'auth/account');
            $this->createServicePrivilege($servicesURI, $serviceAccount->accountId);
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }

    /**
     * Generate a service account token
     * @param string $serviceAccountId The service account identifier
     *
     * @return object The credential
     */
    public function generateToken($serviceAccountId)
    {
        // Check userAccount exists
        $currentDate = \laabs::newTimestamp();

        if (!$this->sdoFactory->exists('auth/account', array('accountId' => $serviceAccountId))) {
            \laabs::newController('audit/entry')->add(
                $entryType = "auth/serviceTokenGenerationFailure",
                $objectClass = "auth/account",
                $objectId = "",
                $message = "Connection failure, unknow service ".$serviceAccountId
            );
            throw \laabs::newException('auth/authenticationException', 'Connection failure, invalid service name.');
        }

        $serviceAccount = $this->sdoFactory->read('auth/account', array('accountId' => $serviceAccountId));

        $serviceAccount->salt = md5(microtime());
        $serviceAccount->tokenDate = $currentDate;

        $dataToken = new \StdClass();
        $dataToken->accountId = $serviceAccount->accountId;

        $token = new \core\token($dataToken, 0);
        $token->salt = $serviceAccount->salt;

        $jsonToken = \json_encode($token);
        $cryptedToken = \laabs::encrypt($jsonToken, \laabs::getCryptKey());
        $cookieToken = base64_encode($cryptedToken);

        $serviceAccount->password = $cookieToken;

        $this->sdoFactory->update($serviceAccount, 'auth/account');

        return $cookieToken;
    }

    /**
     * Search service account
     * @param string $query The query
     *
     * @return array The list of found service
     */
    public function queryServiceAccounts($query = false)
    {
        $queryTokens = \laabs\explode(" ", $query);
        $queryTokens = array_unique($queryTokens);

        $serviceAccountQueryProperties = array("displayName");
        $serviceAccountQueryPredicats = array();
        foreach ($serviceAccountQueryProperties as $serviceAccountQueryProperty) {
            foreach ($queryTokens as $queryToken) {
                $serviceAccountQueryPredicats[] = $serviceAccountQueryProperty."="."'*".$queryToken."*'";
            }
        }
        $serviceAccountQueryString = implode(" OR ", $serviceAccountQueryPredicats);
        if (!$serviceAccountQueryString) {
            $serviceAccountQueryString = "1=1";
        }
        $serviceAccountQueryString .= "(".$serviceAccountQueryString.") AND accountType='service'";

        $serviceAccounts = $this->sdoFactory->find('auth/accountIndex', $serviceAccountQueryString);

        return $serviceAccounts;
    }

    /**
     * Get the account privileges
     * @param string $serviceAccountId The service account identifier
     *
     * @return array The list of privileges
     */
    public function getPrivileges($serviceAccountId)
    {
        return $this->sdoFactory->find("auth/servicePrivilege", "accountId='".$serviceAccountId."'");
    }

    /**
     * create the service privileges
     * @param array  $servicesURI The service privilege
     * @param string $accountId   The service account identifier
     *
     * @return bool The result of the operation
     */
    public function createServicePrivilege(array $servicesURI, $accountId)
    {
        foreach ($servicesURI as $key => $service) {
            $service = trim($service);
            if (preg_match('/\s/', $service)) {
                throw new \bundle\auth\Exception\badValueException("The fields contain white spaces.");
            }
        }

        $this->sdoFactory->deleteChildren("auth/servicePrivilege", array("accountId" => $accountId), 'auth/account');

        $servicePrivilege = new \stdClass();

        foreach ($servicesURI as $key => $service) {
            $servicePrivilege->serviceURI = trim($service);
            $servicePrivilege->accountId = $accountId;

            $this->sdoFactory->create($servicePrivilege, "auth/servicePrivilege");
        }

        return true;
    }
}
