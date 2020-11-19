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
    protected $csv;
    protected $passwordEncryption;
    protected $securityPolicy;
    protected $organizationController;
    protected $servicePositionController;
    protected $userAccountController;
    protected $hasSecurityLevel;

    /**
     * Constructor
     * @param array                   $securityPolicy     The array of security policy parameters
     * @param \dependency\sdo\Factory $sdoFactory         The dependency Sdo Factory object
     * @param \dependency\csv\Csv     $csv                The dependency Csv
     * @param string                  $passwordEncryption The password encryption algorythm
     */
    public function __construct($securityPolicy, \dependency\sdo\Factory $sdoFactory = null, \dependency\csv\Csv $csv = null, $passwordEncryption = 'md5')
    {
        $this->sdoFactory = $sdoFactory;
        $this->csv = $csv;
        $this->passwordEncryption = $passwordEncryption;
        $this->securityPolicy = $securityPolicy;
        $this->organizationController = \laabs::newController('organization/organization');
        $this->servicePositionController = \laabs::newController('organization/servicePosition');
        $this->userAccountController = \laabs::newController('auth/userAccount');
        $this->hasSecurityLevel = isset(\laabs::configuration('auth')['useSecurityLevel']) ? (bool) \laabs::configuration('auth')['useSecurityLevel'] : false;
    }

    /**
     * List all services for administration
     *
     * @param integer $limit Maximal number of results to dispay
     *
     * @return auth/account[] The array of services
     */
    public function index($limit = null)
    {
        $serviceAccounts = $this->sdoFactory->find('auth/account', "accountType='service'", null, null, null, $limit);
        $serviceAccounts = \laabs::castMessageCollection($serviceAccounts, 'auth/serviceAccountIndex');

        return $serviceAccounts;
    }

    /**
     * List all service to display
     *
     * @param string    $accountName    Name of account
     * @param integer   $maxResults     Max result number to return
     *
     * @return array The array of stdClass with dislpay name and service identifier
     */
    public function search($accountName = null, $maxResults = null)
    {
        $serviceAccounts = $this->sdoFactory->find('auth/account', $this->getSearchQuery($accountName), null, false, false, $maxResults);

        return $serviceAccounts;
    }

    /**
     * Query builder for search and count function
     *
     * @param  string $accountName Account Name
     *
     * @return string PDO query
     */
    public function getSearchQuery($accountName = null)
    {
        $accountId = \laabs::getToken("AUTH")->accountId;
        $account = $this->sdoFactory->read("auth/account", array("accountId" => $accountId));

        $userAccountController = \laabs::newController("auth/userAccount");

        $queryAssert = [];
        $queryAssert[] = "accountType='service'";

        if (!is_null($accountName) && $accountName != "null") {
            $queryAssert[] = "accountName~'*$accountName*'";
        }

        if ($this->hasSecurityLevel) {
            switch ($account->getSecurityLevel()) {
                case $account::SECLEVEL_GENADMIN:
                    $queryAssert[] = "(isAdmin='TRUE' AND ownerOrgId!=null)";
                    break;

                case $account::SECLEVEL_FUNCADMIN:
                    $organization = $this->sdoFactory->read('organization/organization', $account->ownerOrgId);
                    $organizations = $this->organizationController->readDescendantOrg($organization->orgId);
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
                    $queryAssert[] = "((isAdmin!='TRUE' AND ownerOrgId='". $account->ownerOrgId."')";
                    break;
            }
        }

        return \laabs\implode(" AND ", $queryAssert);
    }

    /**
     * Count service Accounts
     *
     * @param  string $accountName
     *
     * @return integer $count Number of service accounts
     */
    public function searchCount($accountName = null)
    {
        $count = $this->sdoFactory->count('auth/account', $this->getSearchQuery($accountName));

        return $count;
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
        $servicePrivilegesTmp= \laabs::configuration('auth')['servicePrivileges'];

        foreach ($servicePrivilegesTmp as $value) {
            $servicePrivilege = \laabs::newInstance('auth/servicePrivilege');
            $servicePrivilege->serviceURI = $value['serviceURI'];
            $servicePrivilege->description = $value['description'];
            $serviceAccount->servicePrivilegeOptions []  = $servicePrivilege;
        }

        $serviceAccount->servicePrivilege = null;

        return $serviceAccount;
    }

    /**
     * Enable a service Account
     * @param string $serviceAccountId The service account identifier
     *
     * @return auth/account The service object
     */
    public function enableService($serviceAccountId)
    {
        $this->userAccountController->isAuthorized(['gen_admin', 'func_admin']);

        $serviceAccount = $this->sdoFactory->read("auth/account", $serviceAccountId);
        $accountToken = \laabs::getToken('AUTH');
        $account = $this->read($accountToken->accountId);

        if ($this->hasSecurityLevel) {
            $this->checkPrivilegesAccess($account, $serviceAccount);
        }

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
        $this->userAccountController->isAuthorized(['gen_admin', 'func_admin']);

        $serviceAccount = $this->sdoFactory->read("auth/account", $serviceAccountId);
        $accountToken = \laabs::getToken('AUTH');
        $account = $this->read($accountToken->accountId);

        if ($this->hasSecurityLevel) {
            if (array_search($serviceAccount->accountName, array_column($this->search(), 'accountName')) === false){
                throw new \core\Exception\UnauthorizedException("You are not allowed to modify this service account");
            }
            $this->checkPrivilegesAccess($account, $serviceAccount);
        }

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
        $this->userAccountController->isAuthorized(['gen_admin', 'func_admin']);
        $organizationController = \laabs::newController("organization/organization");

        $accountToken = \laabs::getToken('AUTH');
        $account = $this->read($accountToken->accountId);

        if (isset($orgId) && !empty($orgId)) {
            try {
                $organization = $organizationController->read($orgId);
            } catch (\Exception $e) {
                throw new \core\Exception\NotFoundException("Organization unit identified by " . $orgId . " does not exist.");
            }
        }

        if ($this->hasSecurityLevel) {
            if ($account->getSecurityLevel() == $account::SECLEVEL_FUNCADMIN && array_search($account->ownerOrgId, array_column($this->organizationController->readParentOrg($orgId), 'orgId')) === false){
                throw new \core\Exception\ForbiddenException("You are not allowed to add user in this organization");
            }
            $this->checkPrivilegesAccess($account, $serviceAccount);
        }

        if (!$serviceAccount->ownerOrgId && !empty($orgId)) {
            if(!empty($serviceAccount->ownerOrgId) && $serviceAccount->ownerOrgId != $organization->ownerOrgId) {
                throw new \core\Exception\NotFoundException("Organization identified by " . $serviceAccount->ownerOrgId . " is not the owner organization of the organization identified by " . $orgId);
            }
            $serviceAccount->ownerOrgId = $organization->ownerOrgId;
        }

        if ($serviceAccount->ownerOrgId) {
            try {
                $organizationController->read($serviceAccount->ownerOrgId);
            } catch (\Exception $e) {
                throw new \core\Exception\NotFoundException("Organization identified by " . $serviceAccount->ownerOrgId . " does not exist.");
            }
        }

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
            if (!$serviceAccount->isAdmin) {
                $this->organizationController->addServicePosition($orgId, $serviceAccount->accountId);
            }
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
        $servicePosition = $this->servicePositionController->getPosition($serviceAccountId);
        $servicePrivilegesTmp= \laabs::configuration('auth')['servicePrivileges'];

        foreach ($servicePrivilegesTmp as $value) {
            $servicePrivilege = \laabs::newInstance('auth/servicePrivilege');
            $servicePrivilege->serviceURI = $value['serviceURI'];
            $servicePrivilege->description = $value['description'];
            $serviceAccount->servicePrivilegeOptions []  = $servicePrivilege;
        }

        if (isset($servicePosition->organization)) {
            $serviceAccount->orgId = $servicePosition->organization->orgId;
        }

        $serviceAccount->servicePrivilege = $this->sdoFactory->find(
            'auth/servicePrivilege',
            "accountId='$serviceAccountId'"
        );

        $serviceAccount->securityLevel = $serviceAccount->getSecurityLevel();

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
        $this->userAccountController->isAuthorized(['gen_admin', 'func_admin']);

        $organizationController = \laabs::newController("organization/organization");
        $accountToken = \laabs::getToken('AUTH');
        $account = $this->read($accountToken->accountId);

        if ($account->accountId != $serviceAccount->accountId && $this->hasSecurityLevel) {
            if (array_search($serviceAccount->accountName, array_column($this->search(), 'accountName')) === false){
                throw new \core\Exception\UnauthorizedException("You are not allowed to modify this service account");
            }
            $this->checkPrivilegesAccess($account, $serviceAccount);
        }

        if ($orgId) {
            $organization = $organizationController->read($orgId);
            $serviceAccount->ownerOrgId = $organization->ownerOrgId;
        }

        $oldServiceAccount = $this->sdoFactory->read('auth/account', $serviceAccount->accountId);
        if (($oldServiceAccount->ownerOrgId && $oldServiceAccount->ownerOrgId != $serviceAccount->ownerOrgId)
        ) {
            throw new \core\Exception\UnauthorizedException("The owner org id cannot be modified");
        }

        $serviceAccount = \laabs::castMessage($serviceAccount, 'auth/serviceAccount');
        if (!$this->sdoFactory->exists('auth/account', array('accountId' => $serviceAccount->accountId))) {
            throw \laabs::newException("auth/unknownServiceException");
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            if (!$serviceAccount->isAdmin) {
                if ($orgId) {
                    $servicePosition = $this->servicePositionController->getPosition($serviceAccount->accountId);

                    if (isset($servicePosition->organization)) {
                        $this->organizationController->deleteServicePosition($servicePosition->orgId, $serviceAccount->accountId);
                    }
                    $this->organizationController->addServicePosition($orgId, $serviceAccount->accountId);
                }
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

        try {
            $serviceAccount = $this->sdoFactory->read('auth/account', array('accountId' => $serviceAccountId));
        } catch (\Exception $e) {
            throw new \core\Exception\NotFoundException("Account identified by " . $serviceAccountId . " does not exist.");
        }

        $accountToken = \laabs::getToken('AUTH');
        $ownAccount = $this->read($accountToken->accountId);

        if ($accountToken->accountId != $serviceAccountId && $this->hasSecurityLevel) {
            $organization = $this->sdoFactory->read('organization/organization', $serviceAccount->ownerOrgId);
            $organizations = $this->organizationController->readDescendantOrg($organization->orgId);
            $organizations[] = $organization;
            if (array_search($serviceAccount->ownerOrgId, array_column($organizations, 'orgId')) === false){
                throw new \core\Exception\ForbiddenException("You are not allowed to modify this service account");
            }
            $this->checkPrivilegesAccess($ownAccount, $serviceAccount);
        }

        $serviceAccount->salt = md5(microtime());
        $serviceAccount->tokenDate = $currentDate;

        $dataToken = new \StdClass();
        $dataToken->accountId = $serviceAccount->accountId;
        $dataToken->salt = $serviceAccount->salt;

        $token = new \core\token($dataToken, 0);

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
     * Search accounts for a privilege
     *
     * @param  string $serviceUri Privilege service uri
     *
     * @return array  $accounts  Array of service accounts with same privilege
     */
    public function getAccountsByPrivilege($serviceUri)
    {
        $queryAssert = null;

        if ($this->hasSecurityLevel) {
            $accountId = \laabs::getToken("AUTH")->accountId;
            $account = $this->sdoFactory->read("auth/account", array("accountId" => $accountId));
            switch ($account->getSecurityLevel()) {
                case $account::SECLEVEL_GENADMIN:
                    $queryAssert = " AND (isAdmin='TRUE' AND ownerOrgId!=null)";
                    break;

                case $account::SECLEVEL_FUNCADMIN:
                    $organization = $this->sdoFactory->read('organization/organization', $account->ownerOrgId);
                    $organizations = $this->organizationController->readDescendantOrg($organization->orgId);
                    $organizations[] = $organization;
                    $organizationsIds = [];
                    foreach ($organizations as $key => $organization) {
                        $organizationsIds[] = (string) $organization->orgId;
                    }

                    $queryAssert = " AND ((ownerOrgId= ['" .
                        implode("', '", $organizationsIds) .
                        "']) OR (isAdmin!=TRUE AND ownerOrgId=null))
                        ";
                    break;

                case $account::SECLEVEL_USER:
                    $queryAssert = " AND ((isAdmin!='TRUE' AND ownerOrgId='". $account->ownerOrgId."')";
                    break;
            }
        }

        $accounts = $this->sdoFactory->index(
            "auth/account",
            ["accountId", "accountName", "displayName"],
            "accountId = [READ auth/servicePrivilege  [accountId] (serviceURI='".$serviceUri."' OR serviceURI = :aster)] . $queryAssert",
            ['aster' => "*"]
        );

        return $accounts;
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
        $this->sdoFactory->deleteChildren("auth/servicePrivilege", array("accountId" => $accountId), 'auth/account');

        if (!empty($servicesURI)) {
            foreach ($servicesURI as $key => $service) {
                $service = trim($service);
                if (preg_match('/\s/', $service)) {
                    throw new \bundle\auth\Exception\badValueException("The fields contain white spaces.");
                }
            }

            $servicePrivilege = new \stdClass();

            foreach ($servicesURI as $key => $service) {
                $servicePrivilege->serviceURI = trim($service);
                $servicePrivilege->accountId = $accountId;

                $this->sdoFactory->create($servicePrivilege, "auth/servicePrivilege");
            }
        }

        return true;
    }

    public function exportCsv($limit = null)
    {
        $serviceAccounts = $this->sdoFactory->find('auth/account', "accountType='service'", null, null, null, $limit);

        $servicePositionController = \laabs::newController('organization/servicePosition');
        $organizationController = \laabs::newController('organization/organization');
        foreach ($serviceAccounts as $key => $serviceAccount) {
            $accountId = $serviceAccount->accountId;
            $ownerOrgId = $serviceAccount->ownerOrgId;
            $serviceAccount = \laabs::castMessage($serviceAccount, 'auth/serviceAccountImportExport');

            if ($ownerOrgId) {
                $organization = $organizationController->read($ownerOrgId);
                $serviceAccount->ownerOrgRegNumber = $organization->registrationNumber;
            }

            $position = $servicePositionController->getPosition($accountId);
            if (!empty($position)) {
                $serviceAccount->organizations = $position->orgId;
            }

            $privileges = $this->getPrivileges($accountId);
            if (!empty($privileges)) {
                foreach ($privileges as $privilege) {
                    $serviceAccount->privileges .= $privilege->serviceURI;

                    if (end($privileges) !== $privilege) {
                        $serviceAccount->privileges .= ";";
                    }
                }
            }
            $serviceAccounts[$key] = $serviceAccount;
        }

        $handler = fopen('php://temp', 'w+');
        $this->csv->writeStream($handler, (array) $serviceAccounts, 'auth/serviceAccountImportExport', true);
        return $handler;
    }

    /**
     * Import Service account function and create or update them
     *
     * @param resource  $data       Array of serviceAccountImportExport Message
     * @param boolean   $isReset    Reset tables or not
     *
     * @return boolean              Success of operation or not
     */
    public function import($data, $isReset = false)
    {
        $services = $this->csv->readStream($data, 'auth/serviceAccountImportExport', $messageType = true);
        $organizationController = \laabs::newController('organization/organization');

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }
        $isServiceAccountQueryOriginator = false;

        if ($isReset) {
            $accountToken = \laabs::getToken('AUTH');
            $account = $this->read($accountToken->accountId);
            if ($account->accountType == 'service') {
                // Check if service deleting is present in csv
                $sameServicePresent = false;
                foreach ($services as $service) {
                    if ($service->accountName == $account->accountName) {
                        $sameServicePresent = true;
                    }
                }

                if (!$sameServicePresent) {
                    throw new \core\Exception\BadRequestException("Service account resetting must be present in csv");
                }

                $isServiceAccountQueryOriginator = true;
                $deletingService = $this->read((string) $account->accountId);
            }
            try {
                $this->deleteAllServices();
            } catch (\Exception $e) {
                if ($transactionControl) {
                    $this->sdoFactory->rollback();
                }
                throw $e;
            }
        }
        $newService = false;
        foreach ($services as $key => $service) {
            if ($isReset) {
                $serviceAccount = $this->newService();
                $serviceAccount->accountId = \laabs::newId();

                if (isset($isServiceAccountQueryOriginator)
                    && $isServiceAccountQueryOriginator
                    && $service->accountName == $deletingService->accountName
                ) {
                    $serviceAccount->accountId = $deletingService->accountId;
                }
            } else {
                $serviceAccount = $this->sdoFactory->find('auth/account', 'accountName="' . $service->accountName . '" ');
                if (!isset($serviceAccount) || empty($serviceAccount)) {
                    $newService = true;
                    $serviceAccount = $this->newService();
                    $serviceAccount->accountId = \laabs::newId();
                } else {
                    $serviceAccount = $serviceAccount[0];
                }
            }

            if (is_null($service->password) || empty($service->password)) {
                throw new \core\Exception\BadRequestException("Password cannot be null");
            }

            if (!$service->isAdmin
                && (
                    is_null($service->organizations)
                    || empty($service->organizations)
                )
            ) {
                throw new \core\Exception\BadRequestException("Service account must be attached to at least one service");
            }

            $serviceAccount->accountName = $service->accountName;
            $serviceAccount->displayName = $service->displayName;
            $serviceAccount->emailAddress = !is_null($service->emailAddress) ? $service->emailAddress : '';
            $serviceAccount->password = $service->password;
            $serviceAccount->passwordChangeRequired = true;
            $serviceAccount->locked = $service->locked;
            $serviceAccount->enabled = $service->enabled;
            $serviceAccount->isAdmin = $service->isAdmin;
            $serviceAccount->accountType = 'service';

            if (!is_null($service->ownerOrgRegNumber) && !empty($service->ownerOrgRegNumber)) {
                $serviceOwnerOrg = $organizationController->getOrgByRegNumber($service->ownerOrgRegNumber);
                if (!is_null($serviceOwnerOrg) && !empty($serviceOwnerOrg)) {
                    $serviceAccount->ownerOrgId = (string) $serviceOwnerOrg->orgId;
                }
            }

            try {
                if ($isReset || $newService) {
                    $this->sdoFactory->create($serviceAccount, 'auth/account');
                    $newService = false;
                } else {
                    $this->sdoFactory->update($serviceAccount, 'auth/account');
                }

                if (!is_null($service->organizations) && !empty($service->organizations)) {
                    $service->organizations = explode(';', $service->organizations);
                    $this->deleteServicePosition((string) $serviceAccount->accountId);
                    $this->importServicePositions((array) $service->organizations, (string) $serviceAccount->accountId);
                }

                $this->importServicePrivileges((array) explode(';', $service->privileges), (string) $serviceAccount->accountId);
            } catch (\Exception $e) {
                if ($transactionControl) {
                    $this->sdoFactory->rollback();
                }
                throw $e;
            }
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }
    }


    private function deleteAllServices()
    {
        $services = $this->index();

        foreach ($services as $key => $service) {
            $this->importDeleteService((string) $service->accountId);
        }
    }

    /**
     * Delete existing user
     *
     * @param auth/account $userAccount The user object unique identifier
     *
     * @return
     */
    public function importDeleteService($serviceAccountId)
    {
        // Delete service positions
        $this->deleteServicePosition($serviceAccountId);

        //delete service Privileges
        $this->sdoFactory->deleteChildren("auth/servicePrivilege", array("accountId" => $serviceAccountId), 'auth/account');

        $this->sdoFactory->delete($this->read($serviceAccountId));
    }

    /**
     * Delete service position
     *
     * @param string $serviceAccountId Unique user identifier
     *
     * @return
     */
    private function deleteServicePosition($serviceAccountId)
    {
        $organizationController = \laabs::newController('organization/organization');
        $servicePositionController = \laabs::newController('organization/servicePosition');
        $organizationSdoFactory = \laabs::dependency('sdo', 'organization')->getService('Factory')->newInstance();

        $currentServicePositions = $organizationSdoFactory->find('organization/servicePosition', "serviceAccountId = '" . $serviceAccountId . "'");

        if (!empty($currentServicePositions)) {
            foreach ($currentServicePositions as $key => $servicePosition) {
                $organizationSdoFactory->delete($servicePosition, 'organization/servicePosition');
            }
        }
    }

    /**
     * Import array of organizations org reg numbers
     *
     * @param array  $organizations    Array of orgRegNumber
     * @param string $serviceAccountId Unique user identifier
     *
     * @return [type]                [description]
     */
    private function importServicePositions($organizations, $serviceAccountId)
    {
        $organizationSdoFactory = \laabs::dependency('sdo', 'organization')->getService('Factory')->newInstance();

        foreach ($organizations as $key => $orgRegNumber) {
            try {
                $organization = $organizationSdoFactory->read("organization/organization", ['registrationNumber' => $orgRegNumber]);
            } catch (\Exception $e) {
                throw new \core\Exception\BadRequestException("Organization '%s' does not exists", 400, null, [$orgRegNumber]);
            }

            $servicePosition = \laabs::newInstance('organization/servicePosition');
            $servicePosition->serviceAccountId = $serviceAccountId;
            $servicePosition->orgId = (string) $organization->orgId;
            $organizationSdoFactory->create($servicePosition, 'organization/servicePosition');
        }
    }

    /**
     * Import array of organizations org reg numbers
     *
     * @param array  $privileges       Array of privileges
     * @param string $serviceAccountId Unique user identifier
     *
     * @return
     */
    private function importServicePrivileges($privileges, $serviceAccountId)
    {
        $this->sdoFactory->deleteChildren("auth/servicePrivilege", array("accountId" => $serviceAccountId), 'auth/account');
        $existingPrivileges = $existingPrivileges = array_column(\laabs::configuration('auth')['servicePrivileges'], 'serviceURI');

        // Check if privileges exists in conf file
        if (!empty(array_diff($privileges, $existingPrivileges))) {
            $differences = array_diff($privileges, $existingPrivileges);
            throw new \core\Exception("Privileges %s does not exits", 400, null, $differences);
        }

        foreach ($privileges as $key => $privilege) {
            $servicePrivilege = new \stdClass();
            $servicePrivilege->serviceURI = $privilege;
            $servicePrivilege->accountId = $serviceAccountId;

            $this->sdoFactory->create($servicePrivilege, "auth/servicePrivilege");
        }
    }

    /**
     * If security level is activated in configuration, check if user has clearance
     *
     * @param auth/account $ownAccount           account realising request
     * @param auth/account $targetServiceAccount account to exert action to
     *
     * @return
     */
    protected function checkPrivilegesAccess($ownAccount, $targetServiceAccount)
    {
        $securityLevel = $ownAccount->getSecurityLevel();
        if ($securityLevel == $ownAccount::SECLEVEL_GENADMIN) {
            if (!isset($targetServiceAccount->ownerOrgId) || !$targetServiceAccount->isAdmin) {
                throw new \core\Exception\ForbiddenException("Only a Functional administrator can do this action");
            }
        } elseif ($securityLevel == $ownAccount::SECLEVEL_FUNCADMIN) {
            if ($targetServiceAccount->isAdmin) {
                throw new \core\Exception\ForbiddenException("Only a General administrator can do this action");
            }
        } elseif ($securityLevel == $ownAccount::SECLEVEL_USER) {
            if ($ownAccount != $targetServiceAccount) {
                throw new \core\Exception\ForbiddenException("You are not allowed to do this action");
            }
        }
    }
}
