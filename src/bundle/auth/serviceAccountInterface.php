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

namespace bundle\auth;

/**
 * Interface for serviceAccount
 */
interface serviceAccountInterface
{

    /**
     * List the authorization's service Account
     *
     * @action auth/serviceAccount/index
     */
    public function readIndex();

    /**
     * List the service account detail
     *
     * @action auth/serviceAccount/search
     */
    public function readSearch();

    /**
     * Prepare an empty service Account object
     *
     * @action auth/serviceAccount/newService
     */
    public function readNewservice();


    /**
     * Create a csv file
     *
     * @param  integer $limit Max number of results to display
     *
     * @action auth/serviceAccount/exportCsv
     *
     */
    public function readExport($limit = null);

    /**
     * Prepares access control object for update or create
     *
     * @action auth/serviceAccount/edit
     */
    public function read_serviceAccountId_();

    /**
     * List service account privilege
     *
     * @action auth/serviceAccount/getPrivileges
     */
    public function readPrivilege_serviceAccountId_();

    /**
     * Create a new service Account
     * @param auth/serviceAccount $serviceAccount
     * @param string              $orgId
     * @param array               $servicesURI
     *
     * @action auth/serviceAccount/addService
     */
    public function create($serviceAccount, $orgId, $servicesURI);

    /**
     * Updates a service Account
     * @param auth/serviceAccount $serviceAccount
     * @param string              $orgId
     * @param array               $servicesURI
     *
     * @action auth/serviceAccount/updateServiceInformation
     */
    public function update($serviceAccount, $orgId = null, $servicesURI);

    /**
     * Enable a service Account
     * @param string $serviceAccountId
     *
     * @action auth/serviceAccount/enableService
     */
    public function updateEnable_serviceAccountId_();

    /**
     * Disable a service Account
     * @param string $serviceAccountId
     *
     * @action auth/serviceAccount/disableService
     */
    public function updateDisable_serviceAccountId_();

    /**
     * Generate service account token
     *
     * @action auth/serviceAccount/generateToken
     */
    public function updateServicetoken_serviceAccountId_();

    /**
     * Search the service account for typehead
     *
     * @param string $query The query string
     *
     * @action auth/serviceAccount/queryServiceAccounts
     */
    public function readQuery_query_($query = null);

    /**
     * @param resource  $data     Data base64 encoded or not
     * @param boolean   $isReset  Reset tables or not
     *
     * @action auth/serviceAccount/import
     *
     * @return boolean        Import with reset of table data or not
     */
    public function createImport($data, $isReset);

    /**
     * @param string $serviceUri Uri to check privileges
     *
     * @action auth/serviceAccount/getAccountsByPrivilege
     *
     */
    public function readByRoute($serviceUri);
}
