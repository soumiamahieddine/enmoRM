<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\adminFunc;

/**
 * User story admin authorization
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface adminServiceaccountInterface
{
     /*
     * SERVICE ACCOUNTS
     */
    /**
     * List all service account
     *
     * @return auth/serviceAccount/indexHtml
     */
    public function readServiceaccounts();

    /**
     * List service accounts list with max results
     *
     * @param string    $accountName    Name of account
     * @param integer   $maxResult      Limit of result display
     *
     * @uses auth/serviceAccount/readSearch
     * @uses auth/serviceAccount/readSearchCount
     *
     * @return auth/serviceAccount/accountList
     */
    public function readServiceaccountslist($accountName = null, $maxResults = null);

    /**
     * Get a service account
     *
     * @uses auth/serviceAccount/read_serviceAccountId_
     * @return auth/serviceAccount/edit
     */
    public function readServiceaccount_serviceAccountId_();

    /**
     * Get a new service account
     *
     * @uses auth/serviceAccount/readNewservice
     * @return auth/serviceAccount/edit
     */
    public function readServiceaccount();

    /**
     * Get a new service account
     * @param auth/serviceAccount $serviceAccount The service account object
     * @param string              $orgId          The organization identifier
     *
     * @uses auth/serviceAccount/create
     *
     * @return auth/serviceAccount/create
     */
    public function createServiceaccount($serviceAccount, $orgId);

    /**
     * Get a new service account
     * @param string $serviceName The service name
     *
     * @uses auth/serviceAccount/updateServicetoken_serviceAccountId_
     *
     * @return auth/serviceAccount/serviceToken
     */
    public function updateServiceaccount_serviceAccountId_Token();

    /**
     * Update service account
     * @param auth/serviceAccount $serviceAccount The service account object
     * @param string              $orgId          The organization identifier
     *
     * @uses auth/serviceAccount/update
     * @return auth/serviceAccount/update
     */
    public function updateServiceaccount($serviceAccount, $orgId);

    /**
     * Enable service account
     *
     * @uses auth/serviceAccount/updateEnable_serviceAccountId_
     * @return auth/serviceAccount/enable
     */
    public function updateServiceaccount_serviceAccountId_Enable();

    /**
     * Disable service account
     *
     * @uses auth/serviceAccount/updateDisable_serviceAccountId_
     * @return auth/serviceAccount/disable
     */
    public function updateServiceaccount_serviceAccountId_Disable();

}
