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

namespace Presentation\maarchRM\Presenter\auth;

/**
 * Serializer for service account in html
 *
 * @package Auth
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class serviceAccount
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $json;
    public $translator;

    /**
     * Constructor
     * @param \dependency\html\Document   $view The default view document
     * @param \dependency\json\JsonObject $json The default JSON object
     *
     * @return void
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json)
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $view->translator;
        $this->translator->setCatalog('auth/messages');
    }

    /**
     * List all service account
     * @param array $serviceAccounts Array of service account object
     *
     * @return string
     */
    public function indexHtml()
    {
        $maxResults = null;
        if (isset(\laabs::configuration('presentation.maarchRM')['maxResults'])) {
            $maxResults = \laabs::configuration('presentation.maarchRM')['maxResults'];
        }
        $this->view->addContentFile("auth/serviceAccount/index.html");

        $this->view->setSource("maxResults", $maxResults);


        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * @param array     $serviceAccounts    Array of service accounts
     * @param integer   $count              Count of service accounts without limits
     *
     */
    public function accountList(array $serviceAccounts, $count)
    {
        $this->view->setSource("serviceAccounts", $serviceAccounts);
        $this->view->setSource("totalResultsWithoutLimit", $count);
        $this->view->addContentFile("auth/serviceAccount/serviceAccountsList.html");
        $hasSecurityLevel = isset(\laabs::configuration('auth')['useSecurityLevel']) ? (bool) \laabs::configuration('auth')['useSecurityLevel'] : false;

        $accountId = \laabs::getToken("AUTH")->accountId;
        $account = \laabs::callService("auth/userAccount/read_userAccountId_", $accountId);

        $securityLevel = $account->securityLevel;

        $manageUserRights = true;
        if ($hasSecurityLevel && $securityLevel == \bundle\auth\Model\account::SECLEVEL_USER) {
            $manageUserRights = false;
        }

        $hasReachMaxResults = false;
        if (isset(\laabs::configuration('presentation.maarchRM')['maxResults'])
            && count($serviceAccounts) >= \laabs::configuration('presentation.maarchRM')['maxResults']) {
            $hasReachMaxResults = true;
        }

        $this->view->setSource('hasReachMaxResults', $hasReachMaxResults);
        $this->view->setSource('manageUserRights', $manageUserRights);

        $table = $this->view->getElementById("list-serviceAccount");
        $dataTable = $table->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(3);
        $dataTable->setUnsearchableColumns(1, 3);

        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * View form to edit a token
     * @param auth/serviceAccount $serviceAccount Service account object
     *
     * @return string
     */
    public function edit($serviceAccount)
    {
        $tabOrganizations = \laabs::callService('organization/organization/readIndex');
        $ownerOrganizations = [];
        $organizations = [];

        foreach ($tabOrganizations as $org) {
            if ($org->isOrgUnit) {
                $organizations[] = $org;
            } else {
                $ownerOrganizations []= $org;
            }
        }

        if ($serviceAccount->servicePrivilege) {
            $noDescription = true;
            foreach ($serviceAccount->servicePrivilege as $servicePrivilege) {
                foreach ($serviceAccount->servicePrivilegeOptions as $option) {
                    if ($servicePrivilege->serviceURI == $option->serviceURI) {
                        $servicePrivilege->description = $option->description;
                        $noDescription = false;
                    }
                }
                if ($noDescription) {
                    $servicePrivilege->description =  $servicePrivilege->serviceURI;
                }
                $noDescription = true;
            }
        }

        foreach ($organizations as $org) {
            if (isset($serviceAccount->orgId) && $org->orgId == $serviceAccount->orgId) {
                $serviceAccount->orgName = $org->displayName;
                $ownerOrgid = $org->ownerOrgId;
            }
        }
        foreach ($ownerOrganizations as $org) {
            if (isset($ownerOrgid) && $ownerOrgid == $org->orgId) {
                $serviceAccount->ownerOrgName = $org->displayName;
            }
        }

        $accountId = \laabs::getToken("AUTH")->accountId;
        $account = \laabs::callService("auth/userAccount/read_userAccountId_", $accountId);

        if (!is_null($account->securityLevel) &&  $account->securityLevel != "") {
            $whatAmI = $account->securityLevel;
        } else {
            $whatAmI = 'userWithoutSecurityLevel';
        }

        $this->view->addContentFile("auth/serviceAccount/edit.html");
        $this->view->setSource("organizations", $organizations);
        $this->view->merge($this->view->getElementById("serviceOrgId"));
        $this->view->setSource("serviceAccount", $serviceAccount);
        $this->view->setSource('whatAmI', $whatAmI);


        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * Json serializer for creation method
     * @param auth/serviceAccount $serviceAccount Service account object
     *
     * @return string
     */
    public function create($serviceAccount)
    {
        $this->json->message = "Service account created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Json serializer for update method
     * @param auth/serviceAccount $serviceAccount Service account object
     *
     * @return string
     */
    public function update($serviceAccount)
    {
        $this->json->message = "Service account updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Json serializer for enable method
     *
     * @return string
     */
    public function enable()
    {
        $this->json->message = "Service account is enabled";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Json serializer for disable method
     *
     * @return string
     */
    public function disable()
    {
        $this->json->message = "Service account is disabled";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Exception
     * @param auth/Exception/serviceAlreadyExistException $serviceException
     *
     * @return string
     */
    public function serviceAlreadyExistException($serviceException)
    {
        $this->json->load($serviceException);
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->status = false;

        return $this->json->save();
    }

    public function serviceToken($cookieToken) {
        $this->json->cookie = $cookieToken;
        $this->json->message = "Token changed";
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->status = true;

        return $this->json->save();
    }
    /**
     * Exception
     * @param auth/Exception/badValueException $exception
     *
     * @return string
     */
    public function badValueException($exception)
    {
        $this->json->message = $this->translator->getText($exception->getMessage());
        $this->json->status = false;

        return $this->json->save();
    }
}
