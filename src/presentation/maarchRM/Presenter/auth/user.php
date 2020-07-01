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
namespace presentation\maarchRM\Presenter\auth;

/**
 * user admin html serializer
 *
 * @package User
 * @author  Arnaud VEBER <arnaud.veber@maarch.org>
 */
class user
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    /**
     *
     */
    protected $sdoFactory;
    public $view;
    public $json;
    public $translator;

    /**
     * Constructor
     * @param \dependency\html\Document $view A new empty Html document
     * @param \dependency\sdo\Factory $sdoFactory The dependency Sdo Factory object
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator,
        \dependency\sdo\Factory $sdoFactory = null
    ) {
        $this->view = $view;

        $this->json = $json;
        $this->translator = $translator;
        $this->translator->setCatalog('auth/messages');
        $this->json->status = true;
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * View for the users admin index panel
     * @param array $users An array of user objects to display
     * @param string $offset The offset
     * @param string $length The length
     *
     * @return string The html view string
     */
    public function indexHtml($offset = 0, $length = 10)
    {
        $view = $this->view;

        $accountId = \laabs::getToken("AUTH")->accountId;
        $account = \laabs::callService("auth/userAccount/read_userAccountId_", $accountId);
        $hasSecurityLevel = isset(\laabs::configuration('auth')['useSecurityLevel']) ? (bool) \laabs::configuration('auth')['useSecurityLevel'] : false;

        $securityLevel = $account->securityLevel;

        $manageUserRights = true;
        if ($hasSecurityLevel && $securityLevel == \bundle\auth\Model\account::SECLEVEL_USER) {
            $manageUserRights = false;
        }

        $view->addContentFile("auth/userAccount/admin/index.html");
        $view->translate();

        $view->setSource('manageUserRights', $manageUserRights);

        $view->merge();

        return $view->saveHtml();
    }

    /**
     * View for the users datatable
     * @param boolean $showDisabled show disabled user accounts
     *
     * @return string The html view string
     */
    public function indexDatatable($users)
    {
        $view = $this->view;

        $accountId = \laabs::getToken("AUTH")->accountId;
        $account = \laabs::callService("auth/userAccount/read_userAccountId_", $accountId);
        $hasSecurityLevel = isset(\laabs::configuration('auth')['useSecurityLevel']) ? (bool) \laabs::configuration('auth')['useSecurityLevel'] : false;

        $securityLevel = $account->securityLevel;

        $manageUserRights = true;
        if ($hasSecurityLevel && $securityLevel == \bundle\auth\Model\account::SECLEVEL_USER) {
            $manageUserRights = false;
        }

        $view->addContentFile("auth/userAccount/admin/datatable.html");
        $view->translate();

        $table = $view->getElementById("user_userList");
        $dataTable = $table->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");

        $dataTable->setUnsortableColumns(3, 4, 5);
        $dataTable->setUnsearchableColumns(3, 4, 5);

        $view->setSource('users', $users);
        $view->setSource('manageUserRights', $manageUserRights);

        $view->merge();

        return $view->saveHtml();
    }

    /**
     * View for the edit user form
     * @param user /user $user The user object
     *
     * @return string The html view string
     */
    public function edit($user)
    {
        $user->roles = empty($user->roles) ? false : json_encode($user->roles);

        $roles = $this->sdoFactory->find('auth/role');

        $view = $this->view;

        $view->addContentFile("auth/userAccount/admin/edit.html");

        $restrictUserRoles = isset(\laabs::configuration('auth')['restrictUserRoles']) && \laabs::configuration('auth')['restrictUserRoles'];
        $publicArchives = isset(\laabs::configuration('presentation.maarchRM')['publicArchives']) && \laabs::configuration('presentation.maarchRM')['publicArchives'];
        $hasSecurityLevel = isset(\laabs::configuration('auth')['useSecurityLevel']) ? (bool) \laabs::configuration('auth')['useSecurityLevel'] : false;

        $accountId = \laabs::getToken("AUTH")->accountId;
        $account = \laabs::callService("auth/userAccount/read_userAccountId_", $accountId);

        if (!is_null($account->securityLevel)
            && $account->securityLevel != ""
            && $hasSecurityLevel
        ) {
            $whatAmI = $account->securityLevel;
        } elseif ($hasSecurityLevel) {
            $whatAmI = 'userWithoutSecurityLevelYet';
        } else {
            $whatAmI = 'userWithoutSecurityLevel';
        }

        $sizeRoles = count($roles);
        for ($i = 0; $i < $sizeRoles; $i++) {
            if (is_null($user->securityLevel) ||  $user->securityLevel == "") {
                continue;
            }
            if ($roles[$i]->securityLevel != $user->securityLevel) {
                unset($roles[$i]);
            }
        }

        $view->setSource('whatAmI', $whatAmI);
        $view->setSource('allowUserModification', true);
        $view->setSource('roles', $roles);
        $view->setSource('user', $user);
        $view->setSource('restrictRoles', $publicArchives || $restrictUserRoles);
        $userPositions = \laabs::callService("organization/organization/readAccountpositions_accountId_", $user->accountId);

        $view->setSource('userPositions', $userPositions);

        $view->merge();
        $view->translate();

        return $view->saveHtml();
    }

    /**
     * View for the edit user profile form
     * @param user /user $user The user object
     *
     * @return string The html view string
     */
    public function editProfile($user)
    {
        $user->roles = json_encode($user->roles);

        $roles = $this->sdoFactory->find('auth/role');

        $view = $this->view;

        $view->addContentFile("auth/userAccount/profile/edit.html");

        $allowUserModification = true;
        if (isset(\laabs::configuration('auth')['allowUserModification'])) {
            $allowUserModification = (bool)\laabs::configuration('auth')['allowUserModification'];
        }

        $view->setSource('allowUserModification', $allowUserModification);
        $view->setSource('roles', $roles);
        $view->setSource('profile', true);
        $view->setSource('user', $user);

        $view->merge();
        $view->translate();

        return $view->saveHtml();
    }

    /**
     * View for the create user form
     * @param object $user The user object
     *
     * @return string The html view string
     */
    public function newUser($user)
    {
        $view = $this->view;

        $view->addContentFile("auth/userAccount/admin/edit.html");

        $roles = $this->sdoFactory->find('auth/role');

        $restrictUserRoles = isset(\laabs::configuration('auth')['restrictUserRoles']) && \laabs::configuration('auth')['restrictUserRoles'];
        $publicArchives = isset(\laabs::configuration('presentation.maarchRM')['publicArchives']) && \laabs::configuration('presentation.maarchRM')['publicArchives'];
        $accountId = \laabs::getToken("AUTH")->accountId;
        $account = \laabs::callService("auth/userAccount/read_userAccountId_", $accountId);

        if (!is_null($account->securityLevel) &&  $account->securityLevel != "") {
            $whatAmI = $account->securityLevel;
        } else {
            $whatAmI = 'userWithoutSecurityLevel';
        }

        $sizeRoles = count($roles);
        for ($i = 0; $i < $sizeRoles; $i++) {
            if (is_null($account->securityLevel) ||  $account->securityLevel == "") {
                continue;
            }
            if ($whatAmI == \bundle\auth\Model\account::SECLEVEL_GENADMIN
                && $roles[$i]->securityLevel !== \bundle\auth\Model\account::SECLEVEL_FUNCADMIN) {
                unset($roles[$i]);
            } elseif ($whatAmI == \bundle\auth\Model\account::SECLEVEL_FUNCADMIN
                && $roles[$i]->securityLevel !== \bundle\auth\Model\account::SECLEVEL_USER) {
                unset($roles[$i]);
            }
        }

        $view->setSource('whatAmI', $whatAmI);
        $view->setSource('allowUserModification', true);
        $view->setSource('roles', $roles);
        $view->setSource('restrictRoles', $publicArchives || $restrictUserRoles);
        $view->setSource('user', $user);
        $view->setSource('userPositions', false);


        $view->merge();
        $view->translate();

        return $view->saveHtml();
    }

    /**
     * View to see information on user
     * @param object $user The user object
     *
     * @return string The html view string
     */
    public function visualisation($user)
    {
        $view = $this->view;

        $view->addContentFile("auth/userAccount/admin/visualisation.html");

        $orgModel = \laabs::newInstance("organization/organization");

        $organizations = $orgModel->getOrganizationTree();
        $this->mergeOrganizations($organizations);

        $view->setSource('user', $user);

        $view->merge();
        $view->translate();

        return $view->saveHtml();
    }

    /**
     * undocumented function
     *
     * @return void
     **/
    protected function mergeOrganizations($organizations)
    {
        $orgList = $this->view->getElementById("organizationList");

        foreach ($organizations as $organization) {
            $orgFragment = $this->view->createDocumentFragment();
            $orgFragment->appendHtmlFile("organization/organizationItem.html");

            $this->view->merge($orgFragment, $organization);

            $orgItem = $orgList->appendChild($orgFragment);

            $this->mergeOrgUnits($organization, $orgItem);
        }
    }

    protected function mergeOrgUnits($parent, $container)
    {
        $orgUnitList = $this->view->createElement('ul');
        $container->appendChild($orgUnitList);

        foreach ($parent->orgUnit as $orgUnit) {
            $orgUnitFragment = $this->view->createDocumentFragment();
            $orgUnitFragment->appendHtmlFile("organization/orgUnitItem.html");
            $this->view->merge($orgUnitFragment, $orgUnit);

            $orgUnitItem = $orgUnitList->appendChild($orgUnitFragment);

            $this->mergeOrgUnits($orgUnit, $orgUnitItem);
        }
    }

    //JSON

    /**
     * undocumented function
     *
     * @return void
     */
    public function addUser($user)
    {
        $json = $this->json;
        $json->message = "User added";
        $json->message = $this->translator->getText($json->message);

        return $json->save();
    }

    public function lock()
    {
        $json = $this->json;
        $json->message = "User locked";
        $json->message = $this->translator->getText($json->message);

        return $json->save();
    }

    public function unlock()
    {
        $json = $this->json;
        $json->message = "User unlocked";
        $json->message = $this->translator->getText($json->message);

        return $json->save();
    }

    public function enable()
    {
        $json = $this->json;
        $json->message = "User enable";
        $json->message = $this->translator->getText($json->message);

        return $json->save();
    }

    public function disable()
    {
        $json = $this->json;
        $json->message = "User disable";
        $json->message = $this->translator->getText($json->message);

        return $json->save();
    }

    public function setPassword()
    {
        $json = $this->json;
        $json->message = "Password has been changed";
        $json->message = $this->translator->getText($json->message);

        return $json->save();
    }

    /**
     * Generate reset token
     * @return string
     */
    public function forgotAccount()
    {
        $json = $this->json;
        $json->message = "If the account exists, a reset email has been send";
        $json->message = $this->translator->getText($json->message);

        return $json->save();
    }

    /**
     * Form to change the password
     * @return string
     */
    public function formChangePassword()
    {
        $view = $this->view;

        $view->addContentFile("auth/userAccount/login/changePassword.html");

        $view->translate();

        return $view->saveHtml();
    }

    /**
     * Reset password
     * @return string
     */
    public function resetPassword()
    {
        $json = $this->json;
        $json->message = "Password has been updated";
        $json->message = $this->translator->getText($json->message);

        return $json->save();
    }

    public function requirePasswordChange()
    {
        $json = $this->json;
        $json->message = "Request to changed password sent.";
        $json->message = $this->translator->getText($json->message);

        return $json->save();
    }

    /**
     * Modify a user information
     *
     * @return array
     */
    public function updateUserInformation()
    {
        $this->json->message = "User updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * invalidUserInformationException
     * @param string $exception exception
     * @return void
     */
    public function invalidUserInformationException($exception)
    {
        $exception->message = $this->translator->getText($exception->message);
        $this->json->load($exception);
        $this->json->status = false;

        return $this->json->save();
    }

    /**
     * Serializer JSON for invalid status exception
     * @param Exception $exception The exception
     ** @return object JSON object with a status
     */
    public function noOrganizationException($exception)
    {
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());
        return $this->json->save();
    }

    /**
     * Serializer JSON for invalid status exception
     * @param Exception $exception The exception
     ** @return object JSON object with a status
     */
    public function userAlreadyExistException($exception)
    {
        $this->json->status = false;
        $this->json->message = $this->translator->getText($exception->getMessage());
        return $this->json->save();
    }

}
