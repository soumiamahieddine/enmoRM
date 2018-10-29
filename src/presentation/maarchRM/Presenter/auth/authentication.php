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
 * user authentication html serializer
 *
 * @package User
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 */
class authentication
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    /**
     *
     */
    public $view;

    protected $json;
    protected $translator;

    /**
     * The URI of logo
     *
     * @var string
     **/
    protected $logoUri;

    /**
     * Constructor
     * @param object $view A new empty Html document
     */
    public function __construct(
            \dependency\html\Document $view,
            \dependency\json\JsonObject $json,
            \dependency\localisation\TranslatorInterface $translator,
            $logo, 
            $altLogo = null,
            $loginBackground = null
    ) {
        $this->view = $view;

        $this->logoUri = $logo;

        if (isset($altLogo)) {
            $this->loginLogoUri = $altLogo;
        } else {
            $this->loginLogoUri = $logo;
        }

        $this->json = $json;
        $this->translator = $translator;
        $this->translator->setCatalog('auth/messages');
        $this->json->status = true;

        $this->loginBackground = $loginBackground;
    }

    /**
     * View for the users admin index panel
     *
     * @return string The html view string
     */
    public function prompt()
    {
        $view = $this->view;

        $view->addContentFile("auth/userAccount/login/form.html");
        $view->setSource('logo', $this->loginLogoUri);
        $view->setSource('loginBackground', $this->loginBackground);
        $view->translate();
        $view->merge();

        return $view->saveHtml();
    }

    /**
     * Log out -> login
     *
     * @return void
     **/
    public function logout()
    {
        $this->view->addHeaders();
        $this->view->addContent("<script type='application/javascript'>$(location).attr('href', '/');</script>");

        return $this->view->saveHtml();
    }

    //JSON
    public function login()
    {
        $json = $this->json;
        $json->message = $this->translator->getText("User connected");

        return $json->save();
    }

    public function definePassword($requestPath)
    {
        $json = $this->json;
        $json->message = "Password changed.";
        $json->requestPath = $requestPath;

        return $json->save();
    }

    public function authenticationException($exception)
    {
        $code = $exception->getCode();
        if (!empty($code)) {
            \laabs::setResponseCode($exception->getCode());
        }

        $json = $this->json;
        $json->status = false;

        $exception->setMessage($this->translator->getText($exception->getFormat()));
        $json->message = $exception->getMessage();

        return $json->save();
    }

    public function userDisabledException()
    {
        $json = $this->json;
        $json->status = false;
        $json->message = $this->translator->getText("User is disabled");

        return $json->save();
    }

    public function samePasswordException($exception)
    {
        $json = $this->json;
        $json->status = false;
        $json->message = $this->translator->getText($exception->getMessage());

        return $json->save();
    }

    public function userLockException()
    {
        $json = $this->json;
        $json->status = false;
        $json->message = $this->translator->getText("User locked");

        return $json->save();
    }

    public function userPasswordChangeRequestException()
    {
        $json = $this->json;
        $json->status = false;
        $json->passwordChangeRequired = true;

        return $json->save();
    }

    public function noPrivilege()
    {
        $this->view->addContentFile("auth/authorization/noPrivilege.html");

        $result = false;

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            $result = true;
        }

        $this->view->setSource('asyncRequest', $result);
        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }
}
