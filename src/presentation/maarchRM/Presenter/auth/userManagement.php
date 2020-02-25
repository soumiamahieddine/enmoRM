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
 * user management html serializer
 *
 * @package User
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class userManagement
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    /**
     *
     */
    public $view;

    protected $json;
    protected $translator;


    /**
     * Constructor
     * @param object $view A new empty Html document
     */
    public function __construct(
        \dependency\html\Document $view,
        \dependency\json\JsonObject $json,
        \dependency\localisation\TranslatorInterface $translator
    ) {
        $this->view = $view;
        $this->json = $json;
        $this->translator = $translator;
        $this->translator->setCatalog('auth/authenticationMessages');
        $this->json->status = true;
    }

    /**
     * View for the users to display
     * @param array $users An array of user objects to display
     *
     * @return string The html view string
     */
    public function indexJson($users)
    {
        return json_encode($users);
    }

    /**
     * View edit user profil
     * @param user/userInformation $user User object to display
     *
     * @return string The html view string
     */
    public function editUserInformation($user)
    {
        $view = $this->view;
        //$view->addHeaders();
        //$view->useLayout();
        $view->addContentFile("user/userManagement/userInformation.html");

         //loading of the picture
        if ($user->picture != null) {
            $content = stream_get_contents($user->picture);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $content, FILEINFO_MIME_TYPE);
            $user->picture = "data:".$mimeType.";base64,".base64_encode($content);
        }

        $view->setSource('user', $user);

        $view->merge();
        $view->translate();

        return $view->saveHtml();
    }

    /**
     * View edit user password
     *
     * @return string The html view string
     */
    public function editUserPassword()
    {
        $view = $this->view;
        $view->addHeaders();
        $view->useLayout();
        $view->addContentFile("presentation/user/userManagement/userPasswordChange.html");

        $view->setSource('userName', $_SESSION['user']['user']->userName);
        $view->merge();
        $view->translate();

        return $view->saveHtml();
    }

    //JSON
    /**
     * Org unit users typeahead
     * @param array $users An array of users matching the user query
     *
     * @return string
     **/
    public function queryUsers($users)
    {
        return json_encode($users);
    }
}
