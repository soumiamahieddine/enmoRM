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
 * Serializer for authorization role administration in Html
 *
 * @package Auth
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class adminRole
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $json;

    /**
     * Constructor
     * @param \dependency\html\Document $view The default view document
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
     * View for role admin index panel
     * @param array $roles The list of roles
     *
     * @return string
     */
    public function index(array $roles)
    {
        $this->view->addContentFile("auth/authorization/index.html");

        $table = $this->view->getElementById("list");
        $dataTable = $table->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(1);
        $dataTable->setUnsearchableColumns(1);
        $dataTable->setUnsortableColumns(3);
        $dataTable->setUnsearchableColumns(3);

        $this->view->setSource('roles', $roles);
        $this->view->translate();
        $this->view->merge();

        return $this->view->saveHtml();
    }

    /**
     * View for new role
     * @param auth/role $role The new role
     *
     * @return string
     */
    public function newRole($role)
    {
        $role->privileges = array();
        $role->roleMembers = array();

        $publicUserStories = array();
        $publicUserStories[] = 'app/*';
        
        return $this->edit($role, $publicUserStories);
    }

    /**
     * View for role edition
     * @param auth/role $role              The role to administrate
     * @param array     $publicUserStories The public user stories
     *
     * @return string
     */
    public function edit($role = null, $publicUserStories = array())
    {
        $publicArchive = \laabs::configuration('presentation.maarchRM')['publicArchives'];
        $this->view->addContentFile("auth/authorization/edit.html");

        if (count($role->roleMembers) > 0) {
            $role->roleMembers = \laabs::callService('auth/userAccount/readIndex', "accountId=['".implode("', '", $role->roleMembers)."']");
        }

        $role->superadmin = false;
        if (isset($role->privileges[0]) && $role->privileges[0] == "*") {
            $role->superadmin = true;
        }

        // transform domain/userStory into an array
        // domain[userStory1, userStory2...]
        $userStoryDomains = array();
        $userStories = \laabs::presentation()->getUserStories();
        $userStoryNames = array();
        foreach ($userStories as $userStory) {
            $userStoryName = $userStory->getName();
            $userStoryNames[] = $userStoryName;

            foreach ($publicUserStories as $publicUserStory) {
                if (fnmatch($publicUserStory, $userStoryName)) {
                    continue 2;
                }
            }

            if (strpos($userStoryName, LAABS_URI_SEPARATOR) !== false) {
                $domain = strtok($userStoryName, LAABS_URI_SEPARATOR);
                $name = strtok(LAABS_URI_SEPARATOR);
            } else {
                $domain = 'app';
                $name = $userStoryName;
            }

            if (!isset($userStoryDomains[$domain])) {
                $userStoryDomains[$domain] = new \stdClass();
                $userStoryDomains[$domain]->name = $domain;
                $userStoryDomains[$domain]->privilegeStatus = false;

                if (!empty($role->privileges)) {
                    if (in_array($domain.'/', $role->privileges) || in_array($domain.'/*', $role->privileges)) {
                        $userStoryDomains[$domain]->privilegeStatus = true;
                    }
                }
            }

            $interface = new \stdClass();
            $interface->name = $name;
            $interface->value = $userStoryName;
            if (!empty($role->privileges)) {
                foreach ($role->privileges as $privilege) {
                    if ($userStory->isPublic()) {
                        $interface->status = true;
                    } elseif (fnmatch($privilege, $userStoryName)) {
                        $interface->status = true;
                    }
                }
            }

            $interface->parentStatus = $userStoryDomains[$domain]->privilegeStatus;

            $userStoryDomains[$domain]->userStory[] = $interface;

        }
        $this->view->setSource("userStories", $userStoryDomains);

        $this->view->setSource('publicArchive', $publicArchive);
        $this->view->setSource('role', $role);
        $this->view->merge();
        
        $this->view->translate();
        return $this->view->saveHtml();
    }

    // JSON
    /**
     * Serializer JSON for create method
     * 
     * @return object JSON object with a status and message parameters
     */
    public function create()
    {
        $this->json->message = "New role created.";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for update method
     * 
     * @return object JSON object with a status and message parameters
     */
    public function update()
    {
        $this->json->message = "Role updated.";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for delete method
     * 
     * @return object JSON object with a status and message parameters
     */
    public function delete()
    {
        $this->json->message = "Role deleted.";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for changeStatus method
     * 
     * @return object JSON object with a status and message parameters
     */
    public function changeStatus()
    {
        $this->json->message = "Status changed.";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for queryPersons method
     * @param array $persons An array of personParty matching the user query
     *
     * @return string
     * */
    public function queryPersons($persons)
    {
        return json_encode($persons);
    }

    //JSON
    /**
     * Exception
     * @param auth/Exception/adminRoleException $adminRoleException
     * 
     * @return string
     */
    public function adminRoleException($adminRoleException)
    {
        $this->json->load($adminRoleException);
        $this->json->status = false;

        return $this->json->save();
    }
}
