<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of presentation maarchRM.
 *
 * presentation maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * presentation maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with presentation maarchRM.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Observer;

/**
 * Service for authorization check observer
 *
 * @package MaarchRM
 * @author  Maarch Cyril  VAZQUEZ <cyril.vazquez@maarch.org>
 */
class authorization
{
    
    protected $userAccountController;
    protected $blacklistUserStories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userAccountController = \laabs::newController('auth/userAccount');

        if(isset(\laabs::configuration('auth')['blacklistUserStories'])) {
            $this->blacklistUserStories = \laabs::configuration('auth')['blacklistUserStories'];
        } else {
            $this->blacklistUserStories = null;
        }

    }

    /**
     * Check user privilege against requested route
     * @param array &$userStories The reflection of user stories
     * @param array &$args        The arguments
     *
     * @return boolean
     *
     * @subject LAABS_USER_STORY
     */
    public function filterPrivilege(&$userStories, array &$args=null)
    {

        foreach ($userStories as $i => $userStory) {
            if (is_array($this->blacklistUserStories) && in_array($userStory->uri, $this->blacklistUserStories)) {
                unset($userStories[$i]);
            }

            if ($userStory->isPublic()) {
                continue;
            }

            if ($userStory->isPrivate()) {
                unset($userStories[$i]);
            }

            $hasPrivilege = $this->userAccountController->hasPrivilege($userStory->uri);

            if (!$hasPrivilege) {
                unset($userStories[$i]);
            }
        }
    }


    /**
     * Check user privilege against requested route
     * @param \core\Reflection\Command &$userCommand The reflection of requested user story command
     * @param array                    &$args        The arguments
     *
     * @return boolean
     *
     * @-subject LAABS_USER_COMMAND
     */
    /*public function checkPrivilege(&$userCommand, array &$args=null)
    {
        if (!$userCommand->pattern) {
            return true;
        }

        $userStory = \laabs::presentation()->getUserStory($userCommand->userStory);

        if ($userStory->isPublic()) {
            return true;
        }

        if ($userStory->isPrivate()) {
            return false;
        }

        $hasPrivilege = \laabs::callService('auth/userAccount/readHasprivilege', $userCommand->userStory);

        if (!$hasPrivilege) {
            $userCommand->reroute('app/app/readNoprivilege');
            for ($i = 0, $l = count($args); $i < $l; $i++) {
                unset($args[$i]);
            }

            return false;
        }

        return true;
    }*/
}
