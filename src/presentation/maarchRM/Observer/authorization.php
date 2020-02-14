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
    protected $securityLevelUserStories;
    protected $securityLevel;
    protected $hasSecurityLevel;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userAccountController = \laabs::newController('auth/userAccount');

        if (isset(\laabs::configuration('auth')['blacklistUserStories'])) {
            $this->blacklistUserStories = \laabs::configuration('auth')['blacklistUserStories'];
        } else {
            $this->blacklistUserStories = null;
        }

        $this->hasSecurityLevel = isset(\laabs::configuration('auth')['useSecurityLevel']) ? (bool) \laabs::configuration('auth')['useSecurityLevel'] : false;

        if (isset(\laabs::configuration('auth')['privileges'])
            && isset(\laabs::configuration('auth')['securityLevel'])
            && $this->hasSecurityLevel
        ) {
            $this->securityLevelUserStories = \laabs::configuration('auth')['privileges'];
            $this->securityLevel = \laabs::configuration('auth')['securityLevel'];
        } else {
            $this->securityLevel = null;
            $this->securityLevelUserStories = null;
        }
    }

    /**
     * Check user privilege against requested route
     *
     * @param array &$userStories The reflection of user stories
     * @param array &$args        The arguments
     *
     * @return boolean
     *
     * @subject LAABS_USER_STORY
     */
    public function filterPrivilege(&$userStories, array &$args = null)
    {
        $account = \laabs::getToken('AUTH');
        $accountSecurityLevels = $this->getAccountSecurityRole($account);

        foreach ($userStories as $i => $userStory) {
            if (is_array($this->blacklistUserStories)) {
                foreach ($this->blacklistUserStories as $blacklistUserStory) {
                    if (fnmatch($blacklistUserStory, $userStory->uri)) {
                        unset($userStories[$i]);
                    }
                }
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

            if (!is_null($this->securityLevelUserStories)
                && !is_null($account)
            ) {
                $hasPrivilege = false;
                $domain = strtok($userStory->uri, LAABS_URI_SEPARATOR);
                // if value is set to null or false in database after upgrade from 2.5 version
                if (empty($accountSecurityLevels)) {
                    $hasPrivilege = true;
                }

                foreach ($accountSecurityLevels as $accountSecurityLevel) {
                    if (!isset($this->securityLevel[$accountSecurityLevel])) {
                        throw new \core\Exception("User has an unknown security level");
                    }
                    $value = $this->securityLevel[$accountSecurityLevel];
                    if ($value === '0') {
                        $bitmask = ['1', '2', '4'];
                    } elseif ($value === '3') {
                        $bitmask = ['1', '2'];
                    } elseif ($value === '6') {
                        $bitmask = ['4', '2'];
                    } else {
                        $bitmask = [$value];
                    }

                    foreach ($bitmask as $j) {
                        if ($domain === 'app') {
                            $hasPrivilege = true;
                            continue 2;
                        }

                        if (in_array($domain . '/', $this->securityLevelUserStories[$j])) {
                            $hasPrivilege = true;
                            continue 2;
                        }

                        foreach ($this->securityLevelUserStories[$j] as $securityLevelUserStory) {
                            if (fnmatch($securityLevelUserStory, $userStory->uri)) {
                                $hasPrivilege = true;
                                continue 3;
                            }
                        }
                    }
                }
                if (!$hasPrivilege) {
                    unset($userStories[$i]);
                }
            }
        }
    }

    private function getAccountSecurityRole($account)
    {
        if (!$account) {
            return false;
        }

        $securityRole = [];
        $roleMembers = \laabs::callService("auth/roleMember/readByuseraccount_userAccountId_", $account->accountId);
        foreach ($roleMembers as $roleMember) {
            $r = \laabs::callService("auth/role/read_roleId_", $roleMember->roleId);
            if ($r->securityLevel) {
                $securityRole[] = $r->securityLevel;
            }
        }

        return $securityRole;
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
