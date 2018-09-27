<?php

/*
 * This file is part of the registeredMail package.
 *
 * (c) Maarch
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace presentation\maarchRM;

/**
 * Dashboard html serializer
 *
 * @package MaarchRM
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class dashboard
{
    public $storage;

    public $userPrivileges = array();

    /**
     * Constructor of dashboard
     * @param array  $menu        Menu of the dashboard
     * @param string $logo        Logo URI
     * @param string $navbarTitle The configuration of application name
     * @param string $title       The configuration of title
     * @param string $favicon     The configuration of favicon
     * @param string $css         The configuration of css
     */
    public function __construct(array $menu = null, $logo = "/presentation/img/maarch_box_outline.png", $navbarTitle = false, $title = "Maarch RM", $favicon = "/presentation/img/favicon.ico" , $css = "/presentation/css/style.css")
    {
        $this->storage = new \stdClass();

        if ($accountToken = \laabs::getToken('AUTH')) {
            $userAccountController = \laabs::newController('auth/userAccount');
            $user = $userAccountController->get($accountToken->accountId);

            $this->storage->user = $user;

            $userPositionController = \laabs::newController('organization/userPosition');
            $this->storage->positions = $userPositionController->getMyPositions();

            $this->userPrivileges = \laabs::callService('auth/userAccount/read_userAccountId_Privileges', $user->accountId);
        }

        if ($currentOrganization = \laabs::getToken("ORGANIZATION")) {
            $this->storage->currentOrganization = $currentOrganization;
        }

        $this->storage->menu = $this->filterMenuAuth($menu);

        $this->storage->logo = $logo;
        $this->storage->navbarTitle = $navbarTitle;
        $this->storage->title = $title;
        $this->storage->favicon = $favicon;
        $this->storage->css = $css;
        $this->storage->version = \laabs::getVersion();
        $this->storage->licence = \laabs::getLicence();
    }

    /**
     * dashboard layout merge
     *
     * @return object
     */
    public function layout()
    {
        return $this->storage;
    }

    public function filterMenuAuth($menu)
    {
        foreach ($menu as $i => $item) {
            if (isset($item['submenu'])) {
                //var_dump("go to submenu of " . $item['label']);
                $menu[$i]['submenu'] = $this->filterMenuAuth($item['submenu']);
                if (count($menu[$i]['submenu']) < 1) {
                    unset($menu[$i]);
                }
            } else {
                $parser = parse_url($item['href']);
                if (isset($parser['scheme'])) {
                    continue;
                }
                try {
                    $command = \laabs::command('READ', substr($parser['path'], 1));
                    if (!$this->hasUserPrivilege($command)) {
                        unset($menu[$i]);
                    }
                } catch (\Exception $e) {
                    unset($menu[$i]);
                }
            }
        }

        return $menu;
    }

    protected function hasUserPrivilege($command)
    {
        if (isset($command->tags['requires'])) {
            if (!$this->checkRequirements($command)) {
                return;
            }
        }

        foreach ($this->userPrivileges as $userPrivilege) {
            if (fnmatch($userPrivilege, $command->userStory)) {
                return true;
            }
        }
    }

    protected function checkRequirements($command) 
    {
        // All requirements must be fulfilled
        foreach ($command->tags['requires'] as $requirement) {
            $requirement = array_map('trim', explode(',', substr($requirement, 1, -1)));
            if (!$this->checkRequirement($requirement)) {
                return;
            }
        }

        return true;
    }

    protected function checkRequirement($requirement) 
    {
        // At least one requirement must be fulfilled
        foreach ($requirement as $requirementItem) {
            if (substr($requirementItem, -2) == '/*') {
                foreach ($this->userPrivileges as $userPrivilege) {
                    $domain = explode('/', $userPrivilege)[0].'/?';
                    if (fnmatch($requirementItem, $domain)) {
                        return true;
                    }
                }
            }

            foreach ($this->userPrivileges as $userPrivilege) {
                if (fnmatch($userPrivilege, $requirementItem)) {
                    return true;
                }
            }
        }
    }
}
