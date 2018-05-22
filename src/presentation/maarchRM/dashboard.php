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
     */
    public function __construct(array $menu = null, $logo = "/presentation/img/maarch_box_outline.png", $navbarTitle = false, $title = "Maarch RM", $favicon = "/presentation/img/favicon.ico" ,$css)
    {
        $this->storage = new \stdClass();

        if ($accountToken = \laabs::getToken('AUTH')) {
            $user = \laabs::newController('auth/userAccount')->get($accountToken->accountId);

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

    protected function filterMenuAuth($menu)
    {
        foreach ($menu as $i => $item) {
            if (isset($item['submenu'])) {
                //var_dump("go to submenu of " . $item['label']);
                $menu[$i]['submenu'] = $this->filterMenuAuth($item['submenu']);
                if (count($menu[$i]['submenu']) < 1) {
                    unset($menu[$i]);
                }
            } else {
                if (substr($item['href'], 0, 7) != 'http://') {
                    $path = substr($item['href'], 1);
                    try {
                        $command = \laabs::command('READ', $path);
                        if (!$this->hasUserPrivilege($command->userStory)) {
                            unset($menu[$i]);
                        }
                    } catch (\Exception $e) {
                        unset($menu[$i]);
                    }
                }
            }
        }

        return $menu;
    }

    protected function hasUserPrivilege($userStory)
    {
        foreach ($this->userPrivileges as $userPrivilege) {
            if (fnmatch($userPrivilege, $userStory)) {
                return true;
            }
        }
    }
}
