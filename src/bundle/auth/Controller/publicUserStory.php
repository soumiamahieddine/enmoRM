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

namespace bundle\auth\Controller;

/**
 * Controler for the publicUserStory
 *
 * @package Auth
 * @author Alexandre Morin <alexandre.morin@maarch.org>
 */
class publicUserStory
{

    public $sdoFactory;

    /**
     * Constructor of adminRole class
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }


    /**
     * List all user story public
     *
     * @return auth/publicUserStory The requested role
     */
    public function index()
    {
        $return = array();

        $publicUserStories = $this->sdoFactory->find("auth/publicUserStory");
        foreach ($publicUserStories as $publicUserStories) {
            $return[] = $publicUserStories->userStory;
        }

        return $return;
    }

    /**
     * Recorde a new user story public
     * @param auth/publicUserStory $publicUserStory The user story public object to create
     *
     * @return boolean The status of the query
     */
    public function create($publicUserStory)
    {
        $publicUserStory = \laabs::cast("auth/publicUserStory", $publicUserStory);

        return $this->sdoFactory->create($publicUserStory);
    }


    /**
     * Delete an user story public
     * @param auth/publicUserStory $publicUserStory The user story public object to delete
     *
     * @return boolean The status of the query
     */
    public function delete($publicUserStory)
    {
        $publicUserStory = \laabs::cast("auth/publicUserStory", $publicUserStory);

        return $this->sdoFactory->delete($publicUserStory);
    }
}
