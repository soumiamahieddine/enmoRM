<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle digitalResource.
 *
 * Bundle digitalResource is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle digitalResource is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\digitalResource\Controller;

/**
 * Class for content type
 *
 * @package DigitalResource
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 */
class contentType
{

    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Allow to display all repositories
     *
     * @return digitalResource/contentType[] Array of digitalResource/contentType object
     */
    public function index()
    {
        return $this->sdoFactory->find("digitalResource/contentType");
    }

    /**
     * Edit a repository
     * @param string $name
     *
     * @return digitalResource/contentType contentType object
     */
    public function get($name)
    {
        return $this->sdoFactory->read("digitalResource/contentType", $name);
    }

    /**
     * create a repository
     * @param digitalResource/contentType $contentType The contentType object
     *
     * @return boolean The result of the operation
     */
    public function create($contentType)
    {
        $this->sdoFactory->create($contentType, 'digitalResource/contentType');

        return true;
    }

    /**
     * update a repository
     * @param digitalResource/contentType $contentType The contentType object
     *
     * @return boolean The result of the operation
     */
    public function update($contentType)
    {
        try {
            $this->sdoFactory->update($contentType, 'digitalResource/contentType');
        } catch (\Exception $e) {
            throw new \core\Exception\BadRequestException();
        }

        return true;
    }
}
