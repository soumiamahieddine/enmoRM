<?php

/*
 * Copyright (C) 2018 Maarch
 *
 * This file is part of dependency CSRF.
 *
 * Dependency CSRF is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency CSRF is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency CSRF.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace dependency\csrf;

class CsrfObserver
{
    /**
     * Whitelist for post methods
     * @var array
     */
    protected $whiteList;

    /**
     * Configuration object for CSRF protector
     * @var object
     */
    protected $config;

    public function __construct($whiteList = [], $config = null)
    {
        $this->whiteList = $whiteList;
        $this->config = $config;
    }

    /**
     * Observer for user authentication
     * @param $httpRequest
     *
     * @throws \alreadyInitializedException
     * @throws \configFileNotFoundException
     * @throws \incompleteConfigurationException
     *
     * @subject LAABS_REQUEST
     */
    public function observeRequest(&$httpRequest)
    {
        if (in_array($httpRequest->uri, $this->whiteList)) {
            return;
        }

        require_once "../dependency/csrf/libs/csrf/csrfprotector.php";
        \csrfProtector::init(null, null, $this->config);
    }
}