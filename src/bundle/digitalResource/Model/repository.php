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
namespace bundle\digitalResource\Model;

/**
 * Class model that represents a digital resource repository
 *
 * @package Digitalresource
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 *
 * @pkey [repositoryId]
 * @key [repositoryReference]
 */
class repository
{
    /**
     * The repository identifier
     *
     * @var id
     */
    public $repositoryId;

    /**
     * The repository name
     *
     * @var string
     */
    public $repositoryName;

    /**
     * The repository reference
     *
     * @var name
     */
    public $repositoryReference;

    /**
     * The repository adapter (type)
     *
     * @var string
     */
    public $repositoryType;

    /**
     * The repository name
     * It may be the root path on a filesystem, a datasource name for database, the IP/url, etc.
     *
     * @var string
     */
    public $repositoryUri;

    /**
     * The repository max ussable size
     *
     * @var integer
     */
    public $maxSize;
    /**
     * The repository status (enabled/disabled)
     *
     * @var boolean
     */
    public $enabled;

    /**
     * The repository parameters
     *
     * @var string
     */
    public $parameters;

    /**
     * The repository service
     *
     * @var dependency/repository/RepositoryInterface
     * @access protected
     */
    protected $service;


    /**
     * Setter for the repository service
     * @param object $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }

    /**
     * Getter for the repository service
     * @return object
     */
    public function getService()
    {
        return $this->service;
    }
} // END class address
