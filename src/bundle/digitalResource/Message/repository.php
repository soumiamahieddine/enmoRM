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
namespace bundle\digitalResource\Message;

/**
 * Class message that represents a digital resource repository
 *
 * @package Digitalresource
 * @author Alexis Ragot <alexis.ragot@maarch.org>
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
     * @notempty
     */
    public $repositoryName;

    /**
     * The repository reference
     *
     * @var name
     * @notempty
     */
    public $repositoryReference;

    /**
     * The repository adapter (type)
     *
     * @var string
     * @notempty
     */
    public $repositoryType;

    /**
     * The repository name
     * It may be the root path on a filesystem, a datasource name for database, the IP/url, etc.
     *
     * @var string
     * @notempty
     */
    public $repositoryUri;

    /**
     * The repository status (enabled/disabled)
     *
     * @var boolean
     */
    public $enabled;

    /**
     * The repository max ussable size
     *
     * @var integer
     */
    public $maxSize;

    /**
     * The repository parameters
     *
     * @var array
     */
    public $parameters;
}
