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
 * Class message that represents an address for a digital resource in a repository
 *
 * @package Digitalresource
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class address
{
    /**
     * The universal resource identifier
     *
     * @var id
     */
    public $resId;

    /**
     * The repository identifier
     *
     * @var id
     */
    public $repositoryId;

    /**
     * The address of the resource in the repository
     * It may be the path on a filesystem, a record identifier in database, etc.
     *
     * @var string
     */
    public $path;

    /**
     * The date when the resource address was added
     *
     * @var timestamp
     */
    public $created;

    /**
     * Address is a packed resource in a package
     *
     * @var boolean
     */
    public $packed;

    /**
     * Check result
     *
     * @var boolean
     */
    public $integrityCheckResult;
}
