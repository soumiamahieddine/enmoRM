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
 * Class model that represents a digital resource cluster repository
 *
 * @package Digitalresource
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 * 
 * @pkey [clusterId, repositoryId]
 * @fkey [clusterId] digitalResource/cluster [clusterId]
 * @fkey [repositoryId] digitalResource/repository [repositoryId]
 * 
 */
class clusterRepository
{
    /**
     * The cluster's universal identifier
     *
     * @var id
     */
    public $clusterId;

    /**
     * The repository's universal identifier
     *
     * @var id
     */
    public $repositoryId;

    /**
     * The priority on writing
     *
     * @var integer
     */
    public $writePriority;

    /**
     * The priority on reading
     *
     * @var integer
     */
    public $readPriority;

    /**
     * The priority on deleting
     *
     * @var integer
     */
    public $deletePriority;

} // END class format 
