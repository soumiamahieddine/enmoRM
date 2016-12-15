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
 * Class message that represents a digital resource cluster
 *
 * @package Digitalresource
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class cluster
{
    /**
     * The universal identifier
     *
     * @var id
     */
    public $clusterId;

    /**
     * The cluster name
     *
     * @pattern #^[A-Za-z_][A-Za-z0-9_]*$#
     * @var string
     */
    public $clusterName;

    /**
     * The cluster description
     *
     * @var string
     */
    public $clusterDescription;

    /**
     * The cluster repositories
     *
     * @var digitalResource/clusterRepository[]
     */
    public $clusterRepository;
}
