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
namespace bundle\digitalResource;

/**
 * API admin cluster digital resource
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface clusterInterface {

    /**
     * Allow to display all clusters
     *
     * @action digitalResource/cluster/index
     */
    public function readList();

    /**
     * Edit a cluster
     *
     * @action digitalResource/cluster/edit
     */
    public function read_clusterId_();

    /**
     * Create a new cluster
     * @param digitalResource/cluster $cluster The cluster object
     *
     * @action digitalResource/cluster/create
     */
    public function create($cluster);

    /**
     * Update an existing cluster
     * @param digitalResource/cluster $cluster The cluster object
     *
     * @action digitalResource/cluster/update
     */
    public function update($cluster);
}
