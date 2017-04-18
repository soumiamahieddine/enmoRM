<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency repository.
 *
 * Dependency repository is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency repository is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency repository.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\repository;
/**
 * Interface for repository
 *
 * @package Dependency\Repository
 * @author  Maarch Alexis Ragot <alexis.ragot@maarch.org>
 */
Interface RepositoryInterface
{
    /**
     * @const For Read bitmask, read data
     */
    const READ_DATA = 1;

    /**
     * @const For Read bitmask, read metadata
     */
    const READ_METADATA = 2;

    // CONTAINER
    /**
     * Delete a container
     * @param string $name     The name of container
     * @param mixed  $metadata The object or array of metadata
     * 
     * @return mixed The address/uri/identifier of created container on repository
     */
    public function createContainer($name, $metadata=null);

    /**
     * Update a container metadata
     * @param string $name     The name of container
     * @param mixed  $metadata The object or array of metadata
     * 
     * @return bool
     */
    public function updateContainer($name, $metadata);

    /**
     * Read a container metadata
     * @param string $name The name of container
     * 
     * @return mixed The object or array of metadata if available
     */
    public function readContainer($name);

    /**
     * Delete a container
     * @param string $name The name of container
     * 
     * @return bool
     */
    public function deleteContainer($name);

    // OBJECTS
    /**
     * Create a new object
     * @param string $data The contents to store
     * @param string $path The path to store
     * 
     * @return mixed The address/uri/identifier of stored resource on repository
     */
    public function createObject($data, $path);

    /**
     * Get a resource in repository
     * @param mixed $path The address/uri/identifier of stored resource on repository
     * 
     * @return string The contents of resource
     */
    public function readObject($path);

    /**
     * Update a resource in repository
     * @param mixed  $path The address/uri/identifier of stored resource on repository
     * @param string $data The contents to store. If ignored, only metadata will be updated
     * 
     * @return bool
     */
    public function updateObject($path, $data);

    /**
     * Delete resource in repository
     * @param mixed $path The address/uri/identifier of stored resource on repository
     * 
     * @return bool
     */
    public function deleteObject($path);
}