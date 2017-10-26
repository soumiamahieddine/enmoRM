<?php

/**
 * Interface file for Laabs App Repository handlers
 * @package core\Repository
 */

namespace core\Repository;

/**
 * Interface for Laabs App Repository handlers
 */

interface RepositoryInterface
{

    /* constants */

    /* Properties */

    /* Methods */
    /**
     * Open an instance data container
     * @param mixed  $savePath   The information about the data storage for the instance
     * @param string $instanceId The identifier for the instance
     */
    public function open($savePath, $instanceId);

    /**
     * Close the instance data container
     */
    public function close();

    /**
     * Read the instance data container for a specified instance identifier
     * @param string $instanceId The instance identifier
     */
    public function read($instanceId);

    /**
     * Write into the instance data container for a specified instance identifier
     * @param string $instanceId   The instance identifier
     * @param mixed  $instanceData The instance data
     */
    public function write($instanceId, $instanceData);

    /**
     * Destroy the instance data container for a specified instance identifier
     * called by Instance::regenerate_id($destroy=true) and Instance::destroy()
     * @param string $instanceId The instance identifier
     */
    public function destroy($instanceId);

    /**
     * Garbage collector for instance data storage
     * @param int $maxlifetime The max life time of instance to collect in seconds
     */
    public function gc($maxlifetime);

    /**
     * Scan for instance with filter
     * @param string $path   The path to the repository
     * @param string $filter A string value to compare to isntance id
     * 
     * @return array The array of instance ids
     */
    public function scan($path, $filter=false);

}