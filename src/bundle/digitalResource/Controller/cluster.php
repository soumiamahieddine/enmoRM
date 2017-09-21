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
 * Class of cluster
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class cluster
{

    protected $sdoFactory;
    protected $repositoryController;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;

        $this->repositoryController = \laabs::newController('digitalResource/repository');
    }

    /**
     * Allow to display all clusters
     *
     * @return digitalResource/cluster[]
     */
    public function index()
    {
        $clusters = $this->sdoFactory->find('digitalResource/cluster');
        foreach ($clusters as $cluster) {
            $cluster->clusterRepository = $this->sdoFactory->readChildren("digitalResource/clusterRepository", $cluster);
            $cluster->size = new \stdClass();

            $cluster->size->bSize = 0;
            $cluster->size->gbSize = 0;

            $digitalResources = $this->sdoFactory->readChildren('digitalResource/digitalResource', $cluster);
            foreach ($digitalResources as $digitalResource) {
                $cluster->size->bSize += $digitalResource->size;

                if ($cluster->size->bSize >= 1073741824) {
                    $cluster->size->gbSize ++;
                    $cluster->size->bSize -= 1073741824;
                }
            }
        }

        return $clusters;
    }

    /**
     * New empty cluster with default values
     *
     * @return digitalResource/cluster The cluster object
     */
    public function newCluster()
    {
        return \laabs::newInstance("digitalResource/cluster");
    }

    /**
     * Edit a cluster
     * @param string $clusterId The identifier of cluster
     *
     * @return digitalResource/cluster The cluster object
     */
    public function edit($clusterId = null)
    {
        // pre_load values
        if ($clusterId) {
            if (!$this->sdoFactory->exists("digitalResource/cluster", $clusterId)) {
                throw \laabs::newException("digitalResource/clusterException", "Cluster $clusterId not found.");
            }
            $cluster = $this->sdoFactory->read("digitalResource/cluster", $clusterId);
            if (!$cluster) {
                throw \laabs::newException("digitalResource/clusterException", "Cluster".$clusterId." unknow.");
            }

            $cluster->clusterRepository = $this->sdoFactory->readChildren("digitalResource/clusterRepository", $cluster);
        } else {
            $cluster = $this->newCluster();
        }

        return $cluster;
    }

    /**
     * create a cluster
     * @param digitalResource/cluster $cluster The cluster object
     *
     * @return boolean
     */
    public function create($cluster)
    {
        $cluster->clusterId = \laabs::newId();

        if (!$cluster->clusterName) {
            throw \laabs::newException("digitalResource/clusterException", "Cluster name is empty");
        }

        if (!$cluster->clusterRepository) {
            throw \laabs::newException("digitalResource/clusterException", "Cluster repository is empty");
        }

        foreach ($cluster->clusterRepository as $repo) {
            $repo->clusterId = $cluster->clusterId;
        }

        $this->sdoFactory->beginTransaction();
        try {
            $this->sdoFactory->create($cluster, "digitalResource/cluster");
            $this->sdoFactory->createCollection($cluster->clusterRepository, "digitalResource/clusterRepository");
        } catch (\Exception $e) {
            $this->sdoFactory->rollback();
            throw \laabs::newException("digitalResource/clusterException", "Cluster '$clusterId' not created.");
        }
        $this->sdoFactory->commit();

        return true;
    }

    /**
     * update a repository
     * @param digitalResource/cluster $cluster The cluster object
     *
     * @return boolean
     */
    public function update($cluster)
    {
        $this->sdoFactory->beginTransaction();
        try {
            $this->sdoFactory->update($cluster, "digitalResource/cluster");
            $this->sdoFactory->deleteChildren("digitalResource/clusterRepository", $cluster, "digitalResource/cluster");
            if (count($cluster->clusterRepository) > 0) {
                $this->sdoFactory->createCollection($cluster->clusterRepository, "digitalResource/clusterRepository");
            }
        } catch (\core\Route\Exception $e) {
            $this->sdoFactory->rollback();
            throw \laabs::newException("digitalResource/clusterException", "Cluster '$clusterId' not updated.");
        }
        $this->sdoFactory->commit();

        return true;
    }

    /**
     * Open cluster and repository services for given mode
     * @param string $clusterId The cluster identifier
     * @param string $mode      The mode: read, write, delete
     * @param bool   $limit     Limit the repo services to the lowest priority
     *
     * @return object The cluster with repositories and repo services
     */
    public function openCluster($clusterId, $mode = "read", $limit = false)
    {
        $cluster = $this->sdoFactory->read("digitalResource/cluster", $clusterId);

        $cluster->clusterRepository = $this->sdoFactory->readChildren("digitalResource/clusterRepository", $cluster);

        $this->sortClusterRepositories($cluster, $mode, $limit);

        foreach ($cluster->clusterRepository as $clusterRepository) {
            try {
                $clusterRepository->repository = $this->repositoryController->openRepository($clusterRepository->repositoryId);
            } catch (\Exception $e) {
                $clusterRepository->repository = null;
                continue;
            }
        }

        /*
        $operationnalClusterRepository = [];
        foreach ($cluster->clusterRepository as $clusterRepository) {
            try {
                $clusterRepository->repository = $this->repositoryController->openRepository($clusterRepository->repositoryId);
            } catch (\Exception $e) {
                // to do (log etc..)
                continue;
            }
            $operationnalClusterRepository[] = $clusterRepository;
        }

        $cluster->clusterRepository = $operationnalClusterRepository;
        */

        return $cluster;
    }

    /**
     * Sort repositories with their priority for the given operation
     * @param object  $cluster The cluster definition
     * @param string  $mode    The operation: read, write, delete
     * @param boolean $limit   Only keep repositories with the lowest priority
     */
    public function sortClusterRepositories($cluster, $mode = "read", $limit = false)
    {
        // Sort repositories by write priority
        $priorityProperty = $mode.'Priority';
        $priority = array();
        foreach ($cluster->clusterRepository as $key => $clusterRepository) {
            $priority[$key] = $clusterRepository->$priorityProperty;
        }
        array_multisort($priority, SORT_ASC, SORT_NUMERIC, $cluster->clusterRepository);

        // Get lowest priority and unset other repositories
        if ($limit) {
            $firstRepository = reset($cluster->clusterRepository);
            $lowestPriority = $firstRepository->$priorityProperty;
            foreach ($cluster->clusterRepository as $index => $clusterRepository) {
                if ($clusterRepository->$priorityProperty > $lowestPriority) {
                    unset($cluster->clusterRepository[$index]);
                }
            }

            if (count($cluster->clusterRepository) == 0) {
                throw \laabs::newException("digitalResource/noClusterRepositoryException", "No repository for '$mode' mode");
            }
        }
    }

    /**
     * Create a ressource container on the cluster (after opening it)
     * @param object $cluster
     * @param string $path
     * @param mixed  $metadata
     * 
     * @return array
     */
    public function openContainers($cluster, $path, $metadata=null)
    {
        foreach ($cluster->clusterRepository as $index => $clusterRepository) {
            if ($clusterRepository->repository == null) {
                throw \laabs::newException("digitalResource/clusterException", "All repositories must be accessible");
            }

            $realPath = $this->repositoryController->openContainer($clusterRepository->repository, $path, $metadata);

            if (!$realPath) {
                throw \laabs::newException("digitalResource/clusterException", "Container ".$path." counld not be opened.");
            }
        }
    }

    /**
     * Store a resource on cluster (after opening it)
     * @param digitalResource/cluster         $cluster
     * @param digitalResource/digitalResource $resource
     */
    public function storeResource($cluster, $resource)
    {
        foreach ($cluster->clusterRepository as $index => $clusterRepository) {
            if ($clusterRepository->repository == null) {
                throw \laabs::newException("digitalResource/clusterException", "All repositories must be accessible");
            }

            $address = $this->repositoryController->storeResource($clusterRepository->repository, $resource);

            if (!$address) {
                throw \laabs::newException("digitalResource/clusterException", $address." not found");
            }

            $resource->address[$index] = $address;
        }
    }

    /**
     * Rollback storage transaction
     * @param digitalResource/digitalResource $resource
     */
    public function rollbackStorage($resource)
    {
        if (count($resource->address)) {
            foreach ($resource->address as $address) {
                $this->repositoryController->rollbackStorage($address);
            }
        }
    }

    /**
     * Retrieve a resource contents on cluster (after opening it)
     * @param digitalResource/cluster         $cluster
     * @param digitalResource/digitalResource $resource
     *
     * @return bool
     */
    public function retrieveResource($cluster, $resource)
    {
        foreach ($cluster->clusterRepository as $clusterRepository) {
            if ($clusterRepository->repository == null) {
                continue;
            }
            $address = $this->sdoFactory->read("digitalResource/address", array('resId' => $resource->resId, 'repositoryId' => $clusterRepository->repositoryId));
            if ($address) {
                $contents = null;
                $resource->address[] = $address;
                $address->repository = $clusterRepository->repository;

                try {
                    $contents = $this->repositoryController->retrieveContents($clusterRepository->repository, $address);

                    if (isset($resource->hash) && !$this->checkHash($address, $resource, $contents)) {
                        throw \laabs::newException("digitalResource/clusterException", "Invalid hash for resource ".$resource->resId." at address ".$address->repository->repositoryUri.DIRECTORY_SEPARATOR.$address->path);
                    }

                    $resource->setContents($contents);

                    return $contents;

                } catch (\Exception $e) {
                    // No content retrieved : send error as audit event
                    \laabs::notify(LAABS_BUSINESS_EXCEPTION, $e);
                }
            } else {
                \laabs::notify(LAABS_BUSINESS_EXCEPTION, \laabs::newException("digitalResource/clusterException", "No address found for ressource ".$resource->resId));
            }
        }

        // TODO : throw exception if resource not available on repo, based on options ?
        return null;
    }

    /**
     * Verify a resouce
     *
     * @param type $cluster  The cluster object where the resource is store
     * @param type $resource The digitalResource object to verify
     *
     * @return digitalResource/digitalResource The digitalResouce verify
     */
    public function verifyResource($cluster, $resource)
    {
        $result = true;

        foreach ($cluster->clusterRepository as $clusterRepository) {
            if ($clusterRepository->repository == null) {
                continue;
            }
            $resource->address = $this->sdoFactory->find("digitalResource/address", "resId='".$resource->resId."'");

            foreach ($resource->address as $address) {
                $address->repository = $clusterRepository->repository;
                $contents = $this->repositoryController->retrieveContents($clusterRepository->repository, $address);

                if ($contents) {
                    $result = $result && $this->checkHash($address, $resource, $contents);
                }
            }
        }

        return $result;
    }

    private function checkHash($address, $resource, $contents)
    {
        $hash = strtolower(hash($resource->hashAlgorithm, $contents));

        $address->lastIntegrityCheck = \laabs::newTimestamp();
        $address->integrityCheckResult = false;

        if ($hash == strtolower($resource->hash)) {
            $address->integrityCheckResult = true;
        }
        $this->sdoFactory->update($address);

        return $address->integrityCheckResult;
    }
}
