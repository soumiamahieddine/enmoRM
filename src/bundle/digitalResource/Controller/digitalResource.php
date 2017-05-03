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
 * Main digital resource controller
 */
class digitalResource
{

    protected $finfo;
    protected $sdoFactory;
    public $hashAlgorithm;

    /**
     * Cluster controller
     * @var digitalResource/Controller/format
     */
    protected $formatController;

    /**
     * Cluster controller
     * @var digitalResource/cluster
     */
    protected $clusterController;

    /**
     * Previously loaded digital resource clusters
     * @var array
     */
    protected $clusters;

    /**
     * Currently used ddigital resource cluster
     * @var digitalResource/cluster
     */
    protected $currentCluster;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory    The dependency sdo factory
     * @param string                  $hashAlgorithm The hash algorithm as in php::hash_algos()
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $hashAlgorithm = "sha256")
    {
        $this->hashAlgorithm = $hashAlgorithm;

        $this->finfo = new \finfo(FILEINFO_MIME_TYPE);

        $this->sdoFactory = $sdoFactory;

        $this->clusterController = \laabs::newController('digitalResource/cluster');

        $this->formatController = \laabs::newController('digitalResource/format');
    }

    /**
     * Get file information about one file
     * @param string $filename The file name
     *
     * @return digitalResource/digitalResource
     */
    public function createFromFile($filename)
    {
        if (!file_exists($filename)) {
            return false;
        }

        $contents = file_get_contents($filename);

        $resource = $this->newResource();

        // Basic path information
        $pathinfo = pathinfo($filename);
        $resource->fileName = $pathinfo['filename']."_".(string) \laabs::newDate(\laabs::newDatetime(null, "UTC"), "Y-m-d_H:i:s").".".$pathinfo['extension'];
        if (isset($pathinfo['extension'])) {
            $resource->fileExtension = $pathinfo['extension'];
        }

        $resource->size = filesize($filename);
        $resource->mimetype = $this->finfo->file($filename, \FILEINFO_MIME_TYPE);

        $resource->setContents($contents);

        return $resource;
    }

    /**
     * Get information about resource contents
     * @param string $contents The contents to store
     * @param string $filename The original filename
     *
     * @return digitalResource/digitalResource
     */
    public function createFromContents($contents, $filename = false)
    {
        $resource = $this->newResource();

        // Basic path information
        $resource->size = strlen($contents);
        $resource->mimetype = $this->finfo->buffer($contents, \FILEINFO_MIME_TYPE);

        if ($filename) {
            $pathinfo = pathinfo($filename);
            $resource->fileName = $pathinfo['basename'];
            if (isset($pathinfo['extension'])) {
                $resource->fileExtension = $pathinfo['extension'];
            }
        }

        $resource->setContents($contents);

        return $resource;
    }

    /**
     * Get information about resource stream contents
     * @param string $stream The data stream to store
     *
     * @return digitalResource/digitalResource
     */
    public function createFromStream($stream)
    {
        $contents = stream_get_contents($stream);

        return $this->createFromContents($contents);
    }

    protected function newResource()
    {
        $resource = \laabs::newInstance("digitalResource/digitalResource");

        $resource->resId = \laabs::newId();

        $resource->created = \laabs::newTimestamp();

        return $resource;
    }


    /**
     * Get the hash for a given resource
     * @param digitalResource/digitalResource $resource      The resource
     * @param string                          $hashAlgorithm The hash algorithm to use
     */
    public function getHash($resource, $hashAlgorithm = false)
    {
        if (!$hashAlgorithm) {
            $hashAlgorithm = $this->hashAlgorithm;
        }

        $resource->hashAlgorithm = $hashAlgorithm;
        $resource->hash = hash($hashAlgorithm, $resource->getContents());
    }

    /**
     * Use a digital resource cluster for storage
     * @param string $clusterId
     * @param string $mode
     * @param bool   $limit
     *
     * @return digitalResource/cluster
     */
    public function useCluster($clusterId, $mode, $limit)
    {
        if (!isset($this->clusters[(string) $clusterId])) {
            $this->currentCluster = $this->clusterController->openCluster($clusterId, $mode, $limit);
            $this->clusters[(string) $clusterId] = $this->currentCluster;
        } else {
            $this->currentCluster = $this->clusters[(string) $clusterId];
        }

        return $this->currentCluster;
    }

    /**
     * Create a ressource container on the cluster (after opening it)
     * @param string $clusterId
     * @param string $path
     * @param mixed  $metadata
     * 
     * @return void
     */
    public function openContainers($clusterId, $path, $metadata=null)
    {
        $cluster = $this->useCluster($clusterId, 'write', true);

        $this->clusterController->openContainers($cluster, $path, $metadata);
    }


    /**
     * Store a given resource
     * @param digitalResource/digitalResource $resource The ressource object
     *
     * @return digitalResource/digitalResource
     */
    public function store($resource)
    {
        // Store resource + metadata
        $this->storeDigitalResource($resource);

        return $resource;
    }

    /**
     * Store a collection of resources sharing the same cluster and repositories from a list of resources
     * @param array  $resources An array of resources to store
     * @param string $clusterId The cluster to apply for store procedure
     *
     * @return bool
     */
    public function storeCollection($resources, $clusterId)
    {
        // Get the storage objects
        $this->useCluster($clusterId, 'write', true);

        foreach ($resources as $resource) {
            $this->storeDigitalResource($resource);
            $resources[] = $resource;
        }

        return $resources;
    }

    /**
     * Store a new resource in current cluster
     * @param object $resource The resource
     */
    public function storeDigitalResource($resource)
    {
        $contents = $resource->getContents();
        if (empty($resource->size)) {
            $resource->size = strlen($contents);
        } else {
            if ($resource->size != strlen($contents)) {
            }
        }

        if (empty($resource->mimetype)) {
            $resource->mimetype = $this->finfo->buffer($contents, \FILEINFO_MIME_TYPE);
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $resource->clusterId = $this->currentCluster->clusterId;
            $resource->created = \laabs::newTimestamp();

            $this->sdoFactory->create($resource, 'digitalResource/digitalResource');

            $this->clusterController->storeResource($this->currentCluster, $resource);

            if ($resource->relatedResource) {
                foreach ($resource->relatedResource as $relatedResource) {
                    $relatedResource->relatedResId = $resource->resId;
                    $relatedResource->archiveId = $resource->archiveId;
                    $this->storeDigitalResource($relatedResource);
                }
            }

        } catch (\Exception $exception) {
            $this->clusterController->rollbackStorage($resource);

            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw \laabs::newException("digitalResource/clusterException", "Resource ".$resource->resId." not created: ".$exception->getMessage(), null, $exception);
        }

        // All repositories returned an uri, save all
        if ($transactionControl) {
            $this->sdoFactory->commit();
        }
    }

    /**
     * Get resources of archive
     * @param id $archiveId The archive identifier
     *
     * @return array Array of digitalResource object
     */
    public function getResourcesByArchiveId($archiveId)
    {
        $resources = $this->sdoFactory->find("digitalResource/digitalResource", "archiveId='$archiveId' AND relatedResId=null");

        return $resources;
    }

    /**
     * Get related resources
     * @param string $resId            The identifier of the converted or the original resource
     * @param string $relationshipType The relationship type with the resource identified by the second parameter
     *
     * @return digitalResource/digialResource[]
     */
    public function getRelatedResources($resId, $relationshipType = null)
    {
        // $relationshipType = isConversionOf OR isSignatureOf
        $whereClause = "relatedResId='$resId'";

        if ($relationshipType) {
            $whereClause .= " AND relationshipType='$relationshipType'";
        }

        $resources = $this->sdoFactory->find("digitalResource/digitalResource", $whereClause);

        return $resources;
    }

    /**
     * Retrieve a resource
     * @param string $resId The resource identifier
     *
     * @return digitalResource/digitalResource
     */
    public function retrieve($resId)
    {
        $resource = $this->sdoFactory->read("digitalResource/digitalResource", $resId);

        if (!$resource) {
            throw \laabs::newException("digitalResource/resourceNotFoundException");
        }

        if (isset($resource->puid)) {
            $resource->format = $this->formatController->get($resource->puid);
        }

        $cluster = $this->useCluster($resource->clusterId, 'read', false);
        $resource->cluster = $cluster;
        $contents = $this->clusterController->retrieveResource($cluster, $resource);

        if (!$contents) {
            $packedResources = $this->sdoFactory->find('digitalResource/packedResource', "resId='".$resource->resId."'");
            if (count($packedResources) > 0) {
                $packageController = \laabs::newController('digitalResource/package');
                foreach ($packedResources as $packedResource) {
                    try {
                        $package = $this->sdoFactory->read('digitalResource/package', $packedResource->packageId);
                        $package->resource = $this->retrieve($packedResource->packageId);
                        $contents = $packageController->getPackedContents($package, $packedResource->name);
                        // Check hash
                        $hash = strtolower(hash($resource->hashAlgorithm, $contents));
                        if ($hash !== strtolower($resource->hash)) {
                            throw \laabs::newException("digitalResource/invalidHashException", "Invalid hash.");
                        }

                        $resource->setContents($contents);
                    } catch (\Exception $exception) {
                        throw \laabs::newException("digitalResource/clusterException", "Resource".$resource->resId."not retrieved");
                    }
                }
            }
        }

        if (!$contents) {
            throw \laabs::newException("digitalResource/clusterException", "Resource ".$resource->resId." could not be retrieved.");
        }

        $relatedResources = $this->getRelatedResources($resource->resId);
        foreach ($relatedResources as $relatedResource) {
            $resource->relatedResource[] = $this->retrieve($relatedResource->resId);
        }

        return $resource;
    }

    /**
     * Get the contents of a given resource
     * @param string $resId
     *
     * @return string
     */
    public function contents($resId)
    {
        $resource = $this->sdoFactory->read("digitalResource/digitalResource", $resId);
        if (!$resource) {
            throw \laabs::newException("digitalResource/resourceNotFoundException");
        }

        $cluster = $this->useCluster($resource->clusterId, 'read', false);

        foreach ($cluster->clusterRepository as $clusterRepository) {
            $repositoryService = $clusterRepository->repository->getService();

            $address = $this->sdoFactory->read("digitalResource/address", array('resId' => $resId, 'repositoryId' => $clusterRepository->repositoryId));

            if ($address) {
                $contents = null;
                if (!$contents) {
                    try {
                        $contents = $repositoryService->readObject($address->address);
                        // Check hash
                        $hash = hash($resource->hashAlgorithm, $contents);
                        if ($hash !== $resource->hash) {
                            throw \laabs::newException("digitalResource/clusterException", "Resource unavailable");
                        }
                    } catch (\Exception $e) {
                        // TODO : throw exception if resource not available on repo, based on options ?
                    }
                }
            } else {
                // TODO : throw exception if resource not available on repo, based on options ?
            }
        }

        if (!$contents) {
            throw \laabs::newException("digitalResource/clusterException", "Resource unavailable");
        }

        return $contents;
    }

    /**
     * Retrieve a resource metadata
     * @param string $resId
     *
     * @return mixed
     */
    public function metadata($resId)
    {
        $resource = $this->sdoFactory->read("digitalResource/digitalResource", $resId);
        if (!$resource) {
            throw \laabs::newException("digitalResource/clusterException", "Resource not found");
        }

        $cluster = $this->useCluster($resource->clusterId, 'read', false);

        if (!$cluster->storeMetadata) {
            return;
        }

        foreach ($cluster->clusterRepository as $clusterRepository) {
            $repositoryService = $clusterRepository->repository->getService();

            $address = $this->sdoFactory->read("digitalResource/address", array('resId' => $resId, 'repositoryId' => $clusterRepository->repositoryId));

            if ($address) {
                if ($contents = $repositoryService->readObject($address->path, 2)) {
                    return $contents;
                }
            }
        }
    }

    /**
     * Get all information about one digital resource
     * @param string $resId The identifier of digital resource
     *
     * @return digitalResource/digitalResource
     */
    public function info($resId)
    {
        $resource = $this->sdoFactory->read("digitalResource/digitalResource", $resId);

        if (!$resource) {
            throw \laabs::newException("digitalResource/clusterException", "Resource not found");
        }

        if (isset($resource->puid)) {
            $resource->format = $this->formatController->get($resource->puid);
        }

        $cluster = $this->useCluster($resource->clusterId, 'read', false);

        foreach ($cluster->clusterRepository as $clusterRepository) {
            $repositoryId = $clusterRepository->repositoryId;
            $repository = $this->sdoFactory->read("digitalResource/repository", $repositoryId);
            $address = $this->sdoFactory->read("digitalResource/address", array('resId' => $resId, 'repositoryId' => $repositoryId));
            $address->path = str_replace('\\', '/', $address->path);
            $address->repository = $repository;
            $resource->address[] = $address;
        }

        $relatedResources = $this->getRelatedResources($resource->resId);
        foreach ($relatedResources as $relatedResource) {
            $resource->relatedResource[] = $this->info($relatedResource->resId);
        }

        return $resource;
    }

    /**
     * Delete entire resource and all addresses
     * @param string $resId The resource identifier
     *
     * @return bool
     */
    public function delete($resId)
    {
        $resource = $this->sdoFactory->read("digitalResource/digitalResource", $resId);
        if (!$resource) {
            throw \laabs::newException("digitalResource/clusterException", "Resource not found");
        }

        $relatedResources = $this->getRelatedResources($resource->resId);
        foreach ($relatedResources as $relatedResource) {
            $resource->relatedResource[] = $this->delete($relatedResource->resId);
        }

        $cluster = $this->useCluster($resource->clusterId, 'delete', false);

        foreach ($cluster->clusterRepository as $clusterRepository) {
            $repositoryId = $clusterRepository->repositoryId;
            $address = $this->sdoFactory->read("digitalResource/address", array('resId' => $resId, 'repositoryId' => $repositoryId));
            $address->repository = $clusterRepository->repository;
            $resource->address[] = $address;
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            foreach ($resource->address as $address) {
                $this->sdoFactory->read("digitalResource/repository", $address->repositoryId);

                $repositoryService = $address->repository->getService();

                $repositoryService->deleteObject($address->path);

                $this->sdoFactory->delete($address);
            }

            $this->sdoFactory->delete($resource);
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }
            throw \laabs::newException("digitalResource/clusterException", "Resource not deleted at address $address->path : ".$exception->getMessage());
        }
        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }

    /**
     * Delete resource contents only
     * @param object $resource
     */
    public function rollbackStorage($resource)
    {
        $this->clusterController->rollbackStorage($resource);
    }

    /**
     * Verify integrity of resource
     * @param digitalResource/digitalResource $resource The digital resource
     *
     * @return digitalResource/digitalResource The digitalResource object verify
     *
     * @throws digitalResource/resourceNotFoundException
     */
    public function verifyResource($resource)
    {
        if (!$resource) {
            throw \laabs::newException("digitalResource/resourceNotFoundException");
        }

        $cluster = $this->useCluster($resource->clusterId, 'read', false);

        return $this->clusterController->verifyResource($cluster, $resource);
    }

    /**
     * Convert resource to another format
     * @param object $digitalResource
     *
     * @return object
     */
    public function convert($digitalResource)
    {
        if (!$this->sdoFactory->exists("digitalResource/conversionRule", array('puid' => $digitalResource->puid))) {
            return false;
        }

        $conversionRule = $this->sdoFactory->read("digitalResource/conversionRule", array('puid' => $digitalResource->puid));

        $configuration =  \laabs::configuration('dependency.fileSystem');

        if (!isset($configuration['conversionServices'])) {
            return false;
        }

        $conversionServices = $configuration['conversionServices'];

        if (!is_array($conversionServices)) {
            return false;
        }

        $outputFormats = null;
        $convertService = null;
        foreach ($conversionServices as $service) {
            if ($service["serviceName"] == $conversionRule->conversionService) {
                $outputFormats = $service["outputFormats"];
                $convertService = $service;
            }
        }

        if (!$outputFormats) {
            return false;
        }

        $contents = $digitalResource->getContents();

        $tempdir = str_replace("/", DIRECTORY_SEPARATOR, \laabs\tempdir());

        if (isset($digitalResource->fileName)) {
            $srcfile = $tempdir.DIRECTORY_SEPARATOR.$digitalResource->fileName;
        } else {
            $srcfile = $tempdir.DIRECTORY_SEPARATOR.$digitalResource->resId;
        }

        file_put_contents($srcfile, $contents);

        $converter = \laabs::newService($conversionRule->conversionService);

        if (!($converter instanceof \dependency\fileSystem\conversionInterface)) {
            return false;
        }

        $tgtfile = $converter->convert($srcfile, $outputFormats[$conversionRule->targetPuid]);

        if (!file_exists($tgtfile)) {
            return false;
        }

        $convertedResource = $this->createFromFile($tgtfile);
        $convertedResource->resId = \laabs::newId();
        $convertedResource->archiveId = $digitalResource->archiveId;
        $convertedResource->puid = $conversionRule->targetPuid;
        $convertedResource->softwareName = $convertService["softwareName"];
        $convertedResource->softwareVersion = $convertService["softwareVersion"];
        $this->getHash($convertedResource);

        $convertedResource->relationshipType = "isConversionOf";

        // Get previous
        while ($digitalResource->relatedResId != "" && $digitalResource->relationshipType == "isConversionOf") {
            $digitalResource = $this->sdoFactory->read("digitalResource/digitalResource", $digitalResource->relatedResId);
        }
        $convertedResource->relatedResId = $digitalResource->resId;

        return $convertedResource;
    }
}
