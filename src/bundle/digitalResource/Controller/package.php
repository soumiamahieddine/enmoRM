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
 * Class for digitalResource packing
 *
 * @package Digitalresource
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 */
class package {

    protected $sdoFactory;
    protected $digitalResourceController;
    protected $zip;

    /**
     * Constructor
     * @param \dependency\sdo\Factory                            $sdoFactory                The Sdo factory
     * @param \bundle\digitalResource\Controller\digitalResource $digitalResourceController The controller to store package resources
     * @param \dependency\fileSystem\plugins\zip                 $zip                       The compression service
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, \bundle\digitalResource\Controller\digitalResource $digitalResourceController, \dependency\fileSystem\plugins\zip $zip)
    {
        $this->sdoFactory = $sdoFactory;

        $this->digitalResourceController = $digitalResourceController;

        $this->zip = $zip;
    }

    /**
     * Pack a collection of resources into n packages
     * @param digitalResource/digitalResource[] $resources The array of resources to pack
     * @param object                            $metadata  An object that contains the package metadata
     * @param string                            $clusterId The cluster to apply for store procedure
     * @param integer                           $size      The size of packages to create. If ignored all resources are packed into one unique package
     *
     * @return digitalResource/package[] Array of digitalResource/package object
     */
    public function packResources(array $resources, $metadata = null, $clusterId, $size = false) {
        if ($size) {
            $chunks = array_chunk($resources, $size);
            $packages = array();

            foreach ($chunks as $chunk) {
                $package = $this->createPackage($chunk, $metadata, $clusterId);

                $packages[] = $package;
            }

            return $packages;
        } else {
            return $this->createPackage($resources, $metadata, $clusterId);
        }
    }

    /**
     * Pack a collection of resources into a new package
     * @param digitalResource/digitalResource[] $resources The array of resources to pack
     * @param object                            $metadata  An object that contains the package metadata
     * @param string                            $clusterId The cluster to apply for store procedure
     *
     * @return digitalResource/package[] Array of digitalResource/package object
     */
    protected function createPackage($resources, $metadata = null, $clusterId) {
        $package = \laabs::newInstance('digitalResource/package');
        $package->packageId = \laabs::newId();
        $package->method = 'LZMA';

        $packageDir = \laabs\tempdir().DIRECTORY_SEPARATOR."digitalResource".DIRECTORY_SEPARATOR."pack".DIRECTORY_SEPARATOR.$package->packageId;

        mkdir($packageDir, 0775, true);

        $zipfile = $packageDir.DIRECTORY_SEPARATOR.$package->packageId.'.7z';


        $digitalResourceCluster = $this->digitalResourceController->getCluster($clusterId);
        $this->digitalResourceController->sortClusterRepositories($digitalResourceCluster, Cluster::MODE_WRITE, true);
        $this->digitalResourceController->getClusterRepositoryServices($digitalResourceCluster);

        foreach ($resources as $pos => $resource) {
            $packedResource = \laabs::newInstance('digitalResource/packedResource');
            $packedResource->packageId = $package->packageId;
            $packedResource->resId = $resource->resId;
            $packedResource->name = str_pad($pos, 8, "0", \STR_PAD_LEFT);

            $handler = fopen($packageDir.DIRECTORY_SEPARATOR.$packedResource->name, 'w+');
            stream_copy_to_stream($resource->getHandler(), $handler);
            fclose($handler);

            if ($resMetadata = $resource->getMetadata()) {
                file_put_contents($packageDir.DIRECTORY_SEPARATOR.$packedResource->name.'.metadata', $resMetadata);
            }

            $package->packedResource[] = $packedResource;
        }

        $this->zip->add($zipfile, $packageDir.DIRECTORY_SEPARATOR."*");

        $packageResource = $this->digitalResourceController->createFromFile($zipfile);
        $packageResource->resId = $package->packageId;
        $this->digitalResourceController->getHash($packageResource, 'MD5');

        $package->resource = $packageResource;

        // Store package zip as digital resource
        $this->digitalResourceController->storeDigitalResource($packageResource, $digitalResourceCluster);

        try {
            // Create entry in package db
            $this->sdoFactory->beginTransaction();

            $this->sdoFactory->create($package);

            // Create packedResources in persistence
            foreach ($package->packedResource as $packedResource) {
                $this->sdoFactory->create($packedResource);

                /* foreach ($package->resource->address as $packageAddress) {
                  $address = \laabs::newInstance('digitalResource/address');
                  $address->resId = $packedResource->resId;
                  $address->repositoryId = $packageAddress->repositoryId;
                  $address->address = $packageAddress->address . LAABS_URI_SEPARATOR . $packedResource->name;
                  $address->created = \laabs::newTimestamp();
                  $address->packed = true;
                  var_dump($address);
                  exit;
                  $this->sdoFactory->create($address);
                  } */
            }
        } catch (\Exception $e) {
            $this->sdoFactory->rollback();
            throw \laabs::newException("digitalResource/sdoException");
        }

        $this->sdoFactory->commit();

        return $package;
    }

    /**
     * Retrieve a document by its name on package
     * @param digitalResource/package $package
     * @param string                  $name
     *
     * @return $string The contents
     */
    public function getPackedContents($package, $name) {
        $packageDir = \laabs\tempdir().DIRECTORY_SEPARATOR."digitalResource".DIRECTORY_SEPARATOR."pack".DIRECTORY_SEPARATOR.$package->packageId;

        mkdir($packageDir, 0775, true);

        $zipfile = $packageDir.DIRECTORY_SEPARATOR.$package->packageId.'.7z';
        $handler = fopen($zipfile, 'w+');
        stream_copy_to_stream($package->resource->getHandler(), $handler);
        fclose($handler);
        
        $this->zip->extract($zipfile, $packageDir, $name);

        return fopen($packageDir.DIRECTORY_SEPARATOR.$name, 'r+');
    }
}
