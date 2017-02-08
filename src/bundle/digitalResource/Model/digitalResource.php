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
 * Class model that represents a stored digital resource
 *
 * @package Digitalresource
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 *
 * @pkey [resId]
 * @fkey [clusterId]  digitalResource/cluster[clusterId]
 * @fkey [relatedResId]  digitalResource/digitalResource[resId]
 *
 */
class digitalResource
{
    /**
     * The universal identifier
     *
     * @var id
     * @xpath @oid
     */
    public $resId;

    /**
     * The storing profile identifier
     *
     * @var id
     */
    public $clusterId;

    /**
     * The size of the resource
     *
     * @var integer
     */
    public $size;

    /**
     * The UK National Archives PRONOM registry format identifier
     *
     * @var string
     */
    public $puid;

    /**
     * The mime type
     *
     * @var string
     */
    public $mimetype;

    /**
     * The integrity hash value
     *
     * @var string
     */
    public $hash;

    /**
     * The integrity hash algorithm
     *
     * @var string
     */
    public $hashAlgorithm;

    /**
     * The file extension
     *
     * @var string
     */
    public $fileExtension;

    /**
     * The file name
     *
     * @var string
     */
    public $fileName;

    /**
     * The xml for media information : audio, video, image
     *
     * @var string
     */
    public $mediaInfo;

    /**
     * The date when the resource was recorded
     *
     * @var timestamp
     */
    public $created;

    /**
     * The date when the resource was last mofified
     *
     * @var timestamp
     */
    public $updated;

    /**
     * The current usable addresses of the resource
     *
     * @var digitalResource/address[]
     */
    public $address;

    /**
     * The handler to ressource
     *
     * @var resource
     */
    protected $handler;

    /**
     * The metadata
     *
     * @var object
     */
    protected $metadata;

    /**
     * The format
     *
     * @var digitalResource/format
     */
    public $format;

    /**
     * The related resource identifier
     *
     * @var string
     */
    public $relatedResId;

    /**
     * The relationship type
     *
     * @var string
     */
    public $relationshipType;

    /**
     * Related ressources
     *
     * @var digitalResource/digitalResource[]
     */
    public $relatedResource = [];

    /**
     * Set resource handler
     * @param resource $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * Get resource handler
     *
     * @return resource handler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Set resource filename
     * @param string $path The path of resource
     */
    public function setFilename($path)
    {
        $this->handler = $path;
    }

    /**
     * Set resource contents
     * @param string $contents The contents of resource
     */
    public function setContents($contents)
    {
        $this->handler = \laabs::createMemoryStream($contents);

        $this->size = strlen($contents);

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $this->mimetype = $finfo->buffer($contents);
    }

    /**
     * Get resource contents
     *
     * @return string
     */
    public function getContents()
    {
        if (is_string($this->handler)) {

            $contents = file_get_contents($this->handler);

        } elseif (is_resource($this->handler)) {

            $contents = stream_get_contents($this->handler);
            rewind($this->handler);

        }

        return $contents;
    }

    /**
     * Set metadata
     * @param object $metadata
     */
    public function setMetadata($metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * Get metadata
     *
     * @return object
     */
    public function getMetadata()
    {
        return $this->metadata;
    }
} // END class digitalResource
