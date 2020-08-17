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
 * Class message that represents a stored digital resource
 *
 * @package DigitalResource
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class digitalResource
{
    /**
     * The resource identifier
     *
     * @var id
     */
    public $resId;

    /**
     * The archive identifier
     *
     * @var id
     */
    public $archiveId;

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
     * The handler to resource
     *
     */
    public $handler;

    /**
     * The metadata
     *
     * @var object
     */
    public $metadata;

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
     * Set resource contents
     * @param string $contents The contents of resource
     */
    public function setContents($contents)
    {
        $this->handler = \laabs::createTempStream($contents);
    }

    /**
     * Get resource contents
     *
     * @return string
     */
    public function getContents()
    {
        $contents = stream_get_contents($this->handler);
        rewind($this->handler);

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
}
