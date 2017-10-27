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
 * Class model that represents a digital resource content type configuration
 *
 * @package Digitalresource
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 * 
 * 
 */
class contentType
{
    /**
     * The format name
     *
     * @var name
     * @notempty
     */
    public $name;

    /**
     * The format description
     *
     * @var name
     */
    public $description;

    /**
     * The UK National Archives PRONOM format identifiers
     *
     * @var tokenlist
     */
    public $puids;

    /**
     * The mediatype 
     *
     * @var string
     * @enumeration [message, text, audio, video, image, application, multipart, model]
     */
    public $mediatype;

    /**
     * The validation mode
     *
     * @var string
     */
    public $validationMode;

    /**
     * The conversion mode
     *
     * @var string
     */
    public $conversionMode;

    /**
     * The conversion mode
     *
     * @var string
     */
    public $textExtractionMode;

    /**
     * The metadata extraction mode
     *
     * @var string
     */
    public $metadataExtractionMode;

} // END class resourceType 
