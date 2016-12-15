<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of DFI.
 *
 * DFI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * DFI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with DFI. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Main class for format definition
 * 
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class PronomFormat
{
    /**
     * The format identifier
     * @var string
     */
    public $id;

    /**
     * The format name
     * @var string
     */
    public $name;

    /**
     * The format version
     * @var string
     */
    public $version;

    /**
     * The format PRONOM unique identifier
     * @var string
     */
    public $puid;

    /**
     * The format possible mimetypes
     * @var array
     */
    public $mimetypes = array();

    /**
     * The format possible extensions
     * @var array
     */
    public $extensions = array();

    /**
     * The format internal signatures
     * @var array
     */
    public $internalSignatureIds = array();

    /**
     * The formats that have lower priority against this one
     * @var array
     */
    public $hasPriorityOverFormatIds;

    /**
     * If format is a container type, ZIP or OLE2
     * @var string
     */
    public $containerType;

    /**
     * Constructor
     * @param DOMElement $formatElement The DROID PRONOM signature file format XML element 
     */
    public function __construct($formatElement)
    {
        $this->id = (integer) $formatElement->getAttribute('ID');

        $this->puid = (string) $formatElement->getAttribute('PUID');

        $this->name = (string) $formatElement->getAttribute('Name');
       
        $this->version = (string) $formatElement->getAttribute('Version');

        $this->mimetypes = explode(',', $formatElement->getAttribute('MIMEType'));

        $extensionElements = $formatElement->getElementsByTagName('Extension');
        for ($i=0, $l=$extensionElements->length; $i<$l; $i++) {
            $this->extensions[] = (string) $extensionElements->item($i)->nodeValue;
        }

        $internalSignatureElements = $formatElement->getElementsByTagName('InternalSignatureID');
        for ($i=0, $l=$internalSignatureElements->length; $i<$l; $i++) {
            $this->internalSignatureIds[] = (integer) $internalSignatureElements->item($i)->nodeValue;
        }

        $hasPriorityOverFileFormatIdElements = $formatElement->getElementsByTagName('HasPriorityOverFileFormatID');
        for ($i=0, $l=$hasPriorityOverFileFormatIdElements->length; $i<$l; $i++) {
            $this->hasPriorityOverFormatIds[] = (integer) $hasPriorityOverFileFormatIdElements->item($i)->nodeValue;
        }

    }

    /**
     * Get the string version of the format
     * @return string
     */
    public function __toString() 
    {
        $string = $this->name;

        if (!empty($this->version)) {
            $string .= ", version:" . $this->version; 
        } 

        return $string . ", id:" . $this->id . ", puid:" . $this->puid; 
    }

}