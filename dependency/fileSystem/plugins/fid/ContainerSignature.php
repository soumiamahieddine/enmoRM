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
 * Class for content container signatures
 * 
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class ContainerSignature
{
    /**
     * The container signature identifier
     * @var string
     */
    public $id;

    /**
     * The container type: ZIP or OLE
     * @var string
     */
    public $containerType;

    /**
     * The container description
     * @var string
     */
    public $description;

    /**
     * The container file list
     * @var array
     */
    public $files = array();

    /**
     * Constructor
     * @param DOMElement $containerSignatureElement The DROID PRONOM container signature element
     */
    public function __construct($containerSignatureElement)
    {
        // Get the id
        $this->id = $containerSignatureElement->getAttribute('Id');
        
        // Get the container type   
        $this->containerType = $containerSignatureElement->getAttribute('ContainerType');

        // Get the description
        $this->description = $containerSignatureElement->getElementsByTagName("Description")->item(0)->nodeValue;
        
        // Get the file elements
        $fileElements = $containerSignatureElement->getElementsByTagName("File");

        foreach ($fileElements as $fileElement) {
            // Instanciate the subSequence object
            $file = new ContainerSignatureFile($fileElement);

            // Store the file in list
            $this->files[] = $file;
        }
    }

    /**
     * Match the signature on the container contents
     * @param string $container The container directory
     * 
     * @return boolean The result of the match
     */
    public function match($container)
    {
        // All files in container signature must match (AND)
        foreach ($this->files as $file) {

            if (!$file->match($container)) {

                return false;

            }
        }

        // All files matched
        return true;
    }

}