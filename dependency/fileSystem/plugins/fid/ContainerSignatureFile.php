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
 * Class for content container signature files
 * 
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class ContainerSignatureFile
{
    /**
     * The relative path to find the file
     * @var string
     */
    public $path;

    /**
     * The internal signatures
     * @var array
     */
    public $internalSignatures = array();

    /**
     * Constructor
     * @param DOMElement $containerSignatureFileElement The DROID PRONOM container signature file element
     */
    public function __construct($containerSignatureFileElement)
    {
        // Get the path
        $this->path = $containerSignatureFileElement->getElementsByTagName('Path')->item(0)->nodeValue;

        // Get the internal signature elements
        $internalSignatureElements = $containerSignatureFileElement->getElementsByTagName("InternalSignature");

        foreach ($internalSignatureElements as $internalSignatureElement) {
            // Instanciate the internal signature object
            $internalSignature = new InternalSignature($internalSignatureElement);
            
            // Store the internal signature by identifier
            $this->internalSignatures[(integer) $internalSignature->id] = $internalSignature;            
        }
    }

    /**
     * Match the signature file on the container contents
     * @param string $container The container directory
     * 
     * @return boolean The result of the match
     */
    public function match($container)
    {
        // Get the contents of the file
        $filename = $container . DIRECTORY_SEPARATOR . $this->path;

        // Check if file exists on the container dir
        if (!is_file($filename)) {
            return false;
        }

        // If no signature found, match by default if file found
        if (!count($this->internalSignatures)) {
            return true;            
        }
        
        $contents = Droid::getContents($filename);
        
        $testedInternalSignatureIds = array();
        // Loop on internal signatures to match at least one (OR)
        foreach ($this->internalSignatures as $internalSignature) {
            // Avoid testing of the same signature several times
            if (!in_array($internalSignature->id, $testedInternalSignatureIds)) {
                $testedInternalSignatureIds[] = $internalSignature->id;
            } else {
                continue;
            }

            // Match the internal signatue
            if ($internalSignature->match($contents)) {
                return true;
            }
        }

        // No internal signature matched
        return false;
    }

}