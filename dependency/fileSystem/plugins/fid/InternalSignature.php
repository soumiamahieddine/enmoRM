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
 * Class for format and container file internal signatures
 * 
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class InternalSignature
{
    /**
     * The internal signature identifier
     * @var string
     */
    public $id;

    /**
     * The list of byte sequences to match
     * @var string
     */
    public $byteSequences = array();

    /**
     * Constructor
     * @param DOMElement $internalSignatureElement The DROID PRONOM internal signature file element
     */
    public function __construct($internalSignatureElement)
    {
        $this->id = (integer) $internalSignatureElement->getAttribute('ID');

        $byteSequenceElements = $internalSignatureElement->getElementsByTagName("ByteSequence");

        foreach ($byteSequenceElements as $byteSequenceElement) {
            $this->byteSequences[] = new ByteSequence($byteSequenceElement, $this->id);
        }
    }

    /**
     * Match the signature on the file
     * @param string $contents The contents
     * 
     * @return boolean The result of the match
     */
    public function match($contents)
    {
        foreach ($this->byteSequences as $i => $byteSequence) {
            // All sequences must match (AND)
            if (!$byteSequence->match($contents)) {
                return false;
            }
        }

        return true;
    }

}