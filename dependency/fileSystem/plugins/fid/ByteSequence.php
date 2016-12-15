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
 * Class for byte sequence definitions in internal signatures and container signature files
 * 
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class ByteSequence
{
    /**
     * The owner signature identifier
     * @var string
     */
    protected $signatureId;

    /**
     * The reference
     * @var string
     */
    protected $reference;

    /**
     * The endianness (littleEndian | bigEndian)
     * @var string
     */
    protected $endianness;

    /**
     * The sub-sequence objects
     * @var array
     */
    protected $subSequences = array();

    /**
     * The pattern
     * @var string
     */
    protected $pattern;

    /**
     * Constructor
     * @param DOMElement $byteSequenceElement The DROID PRONOM byte sequence element
     * @param string     $signatureId         The owner signature identifier
     */
    public function __construct($byteSequenceElement, $signatureId)
    {
        $this->signatureId = $signatureId;

        // Get the reference
        $this->reference = $byteSequenceElement->getAttribute('Reference');

        // Get the endianness
        $this->endianness = $byteSequenceElement->getAttribute('Endianness');

        // Get the sub-sequence elements
        $subSequenceElements = $byteSequenceElement->getElementsByTagName("SubSequence");

        foreach ($subSequenceElements as $subSequenceElement) {
            // Instanciate the subSequence object
            $subSequence = new SubSequence($subSequenceElement);

            // Store sub-sequence by position
            if (!isset($this->subSequences[$subSequence->position])) {
                $this->subSequences[$subSequence->position] = array();
            }

            $this->subSequences[$subSequence->position][] = $subSequence;
        }

        $this->pattern = $this->makePattern();

        unset($this->subSequences);

    }

    protected function makePattern()
    {
        $pattern = "/";
        $parts = array();
        $offsetPattern ="";
        foreach ($this->subSequences as $position => $positionSubSequences) {
            $subSequence = $positionSubSequences[0];
            $parts[] = $subSequence->getPattern();
            if ($position == 1) {
                if ($subSequence->minOffset != 0 || $subSequence->maxOffset > 0) {
                    $offsetPattern = '.{' . $subSequence->minOffset . "," . $subSequence->maxOffset . '}';
                } else {
                    $offsetPattern = "";
                }
            }
        }

        if ($this->reference == "BOFoffset") {
            $pattern .= '^' . $offsetPattern;
        }

        $pattern .= implode('.*', $parts);

        if ($this->reference == "EOFoffset") {
            $pattern .= $offsetPattern . '$';
        }

        $pattern .= "/s";

        return $pattern;
    }

    /**
     * Match the sequence on the file
     * @param string $contents The contents
     * 
     * @return boolean The result of the match
     */
    public function match($contents)
    {
        switch ($this->reference) {
            case "BOFoffset":
                return preg_match($this->pattern, substr($contents, 0, 65535));

            case 'EOFoffset':
                return preg_match($this->pattern, $contents, $matches, 0, (strlen($contents)-65535));

            default:
                return preg_match($this->pattern, $contents);
        }
    }
}