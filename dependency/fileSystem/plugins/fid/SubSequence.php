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
 * Class for internal signature sub-sequences
 * 
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class SubSequence
    extends AbstractSequence
{
    /**
     * The min length of matched sequence
     * @var integer
     */
    protected $minLength;

    /**
     * The left fragments to check the matched sequence
     * @var array
     */
    protected $leftFragments;

    /**
     * The right fragments to check the matched sequence
     * @var array
     */
    protected $rightFragments;

    /**
     * Constructor
     * @param DOMElement $subSequenceElement The DROID PRONOM sub-sequence element
     */
    public function __construct($subSequenceElement)
    {
        $this->position = $subSequenceElement->getAttribute('Position');

        if ($subSequenceElement->hasAttribute('SubSeqMinOffset')) {
            $this->minOffset = (integer) $subSequenceElement->getAttribute('SubSeqMinOffset');
            if ($this->minOffset > 65535) {
                $this->minOffset = 65535;
            }
        } 
        if ($subSequenceElement->hasAttribute('SubSeqMaxOffset')) {
            $this->maxOffset = (integer) $subSequenceElement->getAttribute('SubSeqMaxOffset');
            if ($this->maxOffset > 65535) {
                $this->maxOffset = 65535;
            }
        } 

        if ($subSequenceElement->hasAttribute('MinFragLength')) {
            $this->minLength = (integer) $subSequenceElement->getAttribute('MinFragLength');
        }

        $this->value = $subSequenceElement->getElementsByTagName("Sequence")->item(0)->nodeValue;        

        $leftFragmentElements = $subSequenceElement->getElementsByTagName('LeftFragment');
        for ($i=0, $l=$leftFragmentElements->length; $i<$l; $i++) {
            
            $leftFragment = new LeftFragment($leftFragmentElements->item($i));
            if (!isset($this->leftFragments[$leftFragment->position])) {
                $this->leftFragments[$leftFragment->position] = array();
            }
            $this->leftFragments[$leftFragment->position][] = $leftFragment;
        }

        $rightFragmentElements = $subSequenceElement->getElementsByTagName('RightFragment');
        for ($i=0, $l=$rightFragmentElements->length; $i<$l; $i++) {
            
            $rightFragment = new RightFragment($rightFragmentElements->item($i));
            if (!isset($this->rightFragments[$rightFragment->position])) {
                $this->rightFragments[$rightFragment->position] = array();
            }
            $this->rightFragments[$rightFragment->position][] = $rightFragment;
        }

        $this->pattern = $this->makePattern();
    }

    protected function makePattern()
    {
        $pattern = "";
        if ($this->leftFragments) {
            foreach ($this->leftFragments as $position => $positionLeftFragments) {
                $parts = array();
                foreach ($positionLeftFragments as $leftFragment) {
                    $parts[] = $leftFragment->getPattern();
                }
                if (count($parts) > 1) {
                    $positionPattern = '(' . implode('|', $parts) . ')';
                } else {
                    $positionPattern = $parts[0];
                }
                if ($leftFragment->minOffset != 0 || $leftFragment->maxOffset > 0) {
                    $offsetPattern = '.{' . $leftFragment->minOffset . "," . $leftFragment->maxOffset . '}';
                } else {
                    $offsetPattern = "";
                }

                $pattern = $positionPattern . $offsetPattern . $pattern;
            } 
        }

        $pattern .= parent::makePattern();

        if ($this->rightFragments) {
            foreach ($this->rightFragments as $position => $positionRightFragments) {
                $parts = array();
                foreach ($positionRightFragments as $rightFragment) {
                    $parts[] = $rightFragment->getPattern();
                }
                if (count($parts) > 1) {
                    $positionPattern = '(' . implode('|', $parts) . ')';
                } else {
                    $positionPattern = $parts[0];
                }
                if ($rightFragment->minOffset != 0 || $rightFragment->maxOffset != 0) {
                    $offsetPattern = '.{' . $rightFragment->minOffset . "," . $rightFragment->maxOffset . '}';
                } else {
                    $offsetPattern = "";
                }
                $pattern .= $offsetPattern . $positionPattern;
            }
        }

        return $pattern;
    }

}