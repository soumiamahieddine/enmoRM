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
 * abstract class for content fragments definitions in internal signatures and container signature files
 * 
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
abstract class AbstractFragment
    extends AbstractSequence
{

    /**
     * Constructor
     * @param DOMElement $fragmentElement The DROID PRONOM fragment element
     */
    public function __construct($fragmentElement)
    {
        // Get the position
        $this->position = (integer) $fragmentElement->getAttribute('Position');

        // Get the min offset to find fragment
        if ($fragmentElement->hasAttribute('MinOffset')) {
            $this->minOffset = (integer) $fragmentElement->getAttribute('MinOffset');
            if ($this->minOffset > 65535) {
                $this->minOffset = 65535;
            }
        }

        // Get the max offset to find fragment
        if ($fragmentElement->hasAttribute('MaxOffset')) {
            $this->maxOffset = (integer) $fragmentElement->getAttribute('MaxOffset');
            if ($this->maxOffset > 65535) {
                $this->maxOffset = 65535;
            }
        }

        // Get the fragment value
        $this->value = $fragmentElement->nodeValue;
        
        // Generate the associated PCRE pattern to match
        $this->pattern = $this->makePattern();
    }

}