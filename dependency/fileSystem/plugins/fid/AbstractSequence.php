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
 * abstract class for content byte sequence definitions in internal signatures and container signature files
 * 
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
abstract class AbstractSequence
{
    /**
     * The min offset to find sequence
     * @var integer
     */
    public $minOffset;

    /**
     * The max offset to find sequence
     * @var integer
     */
    public $maxOffset;

    /**
     * The hex value of the byte sequence
     * @var integer
     */
    public $value;

    /**
     * The PCRE pattern generated from the byte sequence
     * @var integer
     */
    public $pattern;

    /**
     * Generate the PCRE pattern from the byte sequence
     * @return string
     * @access protected
     */
    protected function makePattern()
    {
        $bytes = str_split($this->value, 1);
        $pattern = "";
        
        while (current($bytes) !== false) {
            $byte = current($bytes);
            switch ($byte) {
                case '[':
                    $pattern .= '[';
                    break;

                case ']':
                    $pattern .= ']';
                    break;

                // Nagation
                case '!':
                    $pattern .= '^';
                    break;

                case '-':
                case ':':
                    $pattern .= "-";
                    break;


                case '?':
                    next($bytes);
                    next($bytes);
                    $pattern .= ".";
                    break;

                case '*':
                    next($bytes);
                    $pattern .= ".*";
                    break;

                // Character string                    
                case "'":
                    next($bytes);
                    while (current($bytes) !== "'" && current($bytes) !== false) {
                        $pattern .= preg_quote(current($bytes), "/");
                        next($bytes);
                    }
                    break;
                
                // White space
                case ' ':
                    break;

                // Hex value
                default:
                    $hex = $byte . next($bytes);
                    $dec = hexdec($hex);
                    if ($dec >= 32 && $dec <= 126) {
                        $pattern .= preg_quote(chr($dec), "/");
                    } else {
                        $pattern .= '\x' . $hex;
                    }
            }

            next($bytes);

        }

        return $pattern;
    }

    /**
     * Get the pattern
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

}