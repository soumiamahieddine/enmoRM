<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of Laabs.
 *
 * Laabs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Laabs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Laabs.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace core\Encoding;

/**
 * Base64 encoder/decoder
 *
 * @package Laabs
 * @author  Cyril Vazquez (Maarch) <cyril.vazquez@maarch.org>
 */
class Base64
{
    /**
     * Encode stream
     */
    public static function decode($value)
    {
        if (is_scalar($value)) {
            return base64_decode($value);
        }

        if (is_resource($value)) {
            $stream = fopen('php://temp', 'w+');
            while (!feof($value)) {
                $chunk = fread($value, 1024*1024*2);
                fwrite($stream, base64_decode($chunk));
            }

            rewind($stream);

            return $stream;
        }
    }
}
