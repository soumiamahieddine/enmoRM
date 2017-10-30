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
 * Url encoder/decoder
 * 
 * @package Laabs
 * @author  Cyril Vazquez (Maarch) <cyril.vazquez@maarch.org>
 */
 class url
{

    /**
     * Encode data into url string
     * @param mixed $data
     * 
     * @return string
     */
    public static function encode($data)
    {
        $object = $data;

        return http_build_query($object);
    }

    /**
     * Decode string url encoded data into array
     * @param mixed $data
     * 
     * @return array
     */
    public static function decode($data)
    {
        $args = array();
        parse_str($data, $args);

        return $args;
    }


 }