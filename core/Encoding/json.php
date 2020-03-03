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
 * Json encoder/decoder
 * 
 * @package Laabs
 * @author  Cyril Vazquez (Maarch) <cyril.vazquez@maarch.org>
 */
class json
{

    /**
     * Decode a json contents into an associative array of parameters
     * @param string $params
     * 
     * @return mixed
     */
    public static function decode($params)
    {
        $params = \json_decode($params);
        
        if (is_object($params)) {
            $params = get_object_vars($params);
        }
        
        return $params;
    }

    /**
     * Decode a json stream
     * @param resource $stream
     * 
     * @return mixed
     */
    public static function decodeStream($stream)
    {
        require_once __DIR__.'/JsonTokenizer.php';
        require_once __DIR__.'/JsonParser.php';

        $parser = new \JsonParser();

        $params = $parser->parse($stream);
        
        if (is_object($params)) {
            $params = get_object_vars($params);
        }
        
        return $params;
    }

    /**
     * Encode data into json string
     * @param mixed $data
     * 
     * @return string
     */
    public static function encode($data)
    {
        require_once __DIR__.'/JsonSerializer.php';
        $serializer = new JsonSerializer();

        //$jsonString = \json_encode($data, \JSON_PRETTY_PRINT + \JSON_UNESCAPED_SLASHES + \JSON_UNESCAPED_UNICODE);
        $jsonString = $serializer($data, \JSON_PRETTY_PRINT + \JSON_UNESCAPED_SLASHES + \JSON_UNESCAPED_UNICODE);

        if ($jsonString === false) {
            return false;
        }

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $jsonString;

            case JSON_ERROR_DEPTH:
                $message = 'The maximum stack depth has been exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Invalid or malformed JSON';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = 'Control character error, possibly incorrectly encoded';
                break;
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error';
                break;
            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            case JSON_ERROR_RECURSION:
                $message = 'One or more recursive references detected in the value to be encoded';
                break;
            case JSON_ERROR_INF_OR_NAN:
                $message = 'One or more NAN or INF values in the value to be encoded';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $message = 'A value of a type that cannot be encoded was given';
                break;
            default:
                $message = 'Unknown error';
        }

        trigger_error("Error encoding JSON: " . $message, E_USER_ERROR);
    }

}
