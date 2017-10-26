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
 * text encoder/decoder
 * 
 * @package Laabs
 * @author  Cyril Vazquez (Maarch) <cyril.vazquez@maarch.org>
 */
class text
{
    /**
     * Encode to string representation
     * @param mixed $data
     * @param int   $tab
     * 
     * @return string
     */
    public static function encode($data, $tab=0)
    {
        $args = func_get_args();

        switch (\gettype($data)) {
            case 'string':
                if (strpos($data, "\n") !== false) {
                    $arr = [];
                    foreach (explode("\n", $data) as $line) {
                        $arr[] = str_repeat(' ', $tab).$line;
                    }

                    return PHP_EOL.implode(PHP_EOL, $arr);
                }
                
                return $data;

            case 'boolean':
                return ($data ? 'true' : 'false');

            case 'integer':
            case 'double':
                return (string) $data;

            case 'array':
                $arr = [];
                if (\laabs\is_assoc($data)) {
                    $klen = 0;
                    foreach ($data as $key => $value) {
                        if (strlen($key) > $klen) {
                            $klen = strlen($key)+3;
                        }
                    }
                    foreach ($data as $key => $value) {
                        $arr[] = str_repeat(' ', $tab).str_pad($key .':', $klen, ' ').static::encode($value, $tab+2);
                    }
                } else {
                    foreach ($data as $key => $value) {
                        $arr[] = str_repeat(' ', $tab).'- '.static::encode($value, $tab+2);
                    }
                }

                return PHP_EOL.implode(PHP_EOL, $arr);

            case 'object':
                if (method_exists($data, '__toString')) {
                    return static::encode((string) $data, $tab+2);
                } else {
                    $arr = [];
                    $klen = 0;
                    $data = get_object_vars($data);
                    foreach ($data as $key => $value) {
                        if (strlen($key) > $klen) {
                            $klen = strlen($key)+3;
                        }
                    }
                    foreach ($data as $key => $value) {
                        $arr[] = str_repeat(' ', $tab).str_pad($key .':', $klen, ' ').static::encode($value, $tab+2);
                    }
                    
                    return PHP_EOL.implode(PHP_EOL, $arr);
                }

            case 'resource':
            case 'NULL':
                return;
        }
    }
}