<?php
/**
 * Copyright (C) 2020 Maarch
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\csv;

/**
 * CSV Encoding and decoding tools
 *
 * @package Laabs
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class Csv
{
    protected $handler;
    protected $delimiter = ",";
    protected $enclosure = '"';
    protected $escape = '\\';

    /**
     * Loads a file
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function loadFile($filename, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        $this->handler = fopen($filename, 'r');
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    /**
     * Loads a string
     * @param string $filename
     * @param string $delimiter
     * @param string $enclosure
     * @param string $escape
     */
    public function load($data, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        $this->handler = explode("\n", $data);
        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    /**
     * Returns the next line
     */
    public function read()
    {
        if (is_array($this->handler)) {
            $line = current($this->handler);
            
            next($this->handler);
            if (empty($line)) {
                return;
            }

            return str_getcsv($line, $this->delimiter, $this->enclosure, $this->escape);
        } else {
            return fgetcsv($this->handler, 0, $this->delimiter, $this->enclosure, $this->escape);
        }
    }

    /**
     * @param string $className
     */
    public function export($className)
    {
        $header = $this->read();

        $class = \laabs::getClass($className);

        $properties = [];
        foreach ($header as $num => $name) {
            if (!$class->hasProperty($name)) {
                throw new Exception("Undefined property $className::$name");
            }
            $properties[$num] = $property = $class->getProperty($name);
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }
        }

        $collection = [];
        while ($line = $this->read()) {
            if (count($line) != count($header)) {
                throw new Exception("Line ".key($data)." is not well formed.");
            }

            $object = $class->newInstanceWithoutConstructor();

            foreach ($properties as $num => $property) {
                $value = $line[$num];
                $type = $property->getType();

                if (isset($type)) {
                    $value = \laabs::cast($value, $type);
                }

                $property->setValue($object, $value);
            }

            $collection[] = $object;
        }

        return $collection;
    }
}
