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
    /**
     * Exports the csv contents to an array of entities
     * @param resource $filename
     */
    public function read($filename, $className, $messageType = false, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        if ($messageType) {
            $class = \laabs::getMessage($className);
        } else {
            $class = \laabs::getClass($className);
        }

        $handler = fopen($filename, 'r');

        $header = fgetcsv($handler, 0, $delimiter, $enclosure, $escape);

        $properties = $this->getPropertiesFromHeader($header, $class);

        $collection = [];
        $lineNumber = 0;
        try {
            while ($line = fgetcsv($handler, 0, $delimiter, $enclosure, $escape)) {
                $collection[] = $this->getObjectFromLine($line, $class, $properties);
                $lineNumber++;
            }
        } catch (\Exception $e) {
            throw new \core\Exception("Wrong columns numbers with csv on line %s", 400, null, [$lineNumber]);
        }

        fclose($handler);

        return $collection;
    }

    protected function getPropertiesFromHeader($header, $class)
    {
        $properties = [];
        foreach ($header as $num => $name) {
            if (!$class->hasProperty($name)) {
                throw new \core\Exception("Undefined property %s", 400, null, ["$class->name::$name"]);
            }
            $properties[$num] = $property = $class->getProperty($name);
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }
        }

        return $properties;
    }

    protected function getObjectFromLine($cols, $class, $properties)
    {
        if (count($cols) != count($properties)) {
            throw new \Exception();
        }

        $object = $class->newInstanceWithoutConstructor();

        foreach ($properties as $num => $property) {
            $value = $cols[$num];
            $type = $property->getType();

            if (isset($type)) {
                $value = \laabs::cast($value, $type);
            }

            if (empty($value)) {
                $value = null;
            }

            $property->setValue($object, $value);
        }

        return $object;
    }

    /**
     * Import an array of entities to a csv contents
     *
     * @param array  $collection
     * @param string $className
     * @param bool   $messageType
     */
    public function write($filename, $collection, $className, $messageType = false, $delimiter = ',', $enclosure = '"', $escape = '\\')
    {
        if ($messageType) {
            $class = \laabs::getMessage($className);
        } else {
            $class = \laabs::getClass($className);
        }

        $properties = $class->getProperties();

        $handler = fopen($filename, 'w');

        fputcsv($handler, array_keys($properties), $delimiter, $enclosure, $escape);

        $properties = [];
        foreach ($properties as $name => $property) {
            if (!$property->isPublic()) {
                $property->setAccessible(true);
            }
        }

        foreach ($collection as $object) {
            fputcsv($handler, get_object_vars($object), $delimiter, $enclosure, $escape);
        }
    }
}
