<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency json.
 *
 * Dependency json is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency json is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency json.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\json;

class JsonObject
{

    /**
     * @var int A bitmask of json options
     */
    protected $options;

    /**
     * @var mixed The storage for data to encode
     */
    protected $storage;

    /**
     * @var dependency\Localisation\DateTimeFormatter
     */
    public $dateTimeFormatter;


    /**
     * Constructor 
     * @param string $type    The type (scalar, class, array, typed array) of data to store
     * @param int    $Options A bitmask of json options
     * @param object $dateTimeFormatter The localisation/dateTimeFormatter object to format the dates of the document
     */
    public function __construct($type=false, $Options=0, \dependency\localisation\DateTimeFormatter $dateTimeFormatter = null)
    {
        $this->options = $Options;
        $this->dateTimeFormatter = $dateTimeFormatter;

        if ($type) {
            $this->storage = \laabs::cast(null, $type);
        } else {
            $this->storage = new \StdClass();
        }

    }

    /**
     * Load data into storage 
     * @param data $data The data to load
     */
    public function load($data)
    {
        $this->storage = $data;
    }

    /**
     * Load json data into storage 
     * @param mixed  $data The data to load
     * @param string $type The type of data to store
     */
    public function loadJson($data, $type=null)
    {
        $object = json_decode($data);

        if ($type) {
            $this->storage = \laabs::cast($object, $type);
        } else {
            $this->storage = $object;
        }
    }

    /**
     * Export storage 
     * @return mixed The value of storage
     */
    public function export()
    {
        return $this->storage;
    }

    /**
     * Save data from storage to json string 
     * @return string The json data string
     */
    public function save()
    {
        $jsonString = \json_encode($this->storage, \JSON_HEX_TAG + \JSON_HEX_AMP + JSON_HEX_APOS + \JSON_HEX_QUOT);

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

        //trigger_error("Error encoding JSON: " . $message . "\n", E_USER_ERROR);

        $e = new \core\Exception("Error encoding JSON: " . $message, 500);
        
        throw $e;
    }

    /**
     * Set value
     * @param string $name  The name of property
     * @param mixed  $value The value to set
     */
    public function __set($name, $value)
    {
        $this->storage->$name = $value;
    }

    /**
     * Get value
     * @param string $name The name of property
     * 
     * @return mixed The value
     */
    public function __get($name)
    {
        return $this->storage->$name;
    }

    /**
     * Call local or storage method
     * @param string $method The name of method
     * @param array  $args   The method args
     * 
     * @return mixed The return of called method
     */
    public function __call($method, array $args=array())
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $args);
        }

        return call_user_func_array(array($this->storage, $method), $args);
    }

    /**
     * Translate storage string values
     * @param string $catalog The name of the catalog to use
     */
    public function translate($catalog)
    {
        $this->translator->setCatalog($catalog);

        $this->recursiveTranslate($this->storage, $depth=0);

    }

    protected function recursiveTranslate(&$value, $depth)
    {
        if ($depth >= 100) {
            return;
        } else {
            $depth++;
        }
        switch (gettype($value)) {
            case 'string':
                $value = $this->translator->getText($value);
                break;

            case 'object':
                $reflectionObject = new \ReflectionObject($value);
                foreach ($reflectionObject->getProperties() as $reflectionProperty) {
                    $reflectionProperty->setAccessible(true);
                    
                    $propertyValue = $reflectionProperty->getValue($value);
                    
                    $this->recursiveTranslate($propertyValue, $depth);
                    
                    $reflectionProperty->setValue($value, $propertyValue);
                }
                break;

            case 'array':
                foreach ($value as $key => &$rowValue) {
                    $this->recursiveTranslate($rowValue, $depth);

                    $value[$key] = $rowValue;
                }
                break;

            default:
        }
    }

    public function formatDateTimes($value = null, $depth=0)
    {
        if (is_null($value)) {
            $value = $this->storage;
        }

        if ($depth >= 100) {
            return;
        } else {
            $depth++;
        }
        
        // Get type of value
        $type = gettype($value);
        //var_dump($type);
        
        switch(true) {
            // If value is scalar, merge text before Pi
            case $type == 'string':
            case $type == 'integer':
            case $type == 'double':
            case $type == 'boolean':
            case $type == 'NULL':
                return $value;

            case $type == 'array':
                foreach ($value as $key => $item) {
                    $value[$key] = $this->formatDateTimes($item, $depth++);
                }
                return $value;

            case $type == 'object' :
                switch (true) {
                    // ArrayObject -> merge array
                    case ($value instanceof \ArrayAccess && $value instanceof \Iterator) :
                        foreach ($value as $key => $item) {
                            $value[$key] = $this->formatDateTimes($item, $depth++);
                        }

                        return $value;

                    case $value instanceof \core\Type\Date:
                        return $this->dateTimeFormatter->formatDate($value);

                    case $value instanceof \core\Type\Timestamp:
                        return $this->dateTimeFormatter->formatTimestamp($value);

                    case $value instanceof \core\Type\DateTime:
                         return $this->dateTimeFormatter->formatDateTime($value);

                    default:
                        foreach ($value as $key => $item) {
                            $value->{$key} = $this->formatDateTimes($item, $depth++);
                        }

                        return $value;

                }
        }
    }

}