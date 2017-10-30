<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency sdo.
 *
 * Dependency sdo is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency sdo is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency sdo.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\sdo\Adapter\Database;
/**
 * Class for Sdo Database adapter Statements
 *
 * @package Dependency_Sdo
 */
class Statement
    extends \dependency\datasource\Adapter\Database\Statement
    implements \dependency\sdo\StatementInterface
{
    /* Constants */

    /* Properties */
    public $query;

    protected $driver;

    protected $driverClass;

    protected $params = array();

    /* Methods */
    /**
     * Construct a new Sdo Database Statement
     * @param \PDOStatement                                    $pdoStatement The PDO statement object
     * @param \core\Language\Query                             $query        The Query object used to prepare the PDO Statement
     * @param \dependency\sdo\Adapter\Database\DriverInterface $driver       The Sdo dataaccess service
     *
     * @return void
     */

    public function __construct(\PDOStatement $pdoStatement, \core\Language\Query $query, \dependency\sdo\Adapter\Database\DriverInterface $driver) 
    {
        parent::__construct($pdoStatement);

        $this->query = $query;

        $this->driver = $driver;

        $this->driverClass = get_class($driver);
    }

    /**
     * Bind a Sdo Data Object to the statement
     * @param string $ns      The namespace of object (class or constraint name)
     * @param object &$object The data object
     * @param object $type    The type (class, constraint key /unique / fkey)
     * @param array  $options An associative array of driver-specific options 
     * 
     * @return bool
     */
    public function bindObject($ns, &$object, $type=null, array $options=null)
    {
        //var_dump("bind object $ns");
        //var_dump($type);
        switch (true) {
            case $type instanceof \core\Reflection\Type :
                return $this->bindTypeProperties($ns, $object, $type->getProperties());

            case is_null($type) :
                return $this->bindObjectProperties($ns, $object);

            default:
                throw new \Exception("Invalid object type for binding: you must provide a Sdo class, key, foreign key");
        }       
    }

    /**
     * Bind a Sdo Data Object key to the statement
     * @param string $ns      The namespace of object (class or constraint name)
     * @param object &$object The data object
     * @param object $key     The key (constraint key /unique)
     * @param array  $options An associative array of driver-specific options 
     * 
     * @return bool
     */
    public function bindKey($ns, &$object, \core\Reflection\Key $key=null, array $options=null)
    {
        //var_dump("bind object $ns");
        //var_dump($type);
        //var_dump($object);
        $class = \laabs::getClass($key->getClass());
        $properties = array();
        foreach ($key->getFields() as $field) {
            $properties[] = $class->getProperty($field);
        }

        return $this->bindTypeProperties($ns, $object, $properties);   
    }

    /**
     * Bind a Sdo Data Object foreign key to the statement
     * @param string $ns         The namespace of object (class or constraint name)
     * @param object &$object    The data object
     * @param object $foreignKey The key (constraint key /unique)
     * @param array  $options    An associative array of driver-specific options 
     * 
     * @return bool
     */
    public function bindForeignKey($ns, &$object, \core\Reflection\ForeignKey $foreignKey, array $options=null)
    {
        //var_dump("bind object $ns");
        //var_dump($type);
        //var_dump($object);
        $refClass = \laabs::getClass($foreignKey->getRefClass());
        $properties = array();
        foreach ($foreignKey->getRefFields() as $refField) {
            $properties[] = $refClass->getProperty($refField);
        }

        return $this->bindTypeProperties($ns, $object, $properties);  
    }

    /* ResultSet methods */
    public function fetch($modelName=false, array $ctor_args=null)
    {
        $object = parent::fetch();
        
        if (!$object) {
            return null;
        }
        
        if ($modelName) {
            return \laabs::castObject($object, $modelName, $ctor_args);
        } else {
            return $object;
        }

        
    }
    
    public function fetchAll($modelName=false, array $ctor_args=null)
    {
        $objects = parent::fetchAll();

        if (empty($objects)) {
            return $objects;
        }

        if ($modelName) {
            return \laabs::castCollection($objects, $modelName);
        } else {
            return $objects;
        }
        
        
    }
    
    public function fetchItem($cursor_offset=0, $modelName=false, array $ctor_args=null)
    {
        $object = parent::fetchItem($cursor_offset);

        if (!$object) {
            return null;
        }

        if ($modelName) {
            return \laabs::castObject($object, $modelName, $ctor_args);
        } else {
            return $object;
        }

        
    }
    
    public function fetchColumn($offset=0, $type=false)
    {
        $value = parent::fetchColumn($offset);
        
        if ($type) {
            return \laabs::cast($value, $type);
        }

        return $value;
    }

    protected function bindTypeProperties($ns, &$object, array $properties)
    {
        foreach ($properties as $property) {
            $propertyName = $property->getName();

            // Only bind existing properties (partial objects)
            if (!property_exists($object, $propertyName)) {
                continue;
            }

            // Only bind public properties
            if (!$property->isPublic()) {
                continue;
            } 

            // Only bind strig properties (scalar or type with __toString and construct methods)
            if (!$property->isStringifyable()) {
                continue;
            }

            $propertyType = $property->getType();          
            
            $bindName = $ns . LAABS_URI_SEPARATOR . $propertyName;

            //var_dump("bind property $bindName");
            //var_dump($propertyType);
            //var_dump($object->$propertyName);
            
            $this->bindProperty($bindName, $object->$propertyName, $propertyType);
        }

        return true;
    }

    protected function bindObjectProperties($ns, &$object) 
    {
        foreach ($object as $name => &$value) {
            $bindName = $ns . LAABS_URI_SEPARATOR . $name;
            $type = gettype($value);
            $this->bindProperty($bindName, $value, $type);
        }

        return true;
    } 

    protected function bindProperty($ns, &$propertyValue, $propertyType=null)
    {
        $driverClass = $this->driverClass;
        if (isset($driverClass::$maxNameLength) && strlen($ns) > $driverClass::$maxNameLength) {
            $bindName = "sdo_" . \laabs\md5($ns, false, true);
        } else {
            $bindName = preg_replace('/[^A-Za-z0-9_]/', '_', $ns);
        }

        $bindType = null;
        $bindLength = null;
        $bindValue = $propertyValue;

        if (is_null($propertyValue) && $propertyType != 'binary') {
            $bindType = \PDO::PARAM_NULL;
        } else {
            if (substr($propertyType, -2) == "[]" && is_array($propertyValue)) {
                $bindType = \PDO::PARAM_STR;
                $bindValue = implode(" ", $propertyValue);
            } else {
                switch ($propertyType) {
                    case 'date':
                        if (!is_string($propertyValue)) {
                            $bindValue = $propertyValue->format('Y-m-d');
                        }
                        $bindType = \PDO::PARAM_STR;
                        break;

                    case 'datetime':
                        if (!is_string($propertyValue)) {
                            $bindValue = $propertyValue->format('Y-m-d H:i:s');
                        }
                        $bindType = \PDO::PARAM_STR;
                        break;

                    case 'timestamp':
                        if (!is_string($propertyValue)) {
                            $bindValue = $propertyValue->format('Y-m-d H:i:s.u');
                        }
                        $bindType = \PDO::PARAM_STR;
                        break;

                    case 'binary':
                        $bindType = \PDO::PARAM_LOB;
                        break;

                    case 'boolean':
                        switch ($this->driver->getBoolFormat()) {
                            case 1 : 
                                $bindType = \PDO::PARAM_BOOL;
                                break;

                            case 2 : 
                                $bindType = \PDO::PARAM_STR;
                                break;

                            case 0 : 
                            default:
                                $bindType = \PDO::PARAM_INT;
                        }
                        break;

                    case 'float':
                    case 'integer':
                        $bindType = \PDO::PARAM_INT;
                        break;

                    case 'string':
                    default:
                        $bindType = \PDO::PARAM_STR;
                }
            }
            
        }

        return $this->bindParam($bindName, $bindValue, $bindType, 0, array(), $ns);
    }

    public function dump()
    {
        $dump = "SQL: [ " . $this->getQueryString() . " ]" . "\n"
                . "PARAMS: " . count($this->params) . "\n";
            
        if (count($this->params) > 0) {
            foreach ($this->params as $pos => $param) {
                if (!$param) {
                    $dump .= "Param " . $pos . "\n";
                    continue;
                }
                $value = $param->getValue();
                if (is_scalar($value)) {
                    if (strlen($value) > 128) {
                        $dmpvalue = substr($value, 0, 125) . "...";
                    } else {
                        $dmpvalue = $value;
                    }
                    $dmpvalue = "'" . $dmpvalue . "'";
                } else {
                    $dmpvalue = \laabs\gettype($value);
                }

                switch($type = $param->getType()) {
                    case "0" : 
                        $type = "NULL";
                        break;

                    case "1" : 
                        $type = "NUMBER";
                        break;

                    case "2" : 
                        $type = "STRING";
                        break;

                    case "3" : 
                        $type = "LOB";
                        break;

                    case "5" : 
                        $type = "BOOL";
                        break;
                }

                $dump .= "Param " . $pos . "\n" 
                    . " Name: " . $param->getName() . "\n" 
                    . " Value: " . $dmpvalue . "\n"
                    . " Type: ". $type . "\n"
                    . " length: " . $param->getLength() . "\n";
            }
        }

        return $dump;
    }

}