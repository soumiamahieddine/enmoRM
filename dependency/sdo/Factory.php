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
namespace dependency\sdo;
/**
 * Factory for Service Data objects
 * Provides basic and advanced methods to create and execute parameterized Queries
 */
class Factory
{
    use \core\ReadonlyTrait,
        FactoryReaderTrait,
        FactoryWriterTrait;
                
    /* Properties */
    /**
     * The Data Access Service object
     * @var object 
     */
    public $das;

    /**
     * Activate trace
     * 0 = no trace
     * 1 = trace errors
     * 2 = trace query strings
     * 3 = trace query strings  + bound params
     * @var integer
     */
    public $trace;

    /**
     * The cluster: array of one or more datasource definitions used in addition to the base Das
     * @var array
     */
    public $cluster;

    /**
     * The cache for prepared statements
     * @var array
     */
    protected $presparedStmts;
           
    /* Methods */
    /**
     * Deploy the trait on the service that uses the trait
     * @param integer $trace   Activate trace in Laabs log
     * @param object  $das     The dependency sdo Das to use
     * @param array   $cluster A cluster definition, array of das parameters
     */
    public function __construct($trace=3, \dependency\sdo\DasInterface $das=null, array $cluster=null) 
    {
        $this->trace = $trace;
        
        if ($cluster) {
            
            $this->das = array($das);
            foreach ($cluster as $i => $datasource) {
                $dasService = \laabs::dependency('sdo')->getService(LAABS_ADAPTER . LAABS_URI_SEPARATOR . $datasource['@Adapter'] . LAABS_URI_SEPARATOR . 'Das');
                
                $das = $dasService->newInstance($datasource);
                $this->das[] = $das;
            }

            // Add empty line to match indexes between cluster definition and das (because default das is not declared on cluster)
            array_unshift($cluster, array());
            $this->cluster = $cluster;

        } else {
            $this->das = $das;
        }
    }

    /**
     * Check if factory uses a single datasource or a cluster
     * @return boolean
     */
    public function isCluster()
    {
        return (is_array($this->das));
    }

    /**
     * Get the cluster datasources where to read or write the given class object
     * @param string $classname The class of objects to read/write
     * 
     * @return array The array of datasources to use with given query
     */
    protected function getCluster($classname)
    {
        $das = array();

        foreach ($this->das as $i => $clusterDas) {
            if ($i==0) {
                $das[] = $clusterDas;
            } else {
                if (isset($this->cluster[$i]['class']) && is_array($this->cluster[$i]['class'])) {
                    if (in_array($classname, $this->cluster[$i]['class'])) {
                        $das[] = $clusterDas;
                    }
                } else {
                    $das[] = $clusterDas;
                } 
            }
        }

        return $das;
    }
        
    /**
     * Prepare a Laabs Query Language operation
     * @param string $queryString The Laabs Query Language query
     *
     * @return stmt The statement
     */
    public function prepare($queryString)
    {
        $query = \core\Language\Query::parse($queryString);
        
        //var_dump($query);
        /* Prepare statement */
        if ($this->isCluster()) {
            $stmt = array();
            foreach ($this->getCluster($query->getClass()->getName()) as $das) {
                $stmt[] = $das->prepare($query);
            }
        } else {
            $stmt = $this->das->prepare($query);
        }

        return $stmt;
    }
    
    /**
     * Execute a Laabs Query Language operation
     * @param string $queryString The Laabs Query Language query
     *
     * @return mixed The result
     */
    public function query($queryString)
    {
        $stmts = $this->prepare($queryString);
        
        foreach ((array) $stmts as $stmt) {
            foreach ($stmt->query->getParams() as $param) {
                $stmt->bindObject($param->getName(), $param->getValue(), $param->getType());
            }

            $this->execute($stmt);
        }

        return $stmt;
    }

    /**
     * Execute a statement with auto-transaction
     * @param object $stmt The statement
     * @param array  $args The params for exec
     * 
     * @return mixed $result
     */
    public function execute($stmt, $args = [])
    {
        if ($args == null) {
            $args = [];
        }

        if ($this->trace == 2) {
            \laabs::log($stmt->getQueryString());
        }
        if ($this->trace == 3) {
            \laabs::log($stmt->dump());
        }

        // Manage date args
        if (count($args)) {
            foreach ($args as $name => $value) {
                switch (gettype($value)) {
                    case 'object':
                        switch (true) {
                            case $value instanceof \core\Type\Date:
                                $args[$name] = $value->format('Y-m-d');
                                break;

                            case $value instanceof \core\Type\DateTime:
                                $args[$name] = $value->format('Y-m-d H:i:s');
                                break; 

                            case $value instanceof \core\Type\Timestamp:
                                $args[$name] = $value->format('Y-m-d H:i:s.u');
                                break; 
                        }
                        break;

                    case 'boolean':
                        switch ($this->das->driver->getBoolFormat()) {
                            case 1 : 
                                break;

                            case 2 : 
                                $args[$name] = (string) $value;
                                break;

                            case 0 : 
                            default:
                                $args[$name] = (int) $value;
                        }
                }
            }
        }

        $transactionControl = !$this->inTransaction();

        if ($transactionControl) {
            $this->beginTransaction();
        }
        try {
            $result = $stmt->execute($args);
        } catch (\Exception $exception) {

            if ($transactionControl) {
                $this->rollback();
            }

            if ($error = $stmt->getError()) {
                $sqlMessage = $error->getCode() . ': ' . $error->getMessage();
            } else {
                $sqlMessage = false;
            }

            if ($this->trace > 0) {
                \laabs::log($sqlMessage . "\n" . $stmt->dump());
            }

            throw new \Exception("An error occured during the execution of the data access statement.");
        }

        if ($transactionControl) {
            $this->commit();
        }

        return $result;
    } 
               
    /**
     * Begins a new transaction with Data Access Service
     * 
     * @return string The transaction identifier
     */
    public function beginTransaction()
    {
        if ($this->isCluster()) {
            $states = 0;
            foreach ($this->das as $das) {
                if (!$das->inTransaction()) {
                    $states += (integer) $das->beginTransaction();
                } else {
                    $states++;
                }
            }

            if ($states != count($this->das) || $states == 0) {
                throw new Exception("Inconsistant cluster transaction status: $states data access services on " . count($this->das) . "  began a transaction");
            }

            return $states;
        } else {
            return $this->das->beginTransaction();
        }
    }
    
    /**
     * Checks if in transaction with Data Access Service
     * 
     * @return bool
     */
    public function inTransaction()
    {
        if ($this->isCluster()) {
            $states = 0;
            foreach ($this->das as $das) {
                $states += (integer) $das->inTransaction();
            }

            if ($states != count($this->das) && $states != 0) {
                throw new Exception("Inconsistant cluster transaction status: $states data access services on " . count($this->das) . " are in transaction");
            }

            return $states;
        } else {
            return $this->das->inTransaction();
        }
    }
    
    /**
     * Commit current transaction with Data Access Service
     * 
     * @return bool
     */
    public function commit()
    {
        if ($this->isCluster()) {
            $states = 0;
            try {
                foreach ($this->das as $das) {
                    $states += (integer) $das->commit();
                }
            } catch (\Exception $e) {

            }

            if ($states != count($this->das)) {
                throw new Exception("Inconsistant cluster transaction status: $states data access services on " . count($this->das) . " remains in transaction");
            }
        } else {
            return $this->das->commit();
        }
    }
    
    /**
     * Rollback current transaction with Data Access Service
     *
     * @return bool
     */
    public function rollback()
    {        
        if ($this->isCluster()) {
            $states = 0;
            try {
                foreach ($this->das as $das) {
                    $states += (integer) $das->rollback();
                }
            } catch (\Exception $e) {

            }

            if ($states != count($this->das)) {
                throw new Exception("Inconsistant cluster transaction status: $states data access services on " . count($this->das) . " remains in transaction");
            }
        } else {
            return $this->das->rollback();
        }
    }
        
    /* ********************************************************************************************
    **
    **      Properties and fields
    ** 
    ******************************************************************************************** */

    /**
     * Generate a key for a new object
     * @param object $class  The class definition
     * @param object $object The data object
     * 
     * @return bool
     * @access protected
     */
    protected function generateKey($class, $object)
    {
        $key = $class->getPrimaryKey();
        if (!$key) {
            return;
        }

        foreach ($key->getFields() as $keyField) {
            if (empty($object->$keyField)) {
                $object->$keyField = \laabs\uniqid();
            }
        }
    }  

    /* ********************************************************************************************
    **
    **      Key navigation
    ** 
    ******************************************************************************************** */

    /**
     * Get the first usable key for a given class, based on the available values versus key fields.
     *  Behaviour depends on the type of key value :
     *      - associative array will use array keys
     *      - object will use properties
     *      - scalar value or indexed array will return primary key if exists
     * @param object $class    The class definition object
     * @param mixed  $keyValue The available values
     * 
     * @return object The first key definition (primary or unique) matching the values
     * @access protected
     */
    protected function getKey($class, $keyValue)
    {
        // Key value is an associative array
        if (is_array($keyValue) && \laabs\is_assoc($keyValue)) {
            $bindedKeyFields = array_keys($keyValue);
        } elseif (is_object($keyValue)) {
            $bindedKeyFields = array_keys(get_object_vars($keyValue));
        } else {
            $bindedKeyFields = false;
        }

        if ($bindedKeyFields) {
            $key = $this->getKeyFromFields($class, $bindedKeyFields);
            if ($key) {
                return $key;
            }
        }
        
        return $class->getPrimaryKey();
    }

    /**
     * Get the first usable key for a given class, based on the named key fields.
     * @param object $class           The class definition object
     * @param mixed  $bindedKeyFields The available key fields
     * 
     * @return object The first key definition (primary or unique) matching the values
     * @access protected
     */
    protected function getKeyFromFields($class, $bindedKeyFields)
    {
        sort($bindedKeyFields);
        $keys = $class->getKeys();
        foreach ($keys as $key) {
            $keyFields = $key->getFields();
            sort($keyFields);
            $intersection = array_intersect($keyFields, $bindedKeyFields);
            if (count($intersection) == count($keyFields)) {
                return $key;
            }
        }
    }
    
    /* ********************************************************************************************
    **
    **      Parent -> Child navigation
    ** 
    ******************************************************************************************** */

    /**
     * Get the first usable foreign key between given child and parent class, based on the available parent values versus ref key fields.
     * Allows a parent to child navigation
     *  Behaviour depends on the type of key value :
     *      - associative array will use array keys
     *      - object will use properties
     *      - scalar value or indexed array not allowed
     * @param object $childClass      The child class definition object
     * @param mixed  $parentValue     The available parent values
     * @param string $parentClassName The class of the parent object provided
     * 
     * @return object The first foreign key definition matching the values
     * @access protected
     */
    protected function getChildKey($childClass, $parentValue, $parentClassName=false)
    {

        // Key value is an associative array
        if (is_array($parentValue) && \laabs\is_assoc($parentValue)) {
            $bindedRefFields = array_keys($parentValue);
        } elseif (is_object($parentValue)) {
            $bindedRefFields = array_keys(get_object_vars($parentValue));
        } else {
            $bindedRefFields = false;
        }

        if ($bindedRefFields) {
            sort($bindedRefFields);
            $foreignKeys = $childClass->getForeignKeys($parentClassName);
            foreach ($foreignKeys as $foreignKey) {
                $refFields = $foreignKey->getRefFields();
                sort($refFields);
                $intersection = array_intersect($refFields, $bindedRefFields);
                if (count($intersection) == count($refFields)) {
                    return $foreignKey;
                }
            }
        }
    }

    /* ********************************************************************************************
    **
    **      Child -> Parent navigation
    ** 
    ******************************************************************************************** */
    /**
     * Get the first usable foreign key between given parent and child class, based on the available child values versus ref key fields.
     * Allows a child to parent navigation
     *  Behaviour depends on the type of key value :
     *      - associative array will use array keys
     *      - object will use properties
     *      - scalar value or indexed array not allowed
     * @param object $childClass      The child class definition object
     * @param mixed  $childValue      The available child values
     * @param object $parentClassName The parent class name
     * 
     * @return object The first foreign key definition matching the values
     * @access protected
     */
    protected function getParentKey($childClass, $childValue, $parentClassName)
    {
        // Key value is an associative array
        if (is_array($childValue) && \laabs\is_assoc($childValue)) {
            $bindedKeyFields = array_keys($childValue);
        } elseif (is_object($childValue)) {
            $bindedKeyFields = array_keys(get_object_vars($childValue));
        } else {
            $bindedKeyFields = false;
        }
        
        $foreignKeys = $childClass->getForeignKeys($parentClassName);
        if (count($foreignKeys) == 0) {
            return;
        }

        // Try to match binded key field names with foreign key references
        if ($bindedKeyFields) {
            sort($bindedKeyFields);
            foreach ($foreignKeys as $foreignKey) {
                $keyFields = $foreignKey->getFields();
                sort($keyFields);
                $intersection = array_intersect($keyFields, $bindedKeyFields);
                if (count($intersection) == count($keyFields)) {
                    return $foreignKey;
                }
            }
        } elseif ($parentClassName == $childClass->getSchema() . LAABS_URI_SEPARATOR . $childClass->getName()) {
            return reset($foreignKeys);
        }
    }

}
