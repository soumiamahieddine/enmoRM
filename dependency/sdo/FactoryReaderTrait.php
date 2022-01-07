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
 * Trait FactoryReader
 * The API for Service Data Object read queries
 */
trait FactoryReaderTrait
{
    /**
     * Count a set of mathcing the filter expression
     * @param string $className   The class of the objects to find
     * @param mixed  $queryString The query (search) expression, encoded in Laabs Query Language, as a string or an array with the string and the variables
     * @param array  $queryParams An associative array of parameters for the query string 
     *
     * @return integer
     */
    public function count($className, $queryString=false, $queryParams=array())
    {
        $lqlString = 'COUNT';
        
        $lqlString .= ' ' . $className;
        if ($queryString) {
            $lqlString .= "(" . $queryString .")";
        }

        // Get query object
        $parser = new \core\Language\Parser();
        $query = $parser->parseQuery($lqlString);

        /* Prepare statement */
        if ($this->isCluster()) {
            $stmt = $this->das[0]->prepare($query);
        } else {
            $stmt = $this->das->prepare($query);
        }

        /* Execute statement */
        $result = $this->execute($stmt, $queryParams);
        
        return $stmt->fetchColumn();
    }

    /**
     * Index a set of objects matching the filter expression
     * @param string  $className   The class of the objects to find
     * @param mixed   $properties  An array of property names to index
     * @param string  $queryString The query (search) expression, encoded in Laabs Query Language
     * @param array   $queryParams An associative array of parameters for the query string
     * @param array   $keyfields   And array of key fields
     * @param integer $offset      The start offset
     * @param integer $length      The max number of results to show
     *
     * @return array An array of object index values matching the query and ordered as index
     */
    public function index(
        $className,
        $properties = null,
        $queryString = false,
        $queryParams = array(),
        $keyfields = null,
        $offset = 0,
        $length = null
    ) {
        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_READ);

        $class = \laabs::getClass($className);
        $query->setClass($class);

        if ($keyfields) {
            $key = $this->getKeyFromFields($class, (array) $keyfields);
        } else {
            $key = $class->getPrimaryKey();
        }
        
        // Find index and add properties
        switch (true) {
            case is_string($properties) && $properties == "*":
            case empty($properties):
                $properties = array();
                break;
            case is_string($properties) && $properties != "*":
                $properties = array($properties);
                // Continue
            case is_array($properties):
                foreach ($properties as $propertyName) {
                    $property = $class->getProperty($propertyName);
                    $query->addProperty($property);
                    $query->addSorting(new \core\Language\Sorting($property));
                }
                foreach ($key->getFields() as $propertyName) {
                    if (!in_array($propertyName, $properties)) {
                        $property = $class->getProperty($propertyName);
                        $query->addProperty($property);
                        $query->addSorting(new \core\Language\Sorting($property));
                    }
                }
                break;
        }

        if ($offset) {
            $query->setOffset($offset);
        }

        if ($length) {
            $query->setLength($length);
        }

        if ($queryString) {
            $lqlString = "(" . $queryString .")";
            $parser = new \core\Language\Parser();
            $assert = $parser->parseAssert($lqlString, $query);
            $query->addAssert($assert);
        }

        /* Prepare statement */
        if ($this->isCluster()) {
            $stmt = $this->das[0]->prepare($query);
        } else {
            $stmt = $this->das->prepare($query);
        }

        /* Execute statement */
        $result = $this->execute($stmt, $queryParams);

        /* Fetch all objects */
        if ($result) {
            $array = $stmt->fetchAll();

            if (count($properties) == 1) {
                $property = $properties[0];
                foreach ($array as $pos => $index) {
                    $keyvalue = array();
                    foreach ($key->getFields() as $field) {
                        $keyvalue[] = $index->$field;
                        if (!in_array($field, $properties)) {
                            unset($index->$field);
                        }
                    }
                    unset($array[$pos]);
                    if (isset($index->$property)) {
                        $array[\laabs\implode(LAABS_URI_SEPARATOR, $keyvalue)] = $index->$property;
                    } else {
                        $array[\laabs\implode(LAABS_URI_SEPARATOR, $keyvalue)] = null;
                    }
                }
            } else {
                //Fecth without casting to a class
                foreach ($array as $pos => $index) {
                    $keyvalue = array();
                    foreach ($key->getFields() as $field) {
                        $keyvalue[] = $index->$field;
                        if (!in_array($field, $properties)) {
                            unset($index->$field);
                        }
                    }
                    unset($array[$pos]);
                    $array[\laabs\implode(LAABS_URI_SEPARATOR, $keyvalue)] = $index;
                }
            }
        }

        return $array;
    }
    
    /**
     * Find a set of objects matching the filter expression, ordered
     * @param string  $className     The class of the objects to find
     * @param string  $queryString   The query (search) expression, encoded in Laabs Query Language
     * @param array   $queryParams   An associative array of parameters for the query string
     * @param string  $sortingString The sorting (order) expression, encoded in Laabs Query Language
     * @param integer $offset        The start offset
     * @param integer $length        The max number of results to show
     * @param bool    $lock          Lock rows for update
     *
     * @return array An array of objects matching the query and ordered as requested
     */
    public function find($className, $queryString=false, $queryParams = array(), $sortingString=false, $offset=0, $length=null, $lock=false)
    {
        $lqlString = 'READ';
                
        $lqlString .= ' ' . $className;

        if ($queryString) {
            $lqlString .= " (" . $queryString .")";
        } 

        if ($sortingString) {
            $lqlString .= ' SORT '. $sortingString;
        } else {
            $class = \laabs::getClass($className);
            if ($class->hasPrimaryKey()) {
                $key = $class->getPrimaryKey();
            } elseif ($class->hasKey()) {
                $keys = $class->getKeys();
                $key = reset($keys);
            }

            if ($key) {
                $sortingString = \implode(', ', $key->getFields());
                $lqlString .= ' SORT '. $sortingString;
            }
        }

        if ($offset) {
            $lqlString .= ' OFFSET ' . (int) $offset;
        }

        if ($length) {
            $lqlString .= ' LIMIT ' . (int) $length;
        }

        if ($lock) {
            $lqlString .= 'LOCK';
        }

        if (isset($this->preparedStmts[$lqlString])) {
            $stmt = $this->preparedStmts[$lqlString];
        } else {
            // Get query object
            $query = \core\Language\Query::parse($lqlString);

            /* Prepare statement */
            if ($this->isCluster()) {
                $stmt = $this->das[0]->prepare($query);
            } else {
                $stmt = $this->das->prepare($query);
            }

            $this->preparedStmts[$lqlString] = $stmt;
        }

        /* Execute statement */
        $result = $this->execute($stmt, $queryParams);

        /* Fetch all objects */
        if ($result) {
            $array = $stmt->fetchAll($className);
        }

        return $array;
    }
    
    /**
     * Checks if an object exists in storage
     * @param string $className The class of the objects to find
     * @param mixed  $keyValue  A scalar, associative array, indexed array or object representing the univoque key to use for object retrieval
     *  Passing an associative array key value will allow to guess which key should be used, else the primary key will be used if exists
     * 
     * @return bool The object exists or not
     */
    public function exists($className, $keyValue)
    {
        $class = \laabs::getClass($className);

        $key = $this->getKey($class, $keyValue);
        if (!$key) {
            throw new Exception("No key found to check existence of object of class $className");
        }

        // Use class name + key name to identify the prepared statement with variables
        // If already prepared, re-use statement, else store un array of prepared statements
        $stmtId = $className . LAABS_URI_SEPARATOR . $key->getName();
        if (isset($this->preparedStmts[$stmtId])) {

            $stmt = $this->preparedStmts[$stmtId];

        } else {
            /* Get Sdo Query 'Read' */
            $query = new \core\Language\Query();
            $query->setCode(LAABS_T_READ);

            $query->setClass($class);
            
            $query->addProperty(true);
            
            $keyAssert = $key->getAssert();
            $query->addAssert($keyAssert);

            /* Prepare statement */
            if ($this->isCluster()) {

                $cluters = $this->getCluster($className);
                $stmt = array();

                foreach ($cluters as $i => $das) {
                    $stmt[$i] = $das->prepare($query);
                }

            } else {
                $stmt = $this->das->prepare($query);
            }
        }
        
        
        /* Bind value of key to key parameter */
        $keyObject = $key->getObject($keyValue);

        /* Prepare statement */
        if ($this->isCluster()) {
            $cluters = $this->getCluster($className);
            $exists = array();

            foreach ($stmt as $i => $stmtn) {
                $stmtn->bindKey($key->getClass(), $keyObject, $key);
                $result = $this->execute($stmtn);
                
                if ($result) {
                    $exists[] = (bool) $stmtn->fetchColumn();
                } 
            }
            
            for ($i = 1; $i < count($exists); $i++) {
                if ($exists[0] != $exists[$i]) {
                    throw new Exception\clusterIntegrityException("Object of class $className identified by $keyValue was not exists on all clusters");
                }
            }
            
            return $exists[0];
        } else {
            $stmt->bindKey($key->getClass(), $keyObject, $key);

            $result = $this->execute($stmt);
        
            if ($result) {
                $exists = $stmt->fetchColumn();

                return (bool) $exists;
            } 
        }
        
    }
    
    /**
     * Read an object from storage
     * @param string $className The class of the objects to find
     * @param mixed  $keyValue  A scalar, associative array, indexed array or object representing the univoque key to use for object retrieval
     * @param bool   $lock      Lock objects in datasource
     * 
     * @return object The requested object
     */
    public function read($className, $keyValue, $lock=false)
    {
        // Get class definition
        $class = \laabs::getClass($className);

        // Get usable key definition
        $key = $this->getKey($class, $keyValue);
        if (!$key) {
            throw new Exception("No key found to read class $className");
        }

        // Use class name + key name to identify the prepared statement with variables
        // If already prepared, re-use statement, else store un array of prepared statements
        $stmtId = $className . LAABS_URI_SEPARATOR . $key->getName();
        if (isset($this->preparedStmts[$stmtId])) {

            $stmt = $this->preparedStmts[$stmtId];

        } else {
            /* Get Query 'Read' */
            $query = new \core\Language\Query();
            $query->setCode(LAABS_T_READ);

            $query->setClass($class);

            $keyAssert = $key->getAssert();
            $query->addAssert($keyAssert);

            if ($lock) {
                $query->lock(true);
            }
            
            /* Prepare statement */
            if ($this->isCluster()) {
                $cluters = $this->getCluster($className);
                $stmt = array();
                foreach ($cluters as $i => $das) {
                    $stmt[$i] = $das->prepare($query);
                }
            } else {
                $stmt = $this->das->prepare($query);
            }

            $this->preparedStmts[$stmtId] = $stmt;
        }

        /* Generate value of key to key parameter */
        $keyObject = $key->getObject($keyValue);

        // Bind key object
        // Execute statement
        // Fetch results
        if ($this->isCluster()) {
            $objects = array();
            foreach ($stmt as $i => $stmtn) {
                $stmtn->bindKey($className, $keyObject, $key);
                $result = $this->execute($stmtn);
                
                if ($result) {
                    $objects[] = $stmtn->fetch($className);
                } 
            }
            

            for ($i = 1; $i < count($objects); $i++) {
                if ($objects[0] != $objects[$i]) {
                    throw new Exception\clusterIntegrityException("Object of class $className identified by $keyValue was not identical on all clusters");
                }
            }

            return $objects[0];

        } else {
            $stmt->bindKey($className, $keyObject, $key);

            $result = $this->execute($stmt);
        
            if ($result) {
                $object = $stmt->fetch($className);

                if ($object) {
                    return $object;
                }
            } 
        }      

        if (is_object($keyValue)) {
            $keyValue = implode(LAABS_URI_SEPARATOR, get_object_vars($keyValue));
        }
        if (is_array($keyValue)) {
            $keyValue = implode(LAABS_URI_SEPARATOR, $keyValue);
        }
        
        throw new \core\Exception\NotFoundException("Object of class $className identified by $keyValue was not found");
    }

    /**
     * Find distinct values
     * @param string  $className    The class of the objects to find
     * @param string  $propertyName The name of the property to find distinct values
     * @param string  $propertySum  The name of the property to sum, if ommited just count
     * @param string  $queryString  The query (search) expression, encoded in Laabs Query Language
     * @param integer $offset       The start offset
     * @param integer $length       The max number of results to show
     *
     * @return array An array of values matching the query and ordered as requested
     */
    public function summarise($className, $propertyName, $propertySum=false, $queryString=false, $offset=0, $length=null)
    {
        $lqlString = 'SUMMARISE';
                
        $lqlString .= ' ' . $className;

        if ($propertySum) {
            $lqlString .= '/' . $propertySum;
        }

        $lqlString .= ' [' . $propertyName . ']';

        $queryArgs = [];
        if ($queryString) {
            if (is_array($queryString)) {
                list($queryString, $queryArgs) = $queryString;
            }
            $lqlString .= "(" . $queryString .")";
        } 

        $lqlString .= ' SORT '. $propertyName;

        if ($offset) {
            $lqlString .= ' OFFSET ' . (int) $offset;
        }

        if ($length) {
            $lqlString .= ' LIMIT ' . (int) $length;
        }

        $parser = new \core\Language\Parser();
        $query = $parser->parseQuery($lqlString);

        /* Prepare statement */
        if ($this->isCluster()) {
            $stmt = $this->das[0]->prepare($query);
        } else {
            $stmt = $this->das->prepare($query);
        }

        /* Execute statement */
        $result = $this->execute($stmt, $queryArgs);
        
        /* Fetch all objects */
        if ($result) {
            $array = $stmt->fetchAll();
            $summary = [];
            $sum = 'sum';
            if ($propertySum) {
                $sum = $propertySum;
            }

            foreach ($array as $i => $object) {
                $summary[$object->{$propertyName}] = $object->$sum;
            }
        }

        return $summary;
    }

    /**
     * Read the children objects of a given parent using a foreign key navigation
     * @param string $childClassName  The class of the objects to find
     * @param mixed  $parentObject    The parent object to read children of
     * @param string $parentClassName The class of the parent object provided, if different from object class
     *
     * @return array An array of objects of requested class related to parent object
     */
    public function readChildren($childClassName, $parentObject, $parentClassName=false, $sortingString=false)
    {
        /* Get Sdo Query 'Read' */
        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_READ);

        $childClass = \laabs::getClass($childClassName);
        $query->setClass($childClass);

        if (!$parentClassName) {
            $parentClassName = \laabs::getClassName($parentObject);
        }

        $foreignKey = $this->getChildKey($childClass, $parentObject, $parentClassName);

        if (!$foreignKey) {
            throw new \Exception("No foreign key found to read child class $childClassName of $parentClassName");
        }
        $childKeyAssert = $foreignKey->getChildAssert();
        $query->addAssert($childKeyAssert);

        if ($sortingString) {
            $query->setSortings((new \core\Language\Parser())->parseSortingList($sortingString, $query));
        } else {
            if ($childClass->hasPrimaryKey()) {
                $key = $childClass->getPrimaryKey();
            } elseif ($childClass->hasKey()) {
                $keys = $childClass->getKeys();
                $key = reset($keys);
            }

            if ($key) {
                $sortingString = \implode(', ', $key->getFields());
                $query->setSortings((new \core\Language\Parser())->parseSortingList($sortingString, $query));
            }
        }

        /* Prepare statement */
        if ($this->isCluster()) {
            $stmt = $this->das[0]->prepare($query);
        } else {
            $stmt = $this->das->prepare($query);
        }

        /* Bind value of fkey to fkey parameter */
        $refKeyObject = $foreignKey->getParentObject($parentObject);

        $stmt->bindForeignKey($foreignKey->getRefClass(), $refKeyObject, $foreignKey);

        $result = $this->execute($stmt);

        if ($result) {
            return $stmt->fetchAll($childClassName);
        }
    }

    /**
     * Read the parent object of a given child using a foreign key navigation
     * @param string $parentClassName The class of the parent object to read
     * @param mixed  $childObject     An associative array, object, indexed array or scalar value representing the child
     * @param string $childClassName  The class of the child object
     *
     * @return object The object of requested class related to child object
     */
    public function readParent($parentClassName, $childObject, $childClassName=false)
    {
        /* Get Sdo Query 'Read' */
        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_READ);

        $parentClass = \laabs::getClass($parentClassName);
        $query->setClass($parentClass);
        
        if ($childClassName) {
            $childClass = \laabs::getClass($childClassName);
        } else {
            if (is_object($childObject)) {
                $childClass = \laabs::getClass($childObject);
            } 
            if (!$childClass) {
                throw new \Exception("Can't delete parent: You must provide either a child object of required class or a child class name.");
            }
        }

        $foreignKey = $this->getParentKey($childClass, $childObject, $parentClassName);
        if (!$foreignKey) {
            throw new \Exception("No foreign key found to read parent class $parentClassName of $childClassName");
        }
        $parentKeyAssert = $foreignKey->getParentAssert();
        $query->addAssert($parentKeyAssert);
        
        /* Prepare statement */
        if ($this->isCluster()) {
            $stmt = $this->das[0]->prepare($query);
        } else {
            $stmt = $this->das->prepare($query);
        }

        /* Bind value of fkey to fkey parameter */
        $childKeyObject = $foreignKey->getChildObject($childObject);
        $stmt->bindKey($foreignKey->getClass(), $childKeyObject, $foreignKey);

        $result = $this->execute($stmt);
        
        if ($result) {
            return $stmt->fetch($parentClassName);
        }
    }
    
    /**
     * Read a tree of a single class. Roots are objects without parents
     * @param string $className   The class of the object to read
     * @param string $queryString An optional filter on tree items
     * 
     * @return array The array of root objects with their branches
     */
    public function readTree($className, $queryString=false)
    {
        /* Get Sdo Query 'Read' */
        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_READ);

        $class = \laabs::getClass($className);
        $query->setClass($class);
        
        $selfKeys = $class->getForeignKeys($class->getName());
        if (count($selfKeys) == 0) {
            return array();
        }
        $selfKey = reset($selfKeys);
        foreach ($selfKey->getFields() as $keyField) {
            $assert = new \core\Language\ComparisonOperation(LAABS_T_EQUAL, $class->getProperty($keyField), new \core\Language\NullOperand(null));
            $query->addAssert($assert);
        }

        /* Add Asserts to statement */
        if ($queryString) {
            $parser = new \core\Language\Parser();
            $assert = $parser->parseAssert($queryString, $query);
            $query->addAssert($assert);
        }

        /* Prepare statement */
        if ($this->isCluster()) {
            $stmt = $this->das[0]->prepare($query);
        } else {
            $stmt = $this->das->prepare($query);
        }

        /* Execute statement */
        $result = $this->execute($stmt);
        
        /* Fetch all objects */
        if (!$result) {
            return array();
        }
            
        $roots = $stmt->fetchAll();

        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_READ);
        
        $query->setClass($class);
        
        $selfKeyAssert = $selfKey->getChildAssert();
        $query->addAssert($selfKeyAssert);
        
        /* Prepare statement */
        if ($this->isCluster()) {
            $stmt = $this->das[0]->prepare($query);
        } else {
            $stmt = $this->das->prepare($query);
        }

        foreach ($roots as $root) {
            $this->readBranches($class, $selfKey, $root, $stmt);
        }
            
        return $roots;
    }
    
    /**
     * Read branches of a given tree root
     * @param object $class        The class definition
     * @param object $selfKey      The key that references the objects on tree
     * @param object $parentObject The root or branch to read children of
     * @param object $stmt         The statement to select objects
     * @param int    $depth        The depth of tree
     *
     * @return void
     */
    protected function readBranches($class, $selfKey, $parentObject, $stmt, $depth=0)
    {
        /* Bind fkey */
        $refKeyObject = $selfKey->getParentObject($parentObject);

        $stmt->bindForeignKey($selfKey->getRefClass(), $refKeyObject, $selfKey);

        /* Execute statement */
        $result = $this->execute($stmt);

        /* Fetch all objects */
        if (!$result) {
            return;
        }
        
        $branchName = \laabs\basename($class->getBaseName());
        $branchObjects = $stmt->fetchAll();
        $parentObject->$branchName = $branchObjects;

        $depth++;
        foreach ($branchObjects as $branchObject) {
            $this->readBranches($class, $selfKey, $branchObject, $stmt, $depth);
        }
    }

    /**
     * Get the ancestor objects on an homogenic tree
     * @param string $className The class of the object 
     * @param string $object    The reference object
     *
     * @return array 
     */
    public function readAncestors($className, $object)
    {
        $ancestors = array();
        while ($object = $this->readParent($className, $className, $object)) {
            $ancestors[] = $object;
        }

        return $ancestors;
    }

    /**
     * Get the descendant objects in an homogenic hierechical tree
     * @param string $className The class of the object 
     * @param string $object    The reference object
     *
     * @return array The descendant objects
     */
    public function readDescendants($className, $object)
    {
        $class = \laabs::getClass($className);
                
        $selfKeys = $class->getForeignKeys($class->getName());
        if (count($selfKeys) == 0) {
            return;
        }
        $selfKey = reset($selfKeys);

        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_READ);
        $query->setClass($class);
        
        $selfKeyAssert = $selfKey->getChildAssert();
        $query->addAssert($selfKeyAssert);
        
        /* Prepare statement */
        if ($this->isCluster()) {
            $stmt = $this->das[0]->prepare($query);
        } else {
            $stmt = $this->das->prepare($query);
        }
        
        $descendants = $this->recursiveReadDescendants($class, $selfKey, $object, $stmt);

        return $descendants;
    }

    /**
     * Recursive getter for children
     * @param object $class   The class definition
     * @param object $selfKey The key that references the objects on tree
     * @param object $self    The current object
     * @param object $stmt    The statement to select objects
     *
     * @return array
     * @author 
     */
    protected function recursiveReadDescendants($class, $selfKey, $self, $stmt)
    {
        /* Bind value of fkey to fkey parameter */
        $selfKeyObject = $selfKey->getParentObject($self);
        $stmt->bindForeignKey($selfKey->getRefClass(), $selfKeyObject, $selfKey);

        $result = $this->execute($stmt);

        if (!$result) {
            return array();
        }

        $descendants = $stmt->fetchAll($class->getName());

        foreach ($descendants as $descendant) {
            $descendants = array_merge($descendants, $this->recursiveReadDescendants($class, $selfKey, $descendant, $stmt));
        }

        return $descendants;
    }
}
