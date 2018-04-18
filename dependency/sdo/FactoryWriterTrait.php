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
 * Trait FactoryWriter
 * The API for Service Data Object Write queries
 */
trait FactoryWriterTrait
{
    /**
     * Create a new Sdo DataObject on the data storage
     * @param object $object    The data object
     * @param string $className The class of the object to be created, if different from object class
     *
     * @return bool True if object has been created, false if an error occured
     */
    public function create($object, $className = false)
    {
        if ($className) {
            $class = \laabs::getClass($className);
        } else {
            if (is_object($object)) {
                $class = \laabs::getClass($object);
            }
            if (!$class) {
                throw new \Exception("Can't create object: You must provide either an object of required class or a class name.");
            }

            $className = $class->getName();
        }

        $lqlString = 'CREATE ' . $className;

        if (isset($this->preparedStmts[$lqlString])) {
            if ($this->isCluster()) {
                $stmts = $this->preparedStmts[$lqlString];
            } else {
                $stmt = $this->preparedStmts[$lqlString];
            }
        } else {
            $query = new \core\Language\Query();
            $query->setCode(LAABS_T_CREATE);

            $query->setClass($class);

            $this->generateKey($class, $object);

            $createProperties = $class->getObjectProperties($object);

            $query->setProperties($createProperties);

            if ($this->isCluster()) {
                $stmts = array();
                $cluster = $this->getCluster($className);
                foreach ($cluster as $das) {
                    $stmts[] = $das->prepare($query);
                }
                $this->preparedStmts[$lqlString] = $stmts;
            } else {
                $stmt = $this->das->prepare($query);

                $this->preparedStmts[$lqlString] = $stmt;
            }
        }

        /* Prepare statement */
        if ($this->isCluster()) {
            $transactionControl = !$this->inTransaction();

            if ($transactionControl) {
                $this->beginTransaction();
            }

            foreach ($stmts as $stmt) {
                $stmt->bindObject($className, $object, $class);

                $exec = $this->execute($stmt);

                $created = $stmt->rowCount();

                if ($created !== 1) {
                    if ($transactionControl) {
                        $this->rollback();
                    }

                    throw new Exception\objectNotFoundException("Object of class $className could not be created");
                }
            }

            if ($transactionControl) {
                $this->commit();
            }

        } else {
            /* Bind object for data */
            $stmt->bindObject($className, $object, $class);

            $exec = $this->execute($stmt);

            $created = $stmt->rowCount();

            if ($created !== 1) {
                throw new Exception\objectNotFoundException("Object of class $className could not be created");
            }
        }

        return true;
    }

    /**
     * Create a batch of DataObject on the data storage
     * @param array  $objects   The array of object
     * @param string $className The class of the object to be created
     *
     * @return bool True if objects has been created, false if an error occured
     */
    public function createCollection(array $objects, $className)
    {
        $transactionControl = !$this->inTransaction();

        if ($transactionControl) {
            $this->beginTransaction();
        }

        try {
            $query = new \core\Language\Query();
            $query->setCode(LAABS_T_CREATE);

            $class = \laabs::getClass($className);
            $query->setClass($class);

            $createProperties = $class->getProperties();

            $query->setProperties($createProperties);

            if ($this->isCluster()) {
                $stmts = array();
                $cluster = $this->getCluster($className);
                foreach ($cluster as $das) {
                    $stmt = $das->prepare($query);

                    $stmts[] = $stmt;
                }
            } else {
                $stmt = $this->das->prepare($query);
            }

            foreach ($objects as $object) {
                $this->generateKey($class, $object);

                /*$createProperties = $class->getObjectProperties($object);

                $query->setProperties($createProperties);*/

                /* Prepare statement */
                if ($this->isCluster()) {
                    foreach ($stmts as $stmt) {
                        /* Bind object for data */
                        $stmt->bindObject($className, $object, $class);

                        $exec = $this->execute($stmt);

                        $created = $stmt->rowCount();

                        if ($created !== 1) {
                            throw new Exception\objectNotFoundException("Object of class $className could not be created");
                        }
                    }

                } else {
                    /* Bind object for data */
                    $stmt->bindObject($className, $object, $class);

                    $exec = $this->execute($stmt);

                    $created = $stmt->rowCount();

                    if ($created !== 1) {
                        throw new Exception\objectNotFoundException("Object of class $className could not be created");
                    }
                }
            }

        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->commit();
        }

        return $created;
    }

    /**
     * Update an object from storage
     * @param string $object    The data object holding data to update
     * @param string $className The class of the objects to update, if different from object class
     * @param mixed  $keyValue  A scalar, associative array, indexed array or object representing the univoque key to use for object retrieval
     *  Passing an associative array key value will allow to guess which key should be used, else the primary key will be used if exists
     *
     * @return bool The success of failure of operation
     */
    public function update($object, $className = false, $keyValue = false)
    {
        if (!$keyValue) {
            $keyValue = $object;
        }

        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_UPDATE);

        if ($className) {
            $class = \laabs::getClass($className);
        } else {
            if (is_object($object)) {
                $class = \laabs::getClass($object);
            }

            $className = $class->getName();
        }

        if (!$class) {
            throw new \Exception("Can't update object: You must provide either an object of required class or a class name.");
        }

        $query->setClass($class);

        $key = $this->getKey($class, $keyValue);

        if (!$key) {
            throw new \Exception("No key found to update class $className");
        }
        $keyAssert = $key->getAssert();
        $query->addAssert($keyAssert);

        $updateProperties = $class->getObjectProperties($object);
        $query->setProperties($updateProperties);

        $keyObject = $key->getObject($keyValue);

        /* Prepare statement */
        if ($this->isCluster()) {
            $stmts = array();
            $cluster = $this->getCluster($className);
            foreach ($cluster as $das) {
                $stmt = $das->prepare($query);

                /* Bind object for data */
                $stmt->bindObject($className, $object, $class);

                $stmt->bindKey($key->getClass(), $keyObject, $key);

                $stmts[] = $stmt;
            }

            $transactionControl = !$this->inTransaction();

            if ($transactionControl) {
                $this->beginTransaction();
            }

            foreach ($stmts as $stmt) {
                $executed = $this->execute($stmt);

                $updated = $stmt->rowCount();

                if ($updated !== 1) {
                    if ($transactionControl) {
                        $this->rollback();
                    }

                    throw new Exception\objectNotFoundException("Object of class $className could not be updated");
                }
            }

            if ($transactionControl) {
                $this->commit();
            }

        } else {
            $stmt = $this->das->prepare($query);

            /* Bind object for data */
            $stmt->bindObject($className, $object, $class);
            $stmt->bindKey($key->getClass(), $keyObject, $key);

            $executed = $this->execute($stmt);

            $updated = $stmt->rowCount();

            /*if ($updated !== 1) {
                throw new Exception\objectNotFoundException("Object of class $className could not be updated");
            }*/
        }

        return true;
    }

        /**
     * Update an object from storage
     * @param string $object      The data object holding data to update
     * @param string $queryString The query (update) expression, encoded in Laabs Query Language
     * @param mixed  $keyValue    A scalar, associative array, indexed array or object representing the univoque key to use for object retrieval
     *  Passing an associative array key value will allow to guess which key should be used, else the primary key will be used if exists
     *
     * @return bool The success of failure of operation
     */
    public function updateCollection($className = false, $keyValue = false, $queryString=false)
    {
        $lqlString = 'UPDATE';
                
        $lqlString .= ' ' . $className;

        if ($queryString) {
            $lqlString .= " (" . $queryString .")";
        }

        if (isset($this->preparedStmts[$lqlString])) {
            $stmt = $this->preparedStmts[$lqlString];
        } else {
            // Get query object
            $query = \core\Language\Query::parse($lqlString);
            
            $class = \laabs::getClass($className);
            $query->setClass($class);

            $updateProperties = $class->getObjectProperties((object) $keyValue);
            $query->setProperties($updateProperties);
            /* Prepare statement */
            if ($this->isCluster()) {
                $stmt = $this->das[0]->prepare($query);
            } else {
                $stmt = $this->das->prepare($query);
            }

            $this->preparedStmts[$lqlString] = $stmt;
        }

        $object = (object) $keyValue;
        $stmt->bindObject($className, $object, $class);

        $executed = $this->execute($stmt);

        $updated = $stmt->rowCount();

        return true;
    }

    /**
     * Delete an object from storage
     * @param mixed  $object    The data holding identifiers of what to delete
     * @param string $className The class of the objects to update, if different from object class
     *
     * @return bool The success of failure of operation
     */
    public function delete($object, $className = false)
    {
        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_DELETE);

        if ($className) {
            $class = \laabs::getClass($className);
        } else {
            if (is_object($object)) {
                $class = \laabs::getClass($object);
            }

            $className = $class->getName();
        }

        if (!isset($class)) {
            throw new \Exception("Can't delete object: You must provide either an object of required class or a class name.");
        }

        $query->setClass($class);

        $key = $this->getKey($class, $object);
        if (!$key) {
            throw new \Exception("No key found to delete class $className");
        }
        $keyAssert = $key->getAssert();
        $query->addAssert($keyAssert);

        $keyObject = $key->getObject($object);

        if ($this->isCluster()) {
            $stmts = array();
            $cluster = $this->getCluster($className);
            foreach ($cluster as $das) {
                $stmt = $das->prepare($query);

                $stmt->bindKey($key->getClass(), $keyObject, $key);

                $stmts[] = $stmt;
            }

            $transactionControl = !$this->inTransaction();
            if ($transactionControl) {
                $this->beginTransaction();
            }

            foreach ($stmts as $stmt) {
                $executed = $this->execute($stmt);

                $deleted = $stmt->rowCount();

                if ($deleted !== 1) {
                    if ($transactionControl) {
                        $this->rollback();
                    }

                    throw new Exception\objectNotFoundException("Object of class $className could not be deleted");
                }
            }

            if ($transactionControl) {
                $this->commit();
            }

        } else {
            $stmt = $this->das->prepare($query);

            $stmt->bindKey($key->getClass(), $keyObject, $key);

            $executed = $this->execute($stmt);

            $deleted = $stmt->rowCount();

            if ($deleted !== 1) {
                throw new Exception\objectNotFoundException("Object of class $className could not be deleted");
            }
        }

        return true;
    }

    /**
     * Delete a batch of DataObject on the data storage
     * @param mixed  $collection The array of object/arrays standing for key values
     * @param string $className  The class of the object to be deleted
     *
     * @return bool True if objects has been deleted, false if an error occured
     */
    public function deleteCollection(array $collection, $className)
    {
        $transactionControl = !$this->inTransaction();
        if ($transactionControl) {
            $this->beginTransaction();
        }

        try {
            $query = new \core\Language\Query();
            $query->setCode(LAABS_T_DELETE);

            $class = \laabs::getClass($className);
            $query->setClass($class);

            foreach ($collection as $object) {
                if (!$this->exists($className, $object)) {
                    continue;
                }

                $key = $this->getKey($class, $object);
                if (!$key) {
                    throw new \Exception("No key found to delete class $className");
                }

                $keyAssert = $key->getAssert();
                $query->setAsserts(array($keyAssert));

                $keyObject = $key->getObject($object);

                /* Prepare statement */
                if ($this->isCluster()) {
                    $stmts = array();
                    $cluster = $this->getCluster($className);
                    foreach ($cluster as $das) {
                        $stmt = $das->prepare($query);

                        $stmt->bindKey($key->getClass(), $keyObject, $key);

                        $result = $this->execute($stmt);

                        $deleted = $stmt->rowCount();

                        if ($deleted !== 1) {
                            throw new Exception\objectNotFoundException("Object of class $className could not be deleted");
                        }
                    }
                } else {
                    $stmt = $this->das->prepare($query);

                    $stmt->bindKey($key->getClass(), $keyObject, $key);

                    $result = $this->execute($stmt);

                    $deleted = $stmt->rowCount();

                    if ($deleted !== 1) {
                        throw new Exception\objectNotFoundException("Object of class $className could not be deleted");
                    }
                }

            }

        } catch (\Exception $exception) {
            if ($transactionControl) {
                $rollbacked = $this->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $committed = $this->commit();
        }

        return $deleted;
    }

    /**
     * Delete the children objects of a given parent using a foreign key navigation
     * @param string $childClassName  The class of the objects to delete
     * @param mixed  $parentObject    The parent object
     * @param string $parentClassName The class of the parent object provided
     *
     * @return int The number of children deleted
     */
    public function deleteChildren($childClassName, $parentObject, $parentClassName = false)
    {
        $childClass = \laabs::getClass($childClassName);

        /* Get Sdo Statement 'Read' */
        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_DELETE);

        $query->setClass($childClass);

        if (!$parentClassName) {
            if (is_object($parentObject)) {
                $parentClassName = \laabs::getClassName($parentObject);
            }
            if (!$parentClassName) {
                throw new \Exception("Can't delete child objects: You must provide either a parent object of required class or a parent class name.");
            }
        }

        $foreignKey = $this->getChildKey($childClass, $parentObject, $parentClassName);
        if (!$foreignKey) {
            throw new \Exception("No foreign key found to read child class $childClassName of $parentClassName");
        }

        $childKeyAssert = $foreignKey->getChildAssert();
        $query->addAssert($childKeyAssert);

        $refKeyObject = $foreignKey->getParentObject($parentObject);

        if ($this->isCluster()) {
            $stmts = array();
            $cluster = $this->getCluster($childClassName);
            foreach ($cluster as $das) {
                $stmt = $das->prepare($query);

                /* Bind value of fkey to fkey parameter */
                $stmt->bindForeignKey($foreignKey->getRefClass(), $refKeyObject, $foreignKey);

                $stmts[] = $stmt;
            }

            $transactionControl = !$this->inTransaction();
            if ($transactionControl) {
                $this->beginTransaction();
            }

            try {
                foreach ($stmts as $stmt) {
                    $executed = $this->execute($stmt);

                    $deleted = $stmt->rowCount();
                }
            } catch (\Exception $e) {
                if ($transactionControl) {
                    $this->rollback();
                }

                throw $e;
            }

            if ($transactionControl) {
                $this->commit();
            }

        } else {
            $stmt = $this->das->prepare($query);

            /* Bind value of fkey to fkey parameter */
            $stmt->bindForeignKey($foreignKey->getRefClass(), $refKeyObject, $foreignKey);

            $executed = $this->execute($stmt);

            $deleted = $stmt->rowCount();

        }


        return $deleted;
    }

    /**
     * Delete the parent object of a given child using a foreign key navigation
     * @param string $parentClassName The class of the parent object to delete
     * @param mixed  $childObject     An associative array, object, indexed array or scalar value representing the child
     * @param string $childClassName  The class of the child object
     *
     * @return bool
     */
    public function deleteParent($parentClassName, $childObject, $childClassName = false)
    {
        /* Get Sdo Model Statement 'ReadByKey' */
        $query = new \core\Language\Query();
        $query->setCode(LAABS_T_DELETE);

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

        $keyObject = $foreignKey->getParentObject($childObject);

        if ($this->isCluster()) {
            $stmts = array();
            $cluster = $this->getCluster($parentClassName);
            foreach ($cluster as $das) {
                $stmt = $das->prepare($query);

                $stmt->bindKey($foreignKey->getClass(), $keyObject, $foreignKey);

                $stmts[] = $stmt;
            }

            $transactionControl = !$this->inTransaction();
            if ($transactionControl) {
                $this->beginTransaction();
            }

            foreach ($stmts as $stmt) {
                $executed = $this->execute($stmt);

                $deleted = $stmt->rowCount();

                if ($deleted !== 1) {
                    if ($transactionControl) {
                        $this->rollback();
                    }

                    throw new Exception\objectNotFoundException("Object of class $className could not be deleted");
                }
            }

            if ($transactionControl) {
                $this->commit();
            }

        } else {
            $stmt = $this->das->prepare($query);

            /* Bind value of fkey to fkey parameter */
            $stmt->bindKey($foreignKey->getClass(), $keyObject, $foreignKey);

            $executed = $this->execute($stmt);

            $deleted = $stmt->rowCount();

            if ($deleted !== 1) {
                throw new Exception\objectNotFoundException("Object of class $className could not be deleted");
            }
        }

        return $deleted;
    }

    /**
     * Save an entire hierarchical tree of objects
     * Root object may have been created or updated, child objects may have been added, updated or deleted
     * @param string $className The class of object to save
     * @param object $object    The object hodling the data
     *
     * @return bool
     */
    public function save($className, $object)
    {
        if (!$this->schema->hasClass($className)) {
            return false;
        }

        $this->beginTransaction();

        if ($this->exists($className, $object)) {
            if (!$this->update($className, $object)) {
                $this->rollback();

                return false;
            }
        } else {
            if (!$this->create($className, $object)) {
                $this->rollback();

                return false;
            }
        }

        if (!$this->saveRecursive($className, $object)) {
            $this->rollback();

            return false;
        }

        return $this->commit();
    }

    /**
     * Save a tree of objects
     * @param string $class  The class definitions of object to save
     * @param object $object The object hodling the data
     *
     * @return bool
     */
    protected function saveRecursive($class, $object)
    {
        foreach ($object as $name => $value) {
            switch (true) {
                case is_scalar($value):
                    break;

                case is_object($value):
                    if (!$this->saveRecursive($name, $value)) {
                        $this->rollback();

                        return false;
                    }
                    break;

                case is_array($value):
                    foreach ($value as $childObject) {
                        if (!$this->saveRecursive($name, $value)) {
                            $this->rollback();

                            return false;
                        }
                    }
                    break;
            }
        }

        return true;
    }
}
