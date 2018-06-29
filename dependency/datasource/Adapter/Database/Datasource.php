<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency datasource.
 *
 * Dependency datasource is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency datasource is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency datasource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\datasource\Adapter\Database;

class Datasource
    implements \dependency\datasource\DatasourceInterface, \dependency\datasource\TransactionalInterface
{
    /* Constants */
    
    /* Properties */
    protected static $instances;

    protected $name;
    
    public $driver;
    
    public $pdo;
    
    /**
     * @var array $Options
     * Associative array of PDO class constructor options
     *  PDO::ATTR_AUTOCOMMIT
     *  PDO::ATTR_CASE
     *  PDO::ATTR_ERRMODE
     *  PDO::ATTR_ORACLE_NULLS
     *  PDO::ATTR_PERSISTENT
     *  PDO::ATTR_PREFETCH
     *  PDO::ATTR_TIMEOUT
     */
    protected $Options = array(
        //\PDO::ATTR_AUTOCOMMIT=> false,
        \PDO::ATTR_PERSISTENT => true
    );
    
    protected $PdoStatementOptions = array(
        \PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY
    );
    
    /**
     * array to declare the class and constructor arguments for statement
     * [0] => name of the class
     * [1] => array of arguments
     *      [0] => PDOStatement retrieved will be inserted at runtime
     *      [1] => mixed args...
     */
    protected $statementClass;

    /**
     * The nested transaction control
     * @var integer
     */
    protected $transactionLevel;
    
    /* Methods */
    /** 
     * Constructor
     */
    public function __construct($Dsn, $Username=null, $Password=null, $Options=null)
    {
        $this->driver = strtok($Dsn, ':');
        $this->name = strtok(':');

        $this->setStatementClass();

        if (!isset(self::$instances[$Dsn])) {
            self::$instances[$Dsn] = $this->newPdo($Dsn, $Username, $Password, (array) $this->Options);
        }

        $this->pdo = self::$instances[$Dsn];
    }

    public function __sleep()
    {
        return array();
    }

    /**
     * New pdo
     * @param type $Dsn
     * @param type $Username
     * @param type $Password
     * @param array $Options
     * 
     * @return \PDO
     */
    protected function newPdo($Dsn, $Username=null, $Password=null, array $Options=null)
    {
        $pdo = new \PDO($Dsn, $Username, $Password, $Options);

        // Set error mode to exception
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // When bool datatype is not available with the driver and a number (0/1) is used instead
        // As PDO does'nt have a FLOAT/DOUBLE param type, it is considered as string
        // But false to string gives empty string ''
        // And empty string conversion to NULL breaks NOT NULL constraints
        $pdo->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);

        return $pdo;
    }
   
    /* DatasourceInterface methods */
    /** 
     * Get the driver name
     * 
     * @return string
     */
    public function getDriver()
    {
        return $this->driver;
    }
    
    /** 
     * Get the datasource name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
   
    /** 
     * Execute a query string
     * @param string $queryString
     * 
     * @return mixed
     */
    public function exec($queryString) 
    {
        return $this->pdo->exec($queryString);
    }
    
    /** 
     * Query the ds
     * @param string $queryString
     * 
     * @return object
     */
    public function query($queryString) 
    {
        $pdoStatement = $this->pdo->query($queryString);

        $statement = $this->newStatement($pdoStatement);

        return $statement;
    }
    /**
     * Prepare the statement
     * 
     * @param type $queryString
     * @param array $Options
     * 
     * @return string
     */
    public function prepare($queryString)
    {
        $pdoStatement = $this->pdo->prepare($queryString);

        $statement = $this->newStatement($pdoStatement);
        
        return $statement;
    }
    
    public function quote($string)
    {
        return $this->pdo->quote($string);
    }
    
    public function getErrors()
    {
        return $this->pdo->errorInfo();
    }
    
    /* TransactionalInterface methods */
    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    
    public function commit()
    {
        return $this->pdo->commit();
    }
    
    public function rollback()
    {
        return $this->pdo->rollback(); 
    }
    
    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }
    
    /**
     * Custom Database methods
     * 
     * @param string $class
     * @param array $args
     */
    public function setStatementClass($class=null, array $args=null)
    {
        /* Back to default statement class and args */
        if (is_null($class)) {
            $class = '\dependency\datasource\Adapter\Database\Statement';
            $args = null;
        }
        $this->statementClass = array($class, (array) $args);
    }

    protected function newStatement($pdoStatement=null) 
    {
        if (!$pdoStatement) {
            $error = $this->pdo->errorInfo();

            throw new \Exception($error[2]);
        }

        $statementClass = new \ReflectionClass($this->statementClass[0]);
        $statementArgs = $this->statementClass[1];
        array_unshift($statementArgs, $pdoStatement);
        
        return $statementClass->newInstanceArgs($statementArgs);
    }
    
}