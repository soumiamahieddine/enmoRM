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

class Statement
    implements \dependency\datasource\StatementInterface,
               \dependency\datasource\ResultSetInterface,
               \IteratorAggregate
{
    /* Constants */

    /* Properties */
    protected $pdoStatement;

    /* Methods */
    /**
     * Constructor
     * @param \PDOStatement $pdoStatement The PDO Statement object
     */
    public function __construct(\PDOStatement $pdoStatement)
    {
        $this->pdoStatement = $pdoStatement;
    }

    /* Statement methods */
    /**
     * Bind a parameter
     * @param string  $name
     * @param mixed   &$variable
     * @param string  $type
     * @param integer $length
     * @param array   $driver_options
     * @param string  $ref
     *
     * @return bool
     */
    public function bindParam($name, &$variable, $type=\PDO::PARAM_STR, $length=null, $driver_options=array(), $ref=false)
    {
        $Param = new Param($name, $variable, $type, $length, $ref);
        $this->params[$name] = $Param;

        return $this->pdoStatement->bindParam(':' . $name, $variable, $type, $length);
    }

    /**
     * Bind a value
     * @param string $name
     * @param string $value
     * @param string $type
     *
     * @return bool
     */
    public function bindValue($name, $value, $type=\PDO::PARAM_STR)
    {
        $this->params[$name] = $value;

        return $this->pdoStatement->bindValue(':' . $name, $value, $type);
    }

    /**
     * Return error
     *
     * @return \core\Error
     */
    public function getError()
    {
        $errInfo = $this->pdoStatement->errorInfo();

        return new \core\Error($errInfo[2], null, $errInfo[0], null, null, $errInfo);
    }

    /**
     * execute
     * @param string $inputParameters
     *
     * @return bool
     */
    public function execute($inputParameters=null)
    {
        if (!empty($inputParameters) && \laabs\is_assoc($inputParameters)) {
            foreach ($inputParameters as $name => $value) {
                $inputParameters[":" . $name] = str_replace("*", "%", $value);
                $inputParameters[":" . $name] = $value;
                unset($inputParameters[$name]);
            }
        } else {
            $inputParameters = null;
        }

        return $this->pdoStatement->execute($inputParameters);
    }

    /**
     * Get params
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Debug params
     * @return string
     */
    public function debugDumpParams()
    {
        return $this->pdoStatement->debugDumpParams();
    }

    /**
     * ResultSet methods
     *
     * @param string $class
     * @param array $ctor_args
     *
     * @return object
     */
    public function fetch($class="\stdClass", array $ctor_args=array())
    {
        $object = $this->pdoStatement->fetchObject($class, $ctor_args);

        if ($object) {
            foreach ($object as $name => $value) {
                if (gettype($value) == 'resource') {
                    $object->$name = stream_get_contents($value);
                }
            }
        }

        return $object;
    }

    /**
     * ResultSet all methods
     *
     * @param string $class
     * @param array $ctor_args
     *
     * @return object
     */
    public function fetchAll($class="\stdClass", array $ctor_args=array())
    {
        $resultSet = array();
        // Fix abnormal behavious of PDOStatement::fetchAll
        // Only ONE pointer used for LOBs so resources will all point to the last LOB retrieved
        while ($object = $this->pdoStatement->fetchObject($class, $ctor_args)) {
            foreach ($object as $name => $value) {
                if (gettype($value) == 'resource') {
                    $object->$name = stream_get_contents($value);
                }
            }
            $resultSet[] = $object;
        }

        return $resultSet;
    }

    /**
     * Fetch item
     * @param integer $cursor_offset
     * @param string $class
     *
     * @return string
     */
    public function fetchItem($cursor_offset=0, $class="\stdClass")
    {
        $this->setFetchMode(\PDO::FETCH_CLASS, $class);

        return $this->pdoStatement->fetch(\PDO::FETCH_CLASS, \PDO::FETCH_ORI_ABS, $cursor_offset);
    }

    /**
     * Fetch column
     * @param integer $offset
     *
     * @return string
     */
    public function fetchColumn($offset=0)
    {
        return $this->pdoStatement->fetchColumn($offset);
    }

    /**
     * Row count
     *
     * @return integer
     */
    public function rowCount()
    {
        return $this->pdoStatement->rowCount();
    }

    /**
     * Get query string
     *
     * @return string
     */
    public function getQueryString()
    {
        return $this->pdoStatement->queryString;
    }

    /**
     * Get iterator
     *
     * @return \dependency\datasource\Adapter\Database\ArrayIterator
     */
    public function getIterator()
    {
        $DataSet = $this->fetchAll();

        return new ArrayIterator($DataSet);
    }

    public function __sleep()
    {
        return array();
    }
}
