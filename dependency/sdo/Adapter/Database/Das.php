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

class Das
    extends \dependency\datasource\Adapter\Database\Datasource
    implements \dependency\sdo\DasInterface,
               \dependency\datasource\TransactionalInterface
{

    /* Methods */
    public function __construct(
        $Dsn, 
        $Username=null, 
        $Password=null, 
        $Options=null, 
        $bindings=array(), 
        $dateFormat=null, 
        $timeFormat=null,
        $datetimeFormat=null,
        $boolFormat=null, 
        $encoding="UTF8")
    {
        parent::__construct($Dsn, $Username, $Password, $Options);

        $this->pdo->setAttribute(\PDO::ATTR_CASE, \PDO::CASE_NATURAL);
        
        $driverClass = __NAMESPACE__ . LAABS_NS_SEPARATOR . "Driver" . LAABS_NS_SEPARATOR . $this->driver;
        $this->driver = new $driverClass($this, $dateFormat, $timeFormat, $datetimeFormat, $boolFormat, $encoding, $bindings);

    }

    /**
     * Prepare a SDO DAS Statement
     * @param \core\Language\Query $query The Query object
     * 
     * @return object The prepared statement
     */
    public function prepare($query) 
    {
        if (is_string($query)) {
            $this->setStatementClass();

            return parent::prepare($query); 
        }

        $queryString = $this->driver->getQueryString($query);

        if (!$queryString) {
            throw new Exception("Error when parsing the query");
        }

        $this->setStatementClass('\dependency\sdo\Adapter\Database\Statement', array($query, $this->driver));

        $stmt = parent::prepare($queryString);

        return $stmt;
    }

    /**
     * Query a SDO DAS Statement
     * @param \core\Language\Query $query The Query object
     * 
     * @return object The executed statement result set
     */
    public function query($query) 
    {
        if (is_string($query)) {
            $this->setStatementClass();

            return parent::query($query);
        } 
        
        $queryString = $this->driver->getQueryString($query);

        if (!$queryString) {
            throw new Exception("Error when parsing the query");
        }

        $this->setStatementClass('\dependency\sdo\Adapter\Database\Statement', array($query, $this->driver));

        // LAABS Variables found will be evaluated and replaced as STRING values
        foreach ($this->driver->getVariables() as $param => $value) {
            $queryString = str_replace($param, $this->parseString($value), $queryString);
        }

        $stmt = parent::query($queryString);

        return $stmt;
    }
    
}
