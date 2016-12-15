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
namespace dependency\datasource\Adapter\Xml;
class Datasource
    extends \DOMDocument
    implements \dependency\datasource\DatasourceInterface
{
    /* Constants */
    const PARAM_NULL = 0;
    const PARAM_STR = 1;
    const PARAM_INT = 2;
    const PARAM_BOOL = 3;
    const QUOTE_STR = "'";
    /* Properties */
    protected $name;
    protected $driver = "Xml";
    protected $statementClass;
    protected $XPath;
    /* Methods */
    public function __construct($Dsn, $Username=null, $Password=null, array $Options=null) {
        $this->name = $Dsn;
        $XmlFile = substr($Dsn, 4);
        $XmlOptions = array_sum($Options);
        parent::__construct();
        $this->load($XmlFile, $XmlOptions);
        $this->setStatementClass();
    }
    
    /**
     * DatasourceInterface methods
     * @return string
     */
    public function getDriver() {
        return $this->driver;
    }
    
    /**
     * Get name
     * @return string
     */
    public function getName() {
        return $this->name;
    }
    
    /**
     * Exec
     * @param string $queryString
     * @param \DOMNode $context
     * 
     * @return integer
     */
    public function exec($queryString, \DOMNode $context=null) {
        $Statement = $this->newStatement($queryString, $context);
        $Statement->execute();
        return count($Statement->fetchAll());
    }
    
    /**
     * Prepare
     * @param string $queryString
     * @param \DOMNode $context
     * 
     * @return string
     */
    public function prepare($queryString, \DOMNode $context=null) {
        return $this->newStatement($queryString, $context);
    }
    
    /**
     * Query
     * @param string $queryString
     * @param \DOMNode $context
     * @param \DOMNode $registerNodeNS
     * 
     * @return string
     */
    public function query($queryString, \DOMNode $context=null, $registerNodeNS=null) {
        $Statement = $this->newStatement($queryString, $context);
        $Statement->execute();
        return $Statement;
    }
    
    /**
     * Quote
     * @param string $string
     * 
     * @return string
     */
    public function quote($string) {
        /* No quote in string, return string enclosed with quotes */
        if (strpos($string, "'") === false)
            return sprintf("'%s'", $string);
        /* Quote found: use concat function and return string enclosed with quotes */
        return sprintf("concat('%s')", str_replace("'", "',\"'\",'", $string));
    }
    
    public function getErrors() {
    }
    
    /**
     * Set statement class
     * @param string $class
     * @param array $args
     */
    public function setStatementClass($class=null, array $args=null) {
        if (is_null($class)) {
            $class = '\dependency\datasource\Adapter\Database\Statement';
            $args = null;
        }
        $this->statementClass = array($class, (array)$args);
    }
    protected function newStatement($queryString, $context=null) {
        $statementClass = new \ReflectionClass($this->statementClass[0]);
        $statementArgs = $this->statementClass[1];
        array_unshift($statementArgs, $context);
        array_unshift($statementArgs, $queryString);
        array_unshift($statementArgs, $this);
        return $statementClass->newInstanceArgs($statementArgs);
    }
}