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

class Statement
    implements \dependency\datasource\StatementInterface,
               \dependency\datasource\ResultSetInterface,
               \IteratorAggregate
{

    /* Properties */
    protected $queryString;
    protected $context;
    protected $Param = array();

    protected $XPath;
    protected $DOMNodeList;
    protected $cursor_offset = -1;

    /* Methods */
    public function __construct(\DOMDocument $Xml, $queryString=false, \DOMNode $context=null) {
        $this->XPath = new \dependency\xml\XPath($Xml);
        $this->queryString = $queryString;
        $this->context = $context;
        $this->registerPhpFunctions();
    }


    /**
     * Bind param
     * @param type $parameter
     * @param type $variable
     * @param type $type
     * @param type $length
     * @param type $driver_options
     */
    public function bindParam($parameter, &$variable, $type=Xml::PARAM_STR, $length=null, $driver_options=null) {
        $Param = new Param($parameter, $variable, $type, $length);
        $this->Param[$parameter] = $Param;
    }

    /**
     * Bind value
     * 
     * @param string $parameter
     * @param string $value
     * @param string $type
     */
    public function bindvalue($parameter, $value, $type=Xml::PARAM_STR) {
        $Param = new Param($parameter, $variable, $type, mb_strlen($value));
        $this->Param[$parameter] = $Param;
    }

    public function getErrors() {

    }

    /**
     * Execute
     * @param string $parameters
     * 
     * @return boolean
     */
    public function execute($parameters=null) {
        $queryString = $this->queryString;
        foreach ($this->Param as $name => $Param) {
            if ($parameters && isset($parameters[$name]))
                $value = "'" . $parameters[$name] . "'";
            else {
                $value = "'" . $Param->getValue() . "'";
            }

            $queryString = str_replace($name, $value, $queryString);
        }
        if ($this->DOMNodeList = $this->XPath->query($queryString, $this->context))
            return true;
        return false;
    }


    public function __get($name) {
        if ($name == 'length')
            return $this->DOMNodeList->length;

        trigger_error("Can not access readonly property $name");
    }

    /**
     * Get query string
     * 
     * @return string
     */
    public function getQueryString() {
        return $this->queryString;
    }

    /**
     * ResultSet methods
     * 
     * @return \DOMNode
     */
    public function fetch() {
        $cursor_offset = $this->cursor_offset++;
        return $this->DOMNodeList->item($cursor_offset);
    }

    /**
     * ResultSet all methods
     * 
     * @return \DOMNode
     */
    public function fetchAll() {
        return $this->DOMNodeList;
    }

    /**
     * Fetch item
     * @param integer $cursor_offset
     * 
     * @return \DOMNode
     */
    public function fetchItem($cursor_offset=0) {
        return $this->DOMNodeList->item($cursor_offset);
    }

    /**
     * Get iterator
     * @return \dependency\datasource\Adapter\Xml\ArrayIterator
     */
    public function getIterator() {
        return new ArrayIterator($this->DOMNodeList);
    }

}