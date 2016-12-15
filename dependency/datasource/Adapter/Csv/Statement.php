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
namespace dependency\datasource\Adapter\Csv;

class Statement
    implements \dependency\datasource\StatementInterface,
               \dependency\datasource\ResultSetInterface,
               \IteratorAggregate
{

    /* Properties */
    protected $queryString;
    protected $Param = array();

    protected $delimiter;
    protected $enclosure;
    protected $escape;
    protected $header;

    protected $operation;

    protected $handle;

    protected $Lines;

    protected $cursor_offset = -1;

    /* Methods */
    public function __construct($queryString, $delimiter, $enclosure, $escape, $header) {

        $this->queryString = $queryString;

        $this->operation = strtok($queryString, " ");
        $this->handle = fopen(strtok(" "));

        $this->delimiter = $delimiter;
        $this->enclosure = $enclosure;
        $this->escape    = $escape;
        $this->header    = $header;
    }

    /**
     * Statement method
     * 
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

    public function bindvalue($parameter, $value, $type=Xml::PARAM_STR) {
        $Param = new Param($parameter, $variable, $type, mb_strlen($value));
        $this->Param[$parameter] = $Param;
    }

    public function getErrors() {

    }

    /**
     * Execute a query string
     * 
     * @param type $parameters
     * 
     * @return boolean
     */
    public function execute($parameters=null) {
        $queryString = $this->queryString;
        foreach ($this->Param as $name => $Param) {
            if ($parameters && isset($parameters[$name]))
                $value = $this->enclosure . $parameters[$name] . $this->enclosure;
            else {
                $value = $this->enclosure . $Param->getValue() . $this->enclosure;
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
     * Get Query String
     * 
     * @return string
     */
    public function getQueryString() {
        return $this->queryString;
    }

    public function fetch() {
        $cursor_offset = $this->cursor_offset++;
        return $this->DOMNodeList->item($cursor_offset);
    }

    public function fetchAll() {
        return $this->DOMNodeList;
    }

    public function fetchItem($cursor_offset=0) {
        return $this->DOMNodeList->item($cursor_offset);
    }

    public function getIterator() {
        return new ArrayIterator($this->DOMNodeList);
    }

}