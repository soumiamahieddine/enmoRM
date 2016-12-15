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

class Datasource
    implements \dependency\datasource\DatasourceInterface
{
    /* Constants */

    /* Properties */
    protected $name;

    protected $delimiter = ",";
    protected $enclosure = '"';
    protected $escape    = "\\";
    protected $header    = false;
    protected $extension = '.csv';
    protected $eol       = PHP_EOL;

    protected $statementClass;


    public function __construct($Dsn, $Username=null, $Password=null, array $Options=null) 
    {
        $this->name = $Dsn;

        if ($Options) {
            foreach ($Options as $name => $value) {
                if (property_exists($this, $name)) {
                    $this->$name = $value;
                }
            }
        }

        $this->setStatementClass();
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
     * Custom Database methods
     * 
     * @param string $class
     * @param array $args
     */
    public function setStatementClass($class=null, array $args=null) 
    {
        /* Back to default statement class and args */
        if (is_null($class)) {
            $class = '\dependency\datasource\Adapter\Csv\Statement';
            $args = array($this->delimiter, $this->enclosure, $this->escape, $this->header, $this->extension, $this->eol);
        }

        $this->statementClass = array($class, (array)$args);
    }

    /**
     * Execute a query string
     * 
     * @param type $queryString
     * 
     * @return mixed
     */
    public function exec($queryString) 
    {
        $queryString = trim($queryString);

        // Find second space to substring data
        $steps = explode(" ", $queryString);
        $operation = array_shift($steps);
        $objectname = array_shift($steps);

        $data = implode(" ", $steps);

        switch(strtoupper($operation)) {
        case 'CREATE':
            // CREATE dir/file csvData (indexed or assoc)
            return $this->execCreate($objectname, $data);

        case 'READ':
            // CREATE dir/file columnNames
            return $this->execRead($objectname, $data);

        case 'UPDATE':
        case 'DELETE':
            break;
        }
    }

    /**
     * Query the ds
     * 
     * @param type $queryString
     */
    public function query($queryString) 
    {
        $queryString = trim($queryString);

        // Find second space to substring data
        $steps = explode(" ", $queryString);
        $operation = array_shift($steps);
        $objectname = array_shift($steps);

        $data = implode(" ", $steps);

        switch(strtoupper($operation)) {
        case 'CREATE':
            $this->execCreate($objectname, $data);
            break;
            
        case 'READ':
        case 'UPDATE':
        case 'DELETE':
            break;
        }
    }

    public function prepare($queryString) 
    {
        
    }

    public function quote($string) 
    {
        return str_replace($this->enclosure, $this->escape . $this->enclosure, $string);
    }

    public function getErrors() 
    {

    }

    protected function execCreate($objectname, $data) 
    {
        $handle = fopen($this->name. DIRECTORY_SEPARATOR . str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $objectname) . $this->extension, "a+");
        $result = fwrite($handle, $data. $this->eol);
        fclose($handle);
    }

    protected function execRead($objectname, $data) 
    {
        $handle = fopen($this->name. DIRECTORY_SEPARATOR . str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $objectname) . $this->extension, "a+");
        $result = fwrite($handle, $data. $this->eol);
        fclose($handle);
    }

}