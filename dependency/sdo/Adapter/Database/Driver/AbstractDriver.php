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
namespace dependency\sdo\Adapter\Database\Driver;
/**
 * Abstract class for database drivers
 */
abstract class AbstractDriver
    extends AbstractCompiler
    implements \dependency\sdo\Adapter\Database\DriverInterface
{
    /* Constants */

    /* Properties */
    public static $name;

    protected $ds;

    protected $schema;

    protected $bindings;

    protected $encoding;

    protected $dateFormat;

    protected $timeFormat;

    protected $datetimeFormat;

    protected $boolFormat;

    /**
     * Constructor
     * @param object $ds             The Sdo datasource, Model or Das
     * @param string $dateFormat     The date format for to_char and to_date convertions 
     * @param string $timeFormat     The time format for to_char and to_time convertions 
     * @param string $datetimeFormat The datetime format for to_char and to_date convertions 
     * @param int    $boolFormat     The bool format for php bool to db representation
     * @param string $encoding       The encoding
     * @param array  $bindings       An array of bindings between bundle/type/property and schema/table/column 
     * 
     * @return void 
     */
    public function __construct(
        \dependency\datasource\Adapter\Database\Datasource $ds, 
        $dateFormat=null, 
        $timeFormat=null,
        $datetimeFormat=null,
        $boolFormat=null, 
        $encoding="UTF8", 
        array $bindings=array())
    {
        $this->ds = $ds;

        $this->boolFormat = $boolFormat;

        $this->bindings = $bindings;

        $this->setDateFormat($dateFormat);
        $this->setTimeFormat($timeFormat);
        $this->setDatetimeFormat($datetimeFormat);
        
        /*$this->encoding = $encoding;
        if ($encoding) {
            $this->setEncoding();
        }*/

    }

    /**
     * Magic method to get the name of the driver
     * 
     * @return string The name of the driver
     */
    public function __toString()
    {
        return self::$name;
    }

    public function setBoolFormat($boolFormat=1) 
    {
        $this->boolFormat = $boolFormat;
    }

    public function getBoolFormat() 
    {
        return $this->boolFormat;
    }

    public function setDateFormat($dateFormat) 
    {
        $this->dateFormat = $dateFormat;
    }

    public function setTimeFormat($timeFormat) 
    {
        $this->timeFormat = $timeFormat;
    }

    /**
     * Get the date format
     * 
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }


    /**
     * Get type bundle/type/property => schema/table/column 
     * @param string $name
     * 
     * @return string The binded type
     */
    public function getBinding($name)
    {
        if (isset($this->bindings[$name])) {           
            return $this->bindings[$name];
        }

        $parts = \laabs\explode(LAABS_URI_SEPARATOR, $name);
        $bind = array();
        switch(count($parts)) {
            case 3:
                $bind[] = array_pop($parts);
                $qTypeName = \laabs\implode(LAABS_URI_SEPARATOR, $parts);
                if (isset($this->bindings[$qTypeName])) {           
                    array_unshift($bind, $this->bindings[$qTypeName]);
                    break;
                }
                // If not found, continue to 2

            case 2:
                array_unshift($bind, array_pop($parts));
                $bundleName = reset($parts);
                if (isset($this->bindings[$bundleName])) {           
                    array_unshift($bind, $this->bindings[$bundleName]);
                    break;
                }

            case 1:
                array_unshift($bind, reset($parts));
        }
        
        return implode(".", $bind);
    }

    

}