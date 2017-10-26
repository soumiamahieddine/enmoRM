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

class pgsql
    extends AbstractDriver
{
    /* Constants */

    /* Properties */
    public static $name = "pgsql";

    public static $maxNameLength;

    /**
     * Set the date format
     * @param string $timeFormat
     */
    public function setDateFormat($dateFormat) 
    {
        if (!$dateFormat) {
            $dateFormat = "dd-mm-yyyy";
        }

        $this->dateFormat = $dateFormat;
    }   

    /**
     * Set the time format
     * @param string $timeFormat
     */
    public function setTimeFormat($timeFormat) 
    {
        if (!$timeFormat) {
            $timeFormat = "hh24:mi:ss";
        }

        $this->timeFormat = $timeFormat;
    }

    /**
     * Set the date time format
     * @param string $datetimeFormat
     */
    public function setDatetimeFormat($datetimeFormat) 
    {
        if (!$datetimeFormat) {
            $datetimeFormat = "YMD";
        }

        $this->datetimeFormat = $datetimeFormat;

        //$this->ds->exec("SET datestyle TO ".$datetimeFormat);
    }

    /**
     * Set the encoding for texts
     * @param string $encoding The sql specific encoding
     */
    public function setEncoding($encoding) 
    {
        $this->ds->exec("SET client_encoding TO '".$encoding."'");
    }


}