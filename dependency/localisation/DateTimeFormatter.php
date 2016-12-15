<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency localisation.
 *
 * Dependency localisation is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency localisation is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency localisation.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\localisation;
/**
 * Date formatter
 * 
 * @package Localisation
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */ 
class DateTimeFormatter
{
    /* Constants */

    /* Properties */
    protected $dateFormat;

    protected $locale;


    /* Methods */
    /**
     * Constructor
     * @param string $outputFormat The default output format for dates
     * @param string $locale       The target locale
     *
     * @return void
     * @author 
     **/
    public function __construct($dateFormat=false, $locale=false)
    {
        $this->dateFormat = $dateFormat;

        if ($locale) {
            $this->setLocale($locale);
        }
    }

    /**
     * Set the target locale for date times
     * @param string $locale The locale indentifer
     */
    public function setLocale($locale) 
    {
        setlocale(\LC_TIME, $locale);
        $this->locale = $locale;
    }

    /**
     * Get a formatted date/time
     * @param string $time         A valid date/time string. 
     * @param string $inputFormat  A PHP date() format to use
     * 
     * @return string The formatted time
     */
    public function format($time, $inputFormat=false) 
    {
        //var_dump("format " . $time . " with format " . $dateFormat);
        /*if ($timezone) {
            $datetimeZone = new DateTimeZone($timezone);
        } else {
            $datetimeZone = null;
        }*/
        if ($inputFormat) {
            $datetime = date_create_from_format($inputFormat, $time);
        } else {
            $datetime = date_create($time);
        }

        if ($datetime) {
            return $datetime->format($this->dateFormat);
        }

        return $time;
    }   

}

