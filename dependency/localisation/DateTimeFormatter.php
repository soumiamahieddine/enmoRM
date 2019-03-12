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
    protected $dateTimeFormat;
    protected $timestampFormat;
    protected $timezone;
    protected $locale;


    /* Methods */
    /**
     * Constructor
     * @param string $dateFormat      The default output format for dates
     * @param string $dateTimeFormat  The default output format for date-times (without microseconds)
     * @param string $timestampFormat The default output format for timestamps
     * @param string $timezone        The default timezone
     * @param string $locale          The target locale
     *
     * @return void
     **/
    public function __construct($dateFormat = 'Y-m-d', $dateTimeFormat = 'Y-m-d H:i:s \(P\)', $timestampFormat = 'Y-m-d\TH:i:s.u\Z', $timezone = 'UTC', $locale = false)
    {
        $this->dateFormat = $dateFormat;
        $this->dateTimeFormat = $dateTimeFormat;
        $this->timestampFormat = $timestampFormat;
        $this->timezone = $timezone;

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
     * Formats a timestamp string from a dateTime object
     * @param \DateTime $dateTime
     * 
     * @return string
     */
    public function formatTimestamp(\DateTime $dateTime)
    {
        $formattedDateTime = clone($dateTime);

        if (!empty($this->timezone)) {
            $formattedDateTime->setTimezone(new \core\Type\TimeZone($this->timezone));
        }

        return $formattedDateTime->format($this->timestampFormat);
    }

    /**
     * Formats a dateTime string from a dateTime object
     * @param \DateTime $dateTime
     * 
     * @return string
     */
    public function formatDateTime(\DateTime $dateTime)
    {
        $formattedDateTime = clone($dateTime);

        if (!empty($this->timezone)) {
            $formattedDateTime->setTimezone(new \core\Type\TimeZone($this->timezone));
        }

        return $formattedDateTime->format($this->dateTimeFormat);
    }

    /**
     * Formats a date string from a dateTime object
     * @param \DateTime $dateTime
     *
     * @return string
     */
    public function formatDate(\DateTime $dateTime)
    {
        return $dateTime->format($this->dateFormat);
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

