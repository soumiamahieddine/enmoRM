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
 * Driver for OCI das
 */
class oci
    extends AbstractDriver
{
    /* Constants */

    /* Properties */
    public static $name = "oci";

    public static $maxNameLength = 30;

    /**
     * Set the date format
     * @param string $dateFormat The sql specific date format argument
     */
    public function setDateFormat($dateFormat) 
    {
        if (!$dateFormat) {
            $dateFormat = "YYYY-MM-DD";
        }
        $this->dateFormat = $dateFormat;
        //$this->ds->exec("ALTER SESSION SET nls_date_format='".$dateFormat."'");
    }

    /**
     * Set the time format
     * @param string $timeFormat The sql specific time format argument
     */
    public function setTimeFormat($timeFormat) 
    {
        if (!$timeFormat) {
            $timeFormat = "HH24:MI:SS";
        }
        $this->timeFormat = $timeFormat;
        //$this->ds->exec("ALTER SESSION SET nls_time_format='".$timeFormat."'");
    }

    /**
     * Set the datetime format
     * @param string $datetimeFormat The sql specific time format argument
     */
    public function setDatetimeFormat($datetimeFormat) 
    {
        if (!$datetimeFormat) {
            $datetimeFormat = "YYYY-MM-DD HH24:MI:SS";
        }
        $this->datetimeFormat = $datetimeFormat;
        //$this->ds->exec("ALTER SESSION SET nls_timestamp_format='".$datetimeFormat."'");

        //$this->ds->exec("ALTER SESSION SET nls_timestamp_tz_format='".$datetimeFormat." TZR'");
    }

    /**
     * Set the encoding for texts
     * @param string $encoding The sql specific encoding
     */
    public function setEncoding($encoding="UTF8") 
    {
        //$this->ds->exec("ALTER SESSION SET nls_language='".$encoding."'");
    }

    
    /**
     * Get driver specific where options
     * @param object $query
     * 
     * @return string
     */
    protected function getWhereListOptions($query)
    {
        $whereOptionList = false;
        $whereOptionListItems = array();

        if ($baseOptionList = parent::getWhereListOptions($query)) {
            $whereOptionListItems[] = $baseOptionList;
        }

        if ($query->getLength()) {
            $whereOptionListItems[] = ' ROWNUM<=' . (int) $query->getLength();
        }

        if (count($whereOptionListItems) > 0) {
            $whereOptionList = \laabs\implode(' AND ', $whereOptionListItems);
        }

        return $whereOptionList;
    }

    /**
     * Parse the statement into Limit clause
     * @param object $query
     * 
     * @return string
     */
    protected function getLimitClause($query)
    {
        $limitClause = false;

        return $limitClause;
    }

    /**
     * Parse the statement into offset clause
     * @param object $query
     * 
     * @return string
     */
    protected function getOffsetClause($query)
    {
        $offsetClause = false;

        return $offsetClause;
    }

    protected function getBlobWriteExpression($property, $query)
    {
        $query->addReturn($property);
        $blobWriteExpression = 'EMPTY_BLOB()';

        return $blobWriteExpression;
    }

    protected function getBlobReadExpression($property)
    {
        $columnNameExpression = $this->getNameExpression($property->getName());
        $blobReadExpression = 'NVL(' . $columnNameExpression . ', EMPTY_BLOB()) ' . $columnNameExpression;

        return $blobReadExpression;
    }

}