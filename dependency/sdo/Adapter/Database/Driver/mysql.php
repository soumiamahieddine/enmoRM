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
 * Driver for mysql / MariaDB
 * @package Dependency_sdo
 */
class mysql extends AbstractDriver
{
    /* Constants */

    /* Properties */
    public static $name = "mysql";

    /**
     * Set the date format
     * @param string $dateFormat
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
            $timeFormat = "HH:MM:SS";
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
            $datetimeFormat = "YYYY-MM-DD HH:MM:SS.ssssss";
        }

        $this->datetimeFormat = $datetimeFormat;

        //$this->ds->exec("SET datestyle TO ".$datetimeFormat);
    }

    /**
     * Prepare Read Query
     * @param \core\Language\Query $query The query object
     *
     * @return string he SQL query string
     */
    protected function getSelectStatement($query)
    {
        $selectStatement  = $this->getSelectClause($query);
        $selectStatement .= ' '.$this->getFromClause($query);

        if ($whereClause = $this->getWhereClause($query)) {
            $selectStatement .= ' '.$whereClause;
        }

        if ($orderByClause = $this->getOrderByClause($query)) {
            $selectStatement .= ' '.$orderByClause;
        }

        if ($limitClause = $this->getLimitClause($query)) {
            $selectStatement .= ' '.$limitClause;
        }

        if ($selectQueryOptions = $this->getSelectQueryOptions($query)) {
            $selectStatement .= ' '.$selectQueryOptions;
        }

        if ($forUpdateClause = $this->getForUpdateClause($query)) {
            $selectStatement .= ' '.$forUpdateClause;
        }

        return $selectStatement;
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

        if ($limit = $query->getLength() | $offset = $query->getOffset()) {
            $limitClause = 'LIMIT ';
            $limitClause .= $offset ? $offset."," : "";
            $limitClause .= $limit ? $limit : "18446744073709551615";
        }

        return $limitClause;
    }

    protected function getNameExpression($name)
    {
        return '`'.$name.'`';
    }

    protected function getDateExpression($date)
    {
        return "DATE_FORMAT(".$date.", '".$this->dateFormat."') ";
    }

    protected function getTimestampExpression($timestamp)
    {
        return "TIMESTAMP(".$timestamp.", '".$this->datetimeFormat."') ";
    }

    protected function getColumnWriteExpression($property, $query)
    {
        $paramName = $query->getClass()->getName().LAABS_URI_SEPARATOR.$property->getName();
        $type = $property->getType();

        switch ($type) {
            case 'binary':
                $columnType = 'binary';
                $columnWriteExpression = $this->getBlobWriteExpression($property, $query);
                break;

            case 'date':
                $columnType = 'string';
                $columnWriteExpression = 'DATE_FORMAT('.$this->getParamExpression($paramName).", '".$this->dateFormat."')";
                break;

            case 'timestamp':
                $columnType = 'string';
                $columnWriteExpression = 'TIMESTAMP('.$this->getParamExpression($paramName).", '".$this->datetimeFormat."')";
                break;

            case 'boolean':
            case 'bool':
                switch ($this->boolFormat) {
                    case 1:
                        $columnType = 'float';
                        break;

                    case 2:
                    default:
                        $columnType = 'string';
                        break;
                }
                $columnWriteExpression = $this->getParamExpression($paramName);
                break;

            case 'name':
            case 'qname':
            case 'id':
            case 'duration':
            case 'json':
                $columnType = 'string';
                $columnWriteExpression = $this->getParamExpression($paramName);
                break;

            case 'number':
                $columnType = 'string';
                $columnWriteExpression = 'TO_NUMBER('.$this->getParamExpression($paramName).", '".$this->numberFormat."')";
                break;

            default:
                $columnType = $type;
                $columnWriteExpression = $this->getParamExpression($paramName);
        }

        //$bindName = "sdo_".\laabs\md5($paramName, false, true);
        //$query->addParam($bindName, $columnType);

        return $columnWriteExpression;
    }

    /**
     * Get the column read expression
     * @param object $property
     *
     * @return string
     */
    protected function getColumnReadExpression($property)
    {
        $columnNameExpression = $this->getNameExpression($property->getName());
        switch ($type = $property->getType()) {
            case 'date':
                $columnReadExpression = 'DATE_FORMAT('.$columnNameExpression.", '".$this->dateFormat."') ".$columnNameExpression;
                break;

            case 'timestamp':
                $columnReadExpression = 'DATE_FORMAT('.$columnNameExpression.", '".$this->datetimeFormat."') ".$columnNameExpression;
                break;

            case 'binary':
                $columnReadExpression = $this->getBlobReadExpression($property);
                break;

            default:
                $columnReadExpression = $this->getColumnNameExpression($property);
        }

        return $columnReadExpression;
    }

    protected function getComparisonExpression($comparison)
    {
        $left = $this->getOperandExpression($comparison->left);
        $cast = false;
        if ($comparison->left instanceof \core\Reflection\Property) {
            $cast = $comparison->left->getType();
        }
        $right = $this->getOperandExpression($comparison->right, $cast);

        switch ($comparison->code) {
            case LAABS_T_EQUAL:
                $operator = "=";
                if ($comparison->right instanceof \core\Language\NullOperand) {
                    $operator = " IS ";
                }
                break;

            case LAABS_T_NOT_EQUAL:
                $operator = "!=";
                if ($comparison->right instanceof \core\Language\NullOperand) {
                    $operator = " IS NOT ";
                }
                break;
            case LAABS_T_GREATER:
                $operator = ">";
                break;
            case LAABS_T_GREATER_OR_EQUAL:
                $operator = ">=";
                break;
            case LAABS_T_SMALLER:
                $operator = "<";
                break;
            case LAABS_T_SMALLER_OR_EQUAL:
                $operator = "<=";
                break;
            /*case LAABS_T_SIMILAR:
                $operator = "=";
                $left = "SOUNDEX(" . $left . ")";
                $right = "SOUNDEX(" . utf8_encode($right) . ")";
                break;*/
            case LAABS_T_CONTAINS:
                /*if ($cast == 'json') {
                    $left = 'CAST(' . $left . ' AS varchar)';
                }*/
                $left = "LOWER(".$left.")";
                $operator = " LIKE ";
                $right = str_replace("*", "%", "LOWER(".$right.")");
                break;

            case LAABS_T_BETWEEN:
                $operator =  ' BETWEEN ';
                break;

            case LAABS_T_IN:
                if ($right) {
                    $operator = " IN ";
                } else {
                    return "false";
                }
                break;
            default:
                throw new \core\Exception("Unknown comparison operator code ".$comparison->code);
        }

        return $left.$operator.$right;
    }

    protected function getOrderByExpression($sorting)
    {
        $property = $sorting->getProperty();

        switch ($sorting->getOrder()) {
            case LAABS_T_DESC:
                return $this->getNameExpression($property->getBaseName())." DESC";

            default:
            case LAABS_T_ASC:
                return $this->getNameExpression($property->getBaseName())." IS NULL ASC";
        }
    }
}
