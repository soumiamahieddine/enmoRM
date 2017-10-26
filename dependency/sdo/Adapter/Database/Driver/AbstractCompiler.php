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
 * undocumented class
 *
 * @package Sdo
 */ 
abstract class AbstractCompiler
{
    /* Properties */
    protected $query;

    protected $variables;
           
    /**
     * Compile Sdo Query into SQL query string
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    public function getQueryString(\core\Language\Query $query)
    {
        $this->variables = array();
        switch(strtolower($query->getCode())) {
            case strtolower(LAABS_T_CREATE):
                $queryString = $this->getInsertStatement($query);
                break;

            case strtolower(LAABS_T_READ):
                $queryString = $this->getSelectStatement($query);
                break;

            case strtolower(LAABS_T_UPDATE):
                $queryString = $this->getUpdateStatement($query);
                break;

            case strtolower(LAABS_T_DELETE):
                $queryString = $this->getDeleteStatement($query);
                break;

            case strtolower(LAABS_T_COUNT):
                $queryString = $this->getCountStatement($query);
                break;

            case strtolower(LAABS_T_SUMMARISE):
                $queryString = $this->getSummarizeStatement($query);
                break;
        }

        return $queryString;
    }
    
    /**
     * Retrieve the variables from query
     * 
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }
    
    /**
     * Compile Create statement
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    protected function getInsertStatement($query)
    {
        $insertStatement  = $this->getInsertIntoClause($query);
        $insertStatement .= ' ' . $this->getValuesClause($query);
        
        if ($returningClause = $this->getReturningClause($query)) {
            $insertStatement .= ' ' . $returningClause;
        }

        return $insertStatement;
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
        $selectStatement .= ' ' . $this->getFromClause($query);

        if ($whereClause = $this->getWhereClause($query)) {
            $selectStatement .= ' ' . $whereClause;
        }

        if ($orderByClause = $this->getOrderByClause($query)) {
            $selectStatement .= ' ' . $orderByClause;
        }

        if ($limitClause = $this->getLimitClause($query)) {
            $selectStatement .= ' ' . $limitClause;
        }

        if ($offsetClause = $this->getOffsetClause($query)) {
            $selectStatement .= ' ' . $offsetClause;
        }

        if ($selectQueryOptions = $this->getSelectQueryOptions($query)) {
            $selectStatement .= ' ' . $selectQueryOptions;
        }

        if ($forUpdateClause = $this->getForUpdateClause($query)) {
            $selectStatement .= ' ' . $forUpdateClause;
        }

        return $selectStatement;
    }
    
    /**
     *   Get update query string from Update Query
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    protected function getUpdateStatement($query)
    {
        $updateStatement  = $this->getUpdateClause($query);
        $updateStatement .= ' ' . $this->getSetClause($query);
        
        if ($whereClause = $this->getWhereClause($query)) {
            $updateStatement .= ' ' . $whereClause;
        }

        if ($returningClause = $this->getReturningClause($query)) {
            $updateStatement .= ' ' . $returningClause;
        }
            
        return $updateStatement;
    }
    
    /**
     *  Get delete query string from SDO Delete Query
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    protected function getDeleteStatement($query)
    {
        $deleteStatement  = $this->getDeleteClause($query);
        $deleteStatement .= ' ' . $this->getFromClause($query);

        if ($whereClause = $this->getWhereClause($query)) {
            $deleteStatement .= ' ' . $whereClause;
        }
            
        return $deleteStatement;
    }

    /**
     *  Get read query string from SDO count Query
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    protected function getCountStatement($query)
    {
        $selectStatement  = $this->getSelectClause($query);
        $selectStatement .= ' ' . $this->getFromClause($query);

        if ($whereClause = $this->getWhereClause($query)) {
            $selectStatement .= ' ' . $whereClause;
        }

        return $selectStatement;
    }

     /**
     *  Get read query string from SDO summarise Query
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    protected function getSummarizeStatement($query)
    {
        $selectStatement  = $this->getSelectClause($query);
        $selectStatement .= ' ' . $this->getFromClause($query);

        if ($whereClause = $this->getWhereClause($query)) {
            $selectStatement .= ' ' . $whereClause;
        }

        $selectStatement .= ' ' . $this->getGroupByClause($query);

        return $selectStatement;
    }
    
    /*************************************************************************
    #                                CLAUSES
    **************************************************************************/
    /* CREATE */
    /**
     *  Parse the Query into Insert into clause
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    protected function getInsertIntoClause($query)
    {
        $insertIntoClause  = 'INSERT INTO';
        $insertIntoClause .= ' ' . $this->getTableExpression($query->getClass());
        $insertIntoClause .= ' (' . $this->getInsertList($query) . ')';
        
        return $insertIntoClause;
    }
    
    /**
     *  Parse the Query into Value clause
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    protected function getValuesClause($query)
    {
        $valuesClause  = 'VALUES';
        $valuesClause  .= ' (' . $this->getValuesList($query) . ')';
        
        return $valuesClause;
    }
    
    /* READ */
    /**
     *  Parse the Query into select clause
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    protected function getSelectClause($query)
    {
        $selectClause = 'SELECT';

        if ($selectClauseOptions = $this->getSelectClauseOptions($query)) {
            $selectClause .= ' ' . $selectClauseOptions;
        }
        
        switch ($query->getCode()) {
            case LAABS_T_COUNT :
                $selectClause .= ' COUNT(1)';
                break;

            case LAABS_T_SUMMARISE :
                $selectClause .= ' ' . $this->getSelectList($query);
                $sum = $query->summarise();
                if (is_bool($sum)) {
                    $selectClause .= ', COUNT(1) AS ' . $this->getNameExpression('sum');
                } else {
                    $selectClause .= ', SUM('.$this->getColumnReadExpression($sum).') AS ' . $this->getNameExpression($sum->name);
                }
                break;

            default:
                $selectClause .= ' ' . $this->getSelectList($query);
        }

        return $selectClause;
    }
    
    /**
     * Parse the statement into From clause
     * @param \core\Language\Query $query The query object
     * 
     * @return string he SQL query string
     */
    protected function getFromClause($query)
    {
        $fromClause  = 'FROM';
        $fromClause .= ' ' . $this->getTableExpression($query->getClass());
        
        return $fromClause;
    }
    
    /**
     * Parse the statement into OrderBy clause
     * @param object $query
     * 
     * @return string
     */
    protected function getOrderByClause($query)
    {
        $orderByClause = false;
        if ($orderByList = $this->getOrderByList($query)) {
            $orderByClause = 'ORDER BY';
            $orderByClause .= ' ' . $orderByList;
        }

        return $orderByClause;
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
        if ($limit = $query->getLength()) {
            $limitClause = 'LIMIT';
            $limitClause .= ' ' . $limit;
        }

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
        if ($offset = $query->getOffset()) {
            $offsetClause = 'OFFSET';
            $offsetClause .= ' ' . $offset;
        }

        return $offsetClause;
    }

    /**
     * Parse the statement into group by clause
     * @param object $query
     * 
     * @return string
     */
    protected function getGroupByClause($query)
    {
        $groupByClause = false;
        if ($groupByList = $this->getGroupByList($query)) {
            $groupByClause = 'GROUP BY';
            $groupByClause .= ' ' . $groupByList;
        }

        return $groupByClause;
    }
    
    /* UPDATE */
    /**
     * Parse the statement into update clause     
     * @param object $query
     * 
     * @return string
     */
    protected function getUpdateClause($query)
    {
        $updateClause  = 'UPDATE';
        $updateClause .= ' ' . $this->getTableExpression($query->getClass());
        
        return $updateClause;
    }
    
    /**
     *  Parse the statement into Set clause
     * @param object $query
     * 
     * @return string
     */
    protected function getSetClause($query)
    {
        $setClause  = 'SET';
        $setClause .= ' ' . $this->getSetList($query);
        
        return $setClause;
    }
    
    /* DELETE */
    /**
     *  Parse the Query into Delete from clause
     * @param object $query
     * 
     * @return string
     */
    protected function getDeleteClause($query)
    {
        return 'DELETE';
    }
    
    /**
     *  Parse the statement into Where clause
     * @param object $query
     * 
     * @return string
     */
    protected function getWhereClause($query)
    {
        $whereClause = false;
        
        if ($whereList = $this->getWhereList($query)) {
            $whereClause  = 'WHERE';
            $whereClause .= ' ' . $whereList;
        }
        
        return $whereClause;
    }

    /**
     *  Parse the Query into Returning clause
     * @param object $query
     * 
     * @return string
     */
    protected function getReturningClause($query)
    {
        $returningClause = false;
        if ($returningList = $this->getReturningList($query)) {
            $returningClause = 'RETURNING ' . $returningList
                . ' INTO ' . $this->getIntoList($query);
        }

        return $returningClause;
    }

    /**
     *  Parse the Query into for update from clause
     * @param object $query
     * 
     * @return string
     */
    protected function getForUpdateClause($query)
    {
        $forUpdateClause = false;

        if ($query->lock()) {
            $forUpdateClause = 'FOR UPDATE';
        }

        return $forUpdateClause;
    }

    /*************************************************************************
    #                                LISTS
    *************************************************************************/
    /**
     *  Prepare the SQL statement table list
     * @param object $query
     * 
     * @return string table list expression
     */
    protected function getTableList($query)
    {
        return $this->getTableExpression($query->getClass());
        /*
        foreach ($this->expression->relations as $i => $Relation) {
            if ($i > 0 && !$Relation->join)
                $this->expression .= ', ';
            if ($Relation->join) {
                // $this->getJoinExpression($Relation->join);
            } else {
                $this->getTableExpression($Relation->Object);
            }
        }*/
    }

    /**
     * Parse the statement into Insert list
     * @param object $query
     * 
     * @return string
     */
    protected function getInsertList($query)
    {
        $columnList = false;
        $columnListItems = array();

        foreach ($query->getProperties() as $property) {
            $columnListItems[] = $this->getColumnNameExpression($property);
        }

        if (count($columnListItems) > 0) {
            $columnList = \laabs\implode(', ', $columnListItems);
        }

        return $columnList;
    }
    
    /**
     *  Prepare the SQL statement insert values list expression
     * @param object $query
     * 
     * @return string
     */
    protected function getValuesList($query)
    {
        $insertValuesList = false;

        if ($properties = $query->getProperties()) {
            $insertValuesItems = array();
            foreach ($properties as $property) {
                $insertValuesItems[] = $this->getColumnWriteExpression($property, $query);
            }
            $insertValuesList = \laabs\implode(', ', $insertValuesItems);
        }
            
        return $insertValuesList;
    }

    /**
     * Parse the statement into Select list
     * @param object $query
     * 
     * @return string
     */
    protected function getSelectList($query)
    {
        $selectList = false;
        $selectListItems = array();

        $properties = $query->getProperties();
        if (count($properties) == 0) {
            $properties = $query->getClass()->getProperties();
        }

        foreach ($properties as $property) {
            if (is_scalar($property)) {
                $selectListItems[] = $property;
            } elseif (
                $property->isPublic()
                && $property->isStringifyable()
                ) {
                    $selectListItems[] = $this->getColumnReadExpression($property);
            }
        }

        if (count($selectListItems) > 0) {
            $selectList = \laabs\implode(', ', $selectListItems);
        }

        return $selectList;
    }
    
    /**
     * Get Set list for update clause
     * @param object $query
     * 
     * @return string
     */
    protected function getSetList($query)
    {
        $setList = false;
        $setListItems = array();

        foreach ($query->getProperties() as $i => $property) {
            $setListItems[] = $this->getColumnNameExpression($property) . "=" . $this->getColumnWriteExpression($property, $query);
        }

        if (count($setListItems) > 0) {
            $setList = \laabs\implode(', ', $setListItems);
        }

        return $setList;
    }
    
    /**
     * Get Where list for select, update, delete clause
     * @param object $query The query object
     * 
     * @return string
     */
    protected function getWhereList($query)
    {
        $whereList = false;
        $whereListItems = array();
        
        if ($asserts = $query->getAsserts()) {
            foreach ($asserts as $assert) {
                $whereListItems[] = $this->getAssertExpression($assert);
            }
        }   

        if ($whereListOptions = $this->getWhereListOptions($query)) {
            $whereListItems[] = $whereListOptions;
        }

        if (count($whereListItems) > 0) {
            $whereList = \laabs\implode(' AND ', $whereListItems);
        }
        
        return $whereList;
    }

    /**
     * Get order by list
     * @param object $query
     * 
     * @return string
     */
    protected function getOrderByList($query)
    {
        $orderByList = false;
        
        if ($sortings = $query->getSortings()) {
            $orderByItems = array();
            foreach ($sortings as $sorting) {
                $orderByItems[] = $this->getOrderByExpression($sorting);
            }
            $orderByList = \laabs\implode(', ', $orderByItems);
        }

        return $orderByList;
    }

    /**
     * Get group by list
     * @param object $query
     * 
     * @return string
     */
    protected function getGroupByList($query)
    {
        $groupByList = false;
        
        foreach ($query->getProperties() as $property) {
            $groupByListItems[] = $this->getColumnNameExpression($property);
        }

        if (count($groupByListItems) > 0) {
            $groupByList = \laabs\implode(', ', $groupByListItems);
        }

        return $groupByList;
    }

    /**
     * Get returning list
     * @param object $query
     * 
     * @return string
     */
    protected function getReturningList($query)
    {
        $returningList = false;
        
        if ($returns = $query->getReturns()) {
            $returningItems = array();
            foreach ($returns as $return) {
                $returningItems[] = $this->getColumnReturnExpression($return);
            }
            $returningList = \laabs\implode(', ', $returningItems);
        }

        return $returningList;
    }

    /**
     * Get into
     * @param object $query
     * 
     * @return string
     */
    protected function getIntoList($query)
    {
        $intoList = false;
        if ($returns = $query->getReturns()) {
            $intoItems = array();
            foreach ($returns as $return) {
                $intoItems[] = $this->getParamExpression(
                    $query->getClass()->getName() . LAABS_URI_SEPARATOR 
                    . $return->getName()
                );
            }
            $intoList = \laabs\implode(', ', $intoItems);
        }

        return $intoList;
    }

    /**
     * Get driver specific select options
     * @param object $query
     * 
     * @return string
     */
    protected function getSelectClauseOptions($query)
    {
        $selectOptionList = false;
        $selectOptionListItems = array();

        if ($query->summarise()) {
            $selectOptionListItems[] = 'DISTINCT';
        }
        
        if (count($selectOptionListItems) > 0) {
            $selectOptionList = \laabs\implode(' ', $selectOptionListItems);
        }

        return $selectOptionList;
    }
    
    /**
     * Get driver specific where options
     * @param object $query
     * 
     * @return string
     */
    protected function getWhereListOptions($query)
    {
        $whereListOptions = false;
        $whereListOptionsItems = array();

        // Any where item non driver specific

        if (count($whereListOptionsItems) > 0) {
            $whereListOptions = \laabs\implode(' ', $whereListOptionsItems);
        }

        return $whereListOptions;
    }
    
    /*************************************************************************
    #                             EXPRESSIONS
    *************************************************************************/
    /**
     *  Parse an object into a table expression
     * @param object $class The class definition
     * 
     * @return string
     */
    protected function getTableExpression($class)
    {
        // Get possible laabs type redefined by class
        $baseName = $class->getBaseName();

        // get name binding
        $tableName = $this->getBinding($baseName);

        return $this->getNameExpression($tableName);
    }

    /**
     * Parse property into column name expression
     * @param object $property
     * 
     * @return string
     */
    protected function getColumnNameExpression($property)
    {
        switch(true) {
            case $property instanceof \core\Reflection\Property:
                /**$columnName = $this->getBinding(
                    $property->getSchema() 
                    . LAABS_URI_SEPARATOR . $property->getClass()
                    . LAABS_URI_SEPARATOR . $property->getName()
                );

                $columnName = substr($columnName, (strrpos($columnName, ".") + 1));*/

                return $this->getNameExpression($property->getBaseName());

            case is_scalar($property) :
                return $property;
        }
    }

    protected function getColumnWriteExpression($property, $query)
    {
        $paramName = $query->getClass()->getName() . LAABS_URI_SEPARATOR . $property->getName();
        $type = $property->getType();

        switch ($type) {
            case 'binary':
                $columnType = 'binary';
                $columnWriteExpression = $this->getBlobWriteExpression($property, $query);
                break;

            case 'date':
                $columnType = 'string';
                $columnWriteExpression = 'TO_DATE(' . $this->getParamExpression($paramName) . ", '". $this->dateFormat . "')";
                break;

            case 'timestamp':
                $columnType = 'string';
                $columnWriteExpression = 'TO_TIMESTAMP(' . $this->getParamExpression($paramName) . ", '". $this->datetimeFormat . "')";
                break;

            case 'boolean':
            case 'bool':
                switch ($this->boolFormat) {
                    case 1 : 
                        $columnType = 'float';
                        break;

                    case 2 : 
                    default :
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
                $columnWriteExpression = 'TO_NUMBER(' . $this->getParamExpression($paramName) . ", '". $this->numberFormat . "')";
                break;

            default:
                $columnType = $type;
                $columnWriteExpression = $this->getParamExpression($paramName);
        }
        
        //$bindName = "sdo_" . \laabs\md5($paramName, false, true);
        //$query->addParam($bindName, $columnType);

        return $columnWriteExpression;
    }

    /**
     * Get the write expression for a BLOB
     * @param object $property
     * @param object $query
     * 
     * @return string
     */
    protected function getBlobWriteExpression($property, $query)
    {
        $paramName = $query->getClass()->getName() . LAABS_URI_SEPARATOR . $property->getName();
        $blobWriteExpression = $this->getParamExpression($paramName);

        return $blobWriteExpression;
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
                $columnReadExpression = 'TO_CHAR(' . $columnNameExpression . ", '". $this->dateFormat . "') " . $columnNameExpression;
                break;

            case 'timestamp':
                $columnReadExpression = 'TO_CHAR(' . $columnNameExpression . ", '". $this->datetimeFormat . "') " . $columnNameExpression;
                break;

            case 'binary':
                $columnReadExpression = $this->getBlobReadExpression($property);
                break;
                
            default:
                $columnReadExpression = $this->getColumnNameExpression($property);
        }

        return $columnReadExpression;
    }

    /**
     * Get the column test expression
     * @param object $property
     * 
     * @return string
     */
    protected function getColumnTestExpression($property)
    {
        $columnTestExpression = $this->getNameExpression($property->getBaseName());
        switch ($type = $property->getType()) {
            /*case 'date':
                $columnTestExpression = 'TO_CHAR(' . $columnTestExpression . ", '". $this->dateFormat . "') ";
                break;

            case 'timestamp':
                $columnTestExpression = 'TO_CHAR(' . $columnTestExpression . ", '". $this->datetimeFormat . "') ";
                break;*/

            case 'xstring':
                $columnTestExpression = $columnTestExpression. "->>'value'";
                break;
                
            default:
                $columnTestExpression = $columnTestExpression;
        }

        return $columnTestExpression;
    }

    protected function getBlobReadExpression($property)
    {
        $blobReadExpression =  $this->getNameExpression($property->getName());

        return $blobReadExpression;
    }

    protected function getColumnReturnExpression($property)
    {
        $columnReturnExpression = $this->getNameExpression($property->getName());

        return $columnReturnExpression;
    }

    /**
     * Get where assert expressions
     * @param object $assert The assert
     * 
     * @return string
     */
    protected function getAssertExpression($assert)
    {
        switch(true) {
            case $assert instanceof \core\Language\ComparisonOperation:
                return $this->getComparisonExpression($assert);

            case $assert instanceof \core\Language\LogicalOperation:
                return $this->getLogicalExpression($assert);

            case $assert instanceof \core\Language\Func:
                return $this->getFuncExpression($assert);

            case $assert instanceof \core\Language\Assert:
                return "( " . $this->getAssertExpression($assert->operand) . " )";

            default:
                throw new \core\Exception("Unknown query assert type " . get_class($assert));
        }
    }
    
    
    protected function getComparisonExpression($comparison)
    {
        $left = $this->getOperandExpression($comparison->left);
        $cast = false;
        if ($comparison->left instanceof \core\Reflection\Property) {
            $cast = $comparison->left->getType();
        }
        $right = $this->getOperandExpression($comparison->right, $cast);

        switch($comparison->code) 
        {
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
                if ($cast == 'json') {
                    $left = 'CAST(' . $left . ' AS varchar)';
                }
                $left = "LOWER(" . $left . ")";
                $operator = " LIKE ";
                $right = str_replace("*", "%", "LOWER(" . $right . ")");
                break;

            case LAABS_T_NOT_CONTAINS:
                if ($cast == 'json') {
                    $left = 'CAST(' . $left . ' AS varchar)';
                }
                $left = "LOWER(" . $left . ")";
                $operator = " NOT LIKE ";
                $right = str_replace("*", "%", "LOWER(" . $right . ")");
                break;

            case LAABS_T_BETWEEN:
                $operator = ' BETWEEN ';
                break;

            case LAABS_T_NOT_BETWEEN:
                $operator = ' NOT BETWEEN ';
                break;

            case LAABS_T_IN:
                if ($right) {
                    $operator = " IN ";
                } else {
                    return "false";
                }
                break;

            case LAABS_T_NOT_IN:
                if ($right) {
                    $operator = " NOT IN ";
                } else {
                    return "false";
                }
                break;
            
            default:
                throw new \core\Exception("Unknown comparison operator code " . $comparison->code);
        }

        return $left . $operator . $right;
    }
    
    protected function getLogicalExpression($logical)
    {
        $left = $this->getOperandExpression($logical->left);
        $right = $this->getOperandExpression($logical->right);
        
        switch($logical->code) {
            case LAABS_T_AND:
                $operator = " AND ";
                break;
            case LAABS_T_OR:
                $operator = " OR ";
                break;
            default:
                throw new \core\Exception("Unknown logical operator code " . $logical->code);
        }
        
        return $left . $operator . $right   ;
    }
    
    protected function getOrderByExpression($sorting)
    {
        $property = $sorting->getProperty();
        
        switch($sorting->getOrder()) {
            case LAABS_T_DESC:
                return $this->getNameExpression($property->getBaseName()) . " DESC";

            default:
            case LAABS_T_ASC:
                return $this->getNameExpression($property->getBaseName()) . " ASC NULLS FIRST";
        }
    }

    protected function getFuncExpression($func)
    {
        switch($func->code) {
            //case LAABS_T_IS_NULL:
            //    return $this->getOperandExpression($func->parameters[0]) . " IS NULL";
        }
    }
    
    protected function getOperandExpression($operand, $cast=false)
    {
        switch(true) {
            case $operand instanceof \core\Reflection\Property:
                return $this->getColumnTestExpression($operand);
            
            case $operand instanceof \core\Language\StringOperand:
                $string = $this->getString($operand->value);
                switch ($cast) {
                    case 'datetime':
                    case 'timestamp':
                        return $this->getTimestampExpression($string);

                    case 'date':
                        return $this->getDateExpression($string);

                    default:
                        return $string;
                }
                
            case $operand instanceof \core\Language\NumberOperand:
                return $this->getNumberExpression($operand->value);

            case $operand instanceof \core\Language\BooleanOperand:
                return $this->getBoolExpression($operand->value);

            case $operand instanceof \core\Language\ListOperand:
                return $this->getListExpression($operand->value);

            /*case $operand instanceof \core\Language\DateOperand:
                return $this->getDateExpression($operand->value);

            case $operand instanceof \core\Language\TimestampOperand:
                return $this->getTimestampExpression($operand->value);*/

            case $operand instanceof \core\Language\RangeOperand:
                $fromExpression = $this->getOperandExpression($operand->from, $cast);
                $toExpression = $this->getOperandExpression($operand->to, $cast);

                return $fromExpression . ' AND ' . $toExpression;

            case $operand instanceof \core\Language\NullOperand:
                return 'NULL';

            case $operand instanceof \core\Language\Path:
                return $this->getPathExpression($operand);

            case $operand instanceof \core\Language\Param:
                $paramExpression = $this->getParamExpression($operand->getName());
                switch ($cast) {
                    case 'datetime':
                    case 'timestamp':
                        return $this->getTimestampExpression($paramExpression);

                    case 'date':
                        return $this->getDateExpression($paramExpression);

                    default:
                        return $paramExpression;
                }
            
            case $operand instanceof \core\Language\Func:
                return $this->getFuncExpression($operand);

            case $operand instanceof \core\Language\ComparisonOperation:
                return $this->getComparisonExpression($operand);

            case $operand instanceof \core\Language\LogicalOperation:
                return $this->getLogicalExpression($operand);

            case $operand instanceof \core\Language\Assert:
                return $this->getAssertExpression($operand);

            case $operand instanceof \core\Language\Query:
                $compiler = clone($this);   
                
                return "(" . $compiler->getQueryString($operand) .")";

            case $operand instanceof \core\Language\LanguageExpression:
                return $operand->value;

            default:
                throw new \core\Exception("Unknown operand type " . get_class($operand));
        }
    }
    
    protected function getParamExpression($paramName)
    {
        if (isset(static::$maxNameLength) && strlen($paramName) > static::$maxNameLength) {
            return ":sdo_" . \laabs\md5($paramName, false, true);
        } else {
            return ":" . preg_replace('/[^A-Za-z0-9_]/', '_', $paramName);
        }
    }
    
    protected function getNameExpression($name)
    {
        $parts = explode(".", $name);

        return '"' . implode('"."', $parts) . '"';
    }

    protected function getBoolExpression($bool)
    {
        switch($this->boolFormat) {
            case false:
            case null:
            case 0:
                if ($bool) {
                    return 1;
                } else {
                    return 0; 
                }

            case 1:
                if ($bool) {
                    return 'true';
                } else {
                    return 'false';
                }

            case 2:
                if ($bool) {
                    return 'Y';
                } else {
                    return 'N';
                }           
        }

    }

    protected function getPathExpression($operand)
    {
        $pathExpression = "";
        $pathExpression = $this->getNameExpression($operand->property->getBaseName());
        $steps = $operand->steps;

        switch ($operand->property->getType()) {
            case 'json':
            default:
                $targetName = "->>'" . array_pop($steps) . "'";
        
                if (count($steps)) {
                    $pathExpression .= "->'" . implode("->'", $steps) . "'";
                }

                $pathExpression .= $targetName;
                break;
        }
        
        return $pathExpression;        
    }

    protected function getDateExpression($date)
    {
        return "TO_DATE(" . $date . ", '". $this->dateFormat . "') ";
    }

    protected function getTimestampExpression($timestamp)
    {
        return "TO_TIMESTAMP(" . $timestamp . ", '". $this->datetimeFormat . "') ";
    }

    
    protected function stripAccents($string)
    {
        $string = strtr(utf8_decode($string), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
        $string = str_replace(' ', '', $string);
        
        return $string;
    }
    
    protected function getVariable($variable) 
    {
        $name = "_". base_convert(md5($variable->source . ":" . $variable->uri), 16, 36);
        if (!isset($this->variables[$name])) {
            $this->variables[$name] = $this->getValue($variable->getValue());
        }

        return ":" . $name;
    }
    
    protected function getValue($value) 
    {
        if (is_scalar($value)) {
            return $this->getScalar($value);
        } 
        if (is_array($value)) {
            return $this->getListExpression($value);
        }
    }
    
    protected function getListExpression($array) 
    {
        $list = array();
        foreach ($array as $value) {
            $compiledValue = $this->getOperandExpression($value);
            if (is_array($compiledValue)) {
                $list[] = \laabs\implode(", ", $compiledValue);
            } else {
                $list[] = $compiledValue;
            }
        }

        if (!empty($list)) {
            return "(" . \laabs\implode(", ", $list) . ")";
        }
    }

    protected function getNumberExpression($number) 
    {
        return $number;
    }

    protected function getFuncContains($parameters) 
    {
        $rOpd = $this->getOperandExpression($parameters[1]);
        if (strpos($rOpd, "'")===0) {
            $rOpd = mb_substr($rOpd, 1, -1);
        }

        return
            $this->getOperandExpression($parameters[0])
            . " LIKE " . $this->getString("%" . $rOpd . "%");
    }
    
    protected function getFuncStartsWith($parameters) 
    {
        $rOpd = $this->getOperandExpression($parameters[1]);
        if (strpos($rOpd, "'")===0) {
            $rOpd = mb_substr($rOpd, 1, -1);
        }

        return
            $this->getOperandExpression($parameters[0])
            . " LIKE " . $this->getString($rOpd . "%");
    }
    
    protected function getFuncIn($parameters) 
    {
        $lOpd = $this->getOperandExpression($parameters[0]);
        array_shift($parameters);

        return $lOpd . " IN (" . $this->getListExpression($parameters) . ")";
    }

    protected function getString($string)
    {
        return "'" . $string . "'";
    }
    
    protected function getNumeric($numeric) 
    {
        return $numeric;
    }


    protected function getSelectQueryOptions($query) 
    {
        $selectQueryOptions = false;

        return $selectQueryOptions;
    }
    
    protected function getConcat() 
    {
        return "CONCAT(" . \laabs\implode(", ", func_get_args()) . ")";
    }

}