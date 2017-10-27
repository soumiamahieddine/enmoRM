<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of Laabs.
 *
 * BLaabs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Laabs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace core\Language;

/**
 * PHP compiler
 */
class Compiler
{

    /* Properties */
    protected $class;

    public function setClass($class)
    {
        $this->class = $class;
    }

    public function getAssertExpression($assert)
    {
        switch(true) {
            case $assert instanceof \core\Language\ComparisonOperation:
                return $this->getComparisonExpression($assert);

            case $assert instanceof \core\Language\LogicalOperation:
                return $this->getLogicalExpression($assert);

            case $assert instanceof \core\Language\Func:
                return $this->getFuncExpression($assert);

            case $assert instanceof \core\Language\Assert:
                return $this->getAssertExpression($assert->operand);

            default:
                throw new \core\Exception("Unknown query assert type " . get_class($assert));
        }
    }

    protected function getComparisonExpression($comparison)
    {
        $left = $this->getOperandExpression($comparison->left);
        $right = $this->getOperandExpression($comparison->right);
        if ($comparison->left instanceof \core\Reflection\Property && $comparison->right->code != LAABS_T_NULL) {
            switch($comparison->left->getType()) {
                case 'date':
                    $right = $this->getDateExpression($right);
                    break;

                case 'timestamp':
                    $right = $this->getTimestampExpression($right);
                    break;
            }
        }
        switch($comparison->code) {
            case LAABS_T_EQUAL:
                $operator = "==";
                break;
            case LAABS_T_NOT_EQUAL:
                $operator = "!=";
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
                $left = "soundex(" . $left . ")";
                $right = "soundex(" . utf8_encode($right) . ")";
                break;*/
            
            case LAABS_T_CONTAINS:
                return "preg_match('/^" . str_replace("\*", ".*", preg_quote($comparison->right->getValue())) . "$/', " . $left . ")";
                break;

            case LAABS_T_IN:
                if ($right) {
                    return "in_array(" . $left . ", " . $right . ")";
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
                $operator = " && ";
                break;
            case LAABS_T_OR:
                $operator = " || ";
                break;
            default:
                throw new \core\Exception("Unknown logical operator code " . $logical->code);
        }
        
        return $left . $operator . $right   ;
    }

    protected function getOperandExpression($operand)
    {        
        switch(true) {
            case $operand instanceof \core\Reflection\Property:
                return $this->getNameExpression(\laabs\basename($operand->getClass()) . LAABS_URI_SEPARATOR . $operand->getBaseName());
            
            case $operand instanceof \core\Language\StringOperand:
                return $this->getStringExpression($operand);               

            case $operand instanceof \core\Language\Param:
                return $this->getParamExpression($operand->getName());
            
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

            default:
                throw new \core\Exception("Unknown operand type " . get_class($operand));
        }
    }

    protected function getNameExpression($name)
    {
        $parts = explode(LAABS_URI_SEPARATOR, $name);

        return '$' . implode('->', $parts);
    }
    
    protected function getConstantExpression($operand)
    {
        switch($operand->code) {
            case LAABS_T_ENCLOSED_STRING:
            case LAABS_T_STRING:
                return $this->getString($operand->value);
                
            case LAABS_T_NUMBER:
                return $operand->value;
            
            case LAABS_T_BOOLEAN:
                return $this->getBoolExpression($operand->getValue()); 

            case  LAABS_T_NULL:
                return 'NULL';

            case LAABS_T_SQL:
                return $operand->getValue(); 

            case LAABS_T_LIST:
                return $this->getListExpression($operand);

            default:
                throw new \core\Exception("Unknown operand code " . $operand->code);
        }
    }

     protected function getBoolExpression($bool)
    {
        if ($bool) {
            return 'true';
        } else {
            return 'false';
        }
    }

    protected function getDateExpression($date)
    {
        return '\laabs::newDate(' . $date . ')';
    }

    protected function getTimestampExpression($timestamp)
    {
        return '\laabs::newTimestamp(' . $timestamp . ')';
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
    
    protected function getListExpression($operand) 
    {
        $list = array();
        foreach ($operand->value as $value) {
            $compiledValue = $this->getOperandExpression($value);
            if (is_array($compiledValue)) {
                $list[] = \laabs\implode(", ", $compiledValue);
            } else {
                $list[] = $compiledValue;
            }
        }

        if (!empty($list)) {
            return "array(" . \laabs\implode(", ", $list) . ")";
        }
    }

    protected function getString($string)
    {
        return "'" . $string . "'";
    }
    
    protected function getNumeric($numeric) 
    {
        return $numeric;
    }
}