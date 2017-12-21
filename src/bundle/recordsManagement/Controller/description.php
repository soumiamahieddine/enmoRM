<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\recordsManagement\Controller;

/**
 * Control of the recordsManagement archive description
 *
 * @package RecordsManagement
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org> 
 */
class description implements \bundle\recordsManagement\Controller\archiveDescriptionInterface
{
    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory
     *
     * @return void
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Create the description
     * @param obejct $archive  The described archive
     * @param string $fullText The archive fullText
     * 
     * @return bool
     */
    public function create($archive, $fullText=false)
    {
        $descriptionObject = \laabs::newInstance('recordsManagement/description');
        $descriptionObject->archiveId = $archive->archiveId;

        if (!empty($archive->archiveName)) {
            $descriptionObject->text = $archive->archiveName.' ';
        }
        if (!empty($archive->originatorArchiveId)) {
            $descriptionObject->text .= $archive->originatorArchiveId.' ';
        }
        if (!empty($archive->originatingDate)) {
            $descriptionObject->text .= $archive->originatingDate.' ';
        }

        $descriptionObject->text .= $this->getText($archive->descriptionObject);
        
        if ($fullText ) {
            $descriptionObject->text .= ' '.$fullText;
        }

        $descriptionObject->description = json_encode($archive->descriptionObject);
        
        $this->sdoFactory->update($descriptionObject);
    }

    protected function getText($data)
    {
        switch (\gettype($data)) {
            case 'string' :
            case 'integer' :
            case 'double' :
                if (strlen((string) $data) > 2) {
                    return (string) $data;
                }
                break;
                
            case 'object':
                if (method_exists($data, '__toString')) {
                    return (string) $data;
                } else {
                    $texts = [];
                    foreach ($data as $name => $value) {
                        $texts[] = $this->getText($value);
                    }

                    return implode(' ', $texts);
                }

            case 'array':
                $texts = [];
                foreach ($data as $name => $value) {
                    $texts[] = $this->getText($value);
                }

                return implode(' ', $texts);
        }
    }

    /**
     * Read the description
     * @param string $archiveId The archive identifier
     * 
     * @return object
     */
    public function read($archiveId)
    {
        try {
            $descriptionObject = $this->sdoFactory->read('recordsManagement/description', $archiveId);

            return json_decode($descriptionObject->description);
        } catch (\Exception $e) {
            
        }
    }

    /**
     * Search the description objects
     * @param string $description The search args on description object
     * @param string $text        The search args on text
     * @param array  $archiveArgs The search args on archive std properties
     * 
     * @return array
     */
    public function search($description=null, $text=null, array $archiveArgs=[])
    {
        $queryParams = [];
        $queryParts = ['description!=null and text!=null'];

        $queryParts[] = \laabs::newController('recordsManagement/archive')->getArchiveAssert($archiveArgs);
        
        // Json
        if (!empty($description)) {
            $parser = new \core\Language\parser();
            $assert = $parser->parseAssert($description);

            $queryParts[] = '<?SQL '.$this->getAssertExpression($assert).' ?>';
        }
        // Fulltext
        if (!empty($text)) {
            /*$lexer = new \core\Language\lexer();
            $tokens = $lexer->tokenize($text, false);

            foreach ($tokens as $token) {
                if (($token[0] == '"' && $token[strlen($token)-1] == '"')
                    || ($token[0] == "'" && $token[strlen($token)-1] == "'")) {
                    $token = substr($token, 1, -1);
                }
                
                $textAsserts[] = "text @@ to_tsquery('$token')";
            }

            $queryParts[] = '<?SQL '.implode(' and ', $textAsserts).' ?>';*/
            $text = preg_replace('/[^\w\-\_]+/', ' ', $text);
            $tokens = \laabs\explode(' ', $text);
            foreach ($tokens as $i => $token) {
                $tokens[$i] = $token.':*';
            }
            $queryParts[] = "<?SQL text @@ to_tsquery('".implode(' & ', $tokens)."') ?>";
        }

        $queryString = \laabs\implode(' and ', $queryParts);

        $archiveUnits = $this->sdoFactory->find('recordsManagement/archiveUnit', $queryString);

        foreach ($archiveUnits as $archiveUnit) {
            $archiveUnit->descriptionObject = json_decode($archiveUnit->description);
        }

        return $archiveUnits;
    }

    /**
     * Update the description
     * @param mixed  $description The description object
     * @param string $archiveId   The archive identifier
     * 
     * @return bool
     */
    public function update($archive)
    {
        if ($archive->fullTextIndexation == "indexed") {
            $archive->fullTextIndexation == "requested";
        }
        
        $this->create($archive);
    }

     /**
     * Delete the description object
     * @param id   $archiveId
     * @param bool $deleteDescription
     */
    public function delete($archiveId, $deleteDescription = true)
    {

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

            case $assert instanceof \core\Language\Assert:
                return "( " . $this->getAssertExpression($assert->operand) . " )";

            default:
                throw new \core\Exception("Unknown query assert type " . get_class($assert));
        }
    }

    protected function getComparisonExpression($comparison)
    {
        $left = "description->>'".$comparison->left."'";

        switch (true) {
            case $comparison->right instanceof \core\Language\NumberOperand :
            case $comparison->right instanceof \core\Language\RangeOperand 
                && ( $comparison->right->from instanceof \core\Language\NumberOperand 
                || $comparison->right->to instanceof \core\Language\NumberOperand ) :
                $left = '('. $left.')::numeric';
                break;
        }
        
        $right = $this->getOperandExpression($comparison->right);

        switch($comparison->code) 
        {
            case LAABS_T_EQUAL:
                $operator = "=";
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
            
            case LAABS_T_CONTAINS:
                $left = "LOWER(" . $left . ")";
                $operator = " LIKE ";
                $right = str_replace("*", "%", "LOWER(" . $right . ")");
                break;

            case LAABS_T_NOT_CONTAINS:
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
        
        return $left . $operator . $right;
    }

    protected function getOperandExpression($operand)
    {
        switch(true) {
            case is_scalar($operand):
                return $this->getString($operand);

            case $operand instanceof \core\Language\StringOperand:
                return $this->getString($operand->value);
                
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
                $fromExpression = $this->getOperandExpression($operand->from);
                $toExpression = $this->getOperandExpression($operand->to);

                return $fromExpression . ' AND ' . $toExpression;

            case $operand instanceof \core\Language\NullOperand:
                return 'NULL';
            
            case $operand instanceof \core\Language\Func:
                return $this->getFuncExpression($operand);

            case $operand instanceof \core\Language\ComparisonOperation:
                return $this->getComparisonExpression($operand);

            case $operand instanceof \core\Language\LogicalOperation:
                return $this->getLogicalExpression($operand);

            case $operand instanceof \core\Language\Assert:
                return $this->getAssertExpression($operand);

            default:
                throw new \core\Exception("Unknown operand type " . get_class($operand));
        }
    }

    protected function getBoolExpression($bool)
    {
        if ($bool) {
            return '1';
        } else {
            return '0';
        }
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