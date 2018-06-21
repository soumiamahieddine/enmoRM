<?php
namespace core\Language;

/**
 * class for Laabs Query Language parser
 *
 * @package Core
 * @author  Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class Parser
{
    
    protected $queryString;

    protected $lexer;
    
    public $tokens;

    protected $query;
    
    protected $bindings = array();

    private $tokenNames = array(
        LAABS_T_CREATE                => 'LAABS_T_CREATE',
        LAABS_T_READ                  => 'LAABS_T_READ',
        LAABS_T_UPDATE                => 'LAABS_T_UPDATE',
        LAABS_T_DELETE                => 'LAABS_T_DELETE',
        LAABS_T_COUNT                 => 'LAABS_T_COUNT',
        LAABS_T_SUMMARISE             => 'LAABS_T_SUMMARISE',
        
        LAABS_T_OPEN_PARENTHESIS      => 'LAABS_T_OPEN_PARENTHESIS',
        LAABS_T_CLOSE_PARENTHESIS     => 'LAABS_T_CLOSE_PARENTHESIS',
        LAABS_T_OPEN_BRACKET          => 'LAABS_T_OPEN_BRACKET',
        LAABS_T_CLOSE_BRACKET         => 'LAABS_T_CLOSE_BRACKET',
        LAABS_T_OPEN_BRACE            => 'LAABS_T_OPEN_BRACE',
        LAABS_T_CLOSE_BRACE           => 'LAABS_T_CLOSE_BRACE',
        
        LAABS_T_AND                   => 'LAABS_T_AND',
        LAABS_T_OR                    => 'LAABS_T_OR',
        LAABS_T_NOT                   => 'LAABS_T_NOT',
        LAABS_T_EQUAL                 => 'LAABS_T_EQUAL',
        LAABS_T_NOT_EQUAL             => 'LAABS_T_NOT_EQUAL',
        LAABS_T_GREATER               => 'LAABS_T_GREATER',
        LAABS_T_GREATER_OR_EQUAL      => 'LAABS_T_GREATER_OR_EQUAL',
        LAABS_T_SMALLER               => 'LAABS_T_SMALLER',
        LAABS_T_SMALLER_OR_EQUAL      => 'LAABS_T_SMALLER_OR_EQUAL',
        LAABS_T_CONTAINS              => 'LAABS_T_CONTAINS',
        LAABS_T_NOT_CONTAINS          => 'LAABS_T_NOT_CONTAINS',
        LAABS_T_IN                    => 'LAABS_T_IN',
        LAABS_T_NOT_IN                => 'LAABS_T_NOT_IN',
        
        LAABS_T_ASSOC_OPERATOR        => 'LAABS_T_ASSOC_OPERATOR',
        LAABS_T_RANGE_SEPARATOR       => 'LAABS_T_RANGE_SEPARATOR',
        LAABS_T_NS_SEPARATOR          => 'LAABS_T_NS_SEPARATOR',
        LAABS_T_NUMBER                => 'LAABS_T_NUMBER',
        LAABS_T_STRING                => 'LAABS_T_STRING',
        LAABS_T_ENCLOSED_STRING       => 'LAABS_T_ENCLOSED_STRING',
        LAABS_T_VAR                   => 'LAABS_T_VAR',
        LAABS_T_PROPERTY              => 'LAABS_T_PROPERTY',
        LAABS_T_NONE                  => 'LAABS_T_NONE',
        LAABS_T_TRUE                  => 'LAABS_T_TRUE',
        LAABS_T_FALSE                 => 'LAABS_T_FALSE',
        LAABS_T_BOOLEAN               => 'LAABS_T_BOOLEAN',
        LAABS_T_DATE                  => 'LAABS_T_DATE',
        //LAABS_T_IS_NULL               => 'LAABS_T_IS_NULL',
        LAABS_T_XPATH                 => 'LAABS_T_XPATH',
        LAABS_T_SQL                   => 'LAABS_T_SQL',
        LAABS_T_PHP                   => 'LAABS_T_PHP',
        LAABS_T_LQL                   => 'LAABS_T_LQL',
        
        LAABS_T_ASC                   => 'LAABS_T_ASC',
        LAABS_T_DESC                  => 'LAABS_T_DESC',
    
    );

    private $tokenTypes = array(
        'name' => array(
                LAABS_T_STRING,
                LAABS_T_ENCLOSED_STRING,
            ),
        'operation' => array(
                LAABS_T_CREATE,
                LAABS_T_READ,
                LAABS_T_UPDATE,
                LAABS_T_DELETE,
                LAABS_T_COUNT,
                LAABS_T_SUMMARISE,
            ),
        'operand' => array(
                LAABS_T_VAR,
                LAABS_T_STRING,
                LAABS_T_ENCLOSED_STRING,
                LAABS_T_NUMBER,
                LAABS_T_TRUE,
                LAABS_T_FALSE,
                LAABS_T_BOOLEAN,
                LAABS_T_FUNC,
                LAABS_T_LIST,
                LAABS_T_NS_SEPARATOR,
                LAABS_T_OPEN_PARENTHESIS,
                LAABS_T_SQL,
                LAABS_T_LQL,
                LAABS_T_XPATH,
                LAABS_T_DATE,
            ),
        'constant' => array(
                LAABS_T_ENCLOSED_STRING, 
                LAABS_T_NUMBER,
                LAABS_T_TRUE,
                LAABS_T_FALSE,
                LAABS_T_NULL,
                LAABS_T_BOOLEAN,
                LAABS_T_DATE,
            ),
        'listitem' => array(
                LAABS_T_ENCLOSED_STRING, 
                LAABS_T_STRING, 
                LAABS_T_FUNC, 
                LAABS_T_NUMBER,
            ),
        'comparisonOperator' => array(
                LAABS_T_EQUAL,
                LAABS_T_NOT_EQUAL,
                LAABS_T_GREATER,
                LAABS_T_GREATER_OR_EQUAL,
                LAABS_T_SMALLER,
                LAABS_T_SMALLER_OR_EQUAL,
                LAABS_T_CONTAINS,
                LAABS_T_NOT_CONTAINS,
            ),
        'logicalOperator' => array(
                LAABS_T_AND,
                LAABS_T_OR,
            ),
        );
    
    /**
     * Construct a new parser with a token list object
     * @param object $tokens
     */
    public function __construct($tokens=null) 
    {
        if ($tokens) {
            $this->tokens = $tokens;
        }
    }

    protected function tokenize($queryString)
    {
        $this->queryString = $queryString;
        $this->lexer = new Lexer();

        $this->tokens = $this->lexer->tokenize($queryString);
    }
    
    /**
     * Parse a LQL query string to Query object
     * @param string $queryString The LQL query string to parse
     *
     * @return \core\Language The Query object
     * @author 
     */
    public function parseQuery($queryString=false)
    {
        if ($queryString) {
            $this->tokenize($queryString);
        }
        
        // Operation CRUD on object OR  query ADD|REMOVE|MODIFY on component
        $this->expect($this->tokenTypes['operation']);

        $queryCode = $this->code();
        $this->query = new Query();
        $this->query->setCode($queryCode);

        switch ($queryCode) {
            case LAABS_T_CREATE:
                $this->parseCreateQuery();
                break;

            case LAABS_T_READ:
                $this->parseReadQuery();
                break;

            case LAABS_T_UPDATE:
                $this->parseUpdateQuery();
                break;

            case LAABS_T_DELETE:
                $this->parseDeleteQuery();
                break;

            case LAABS_T_COUNT:
                $this->parseCountQuery();
                break;

            case LAABS_T_SUMMARISE:
                $this->parseSummariseQuery();
                break;
        }

        // RETURN INTO
        if ($this->isNext(LAABS_T_RETURN)) {
            $this->next();
            $properties = $this->parsePropertyList();
            $this->query->setReturns($properties);
        }

        return $this->query;
    }

    /**
     * Get a class by its name or qualified name
     * @param string $class
     * @param object $query 
     * 
     * @return object The class definition object
     *
     * @throws Exception if no schema name given on class name (qualified) or no schema in use (by previous call of useSchema)
     */
    public function parseClass($class=false, $query=null)
    {
        if ($class) {
            $this->tokenize($class);
            $this->query = $query;
        }

        // Class name qualified by schema or not
        $this->expect($this->tokenTypes['name']);
        if ($this->isNext(LAABS_T_NS_SEPARATOR)) {
            $schemaName = $this->value();
            $schema = \laabs::bundle($schemaName);

            // Skip NS separator
            $this->next();

            // Move to class name
            $this->next();
            $this->expect($this->tokenTypes['name']);
            $className = $this->value();

        } elseif ($this->schema) {
            $className = $this->value();
            $schemaName = $this->schema;
            $schema = \laabs::bundle($schemaName);

        } else {
            
            throw new \Exception("Invalid qualified class name: no schema provided for class '$className'");
        
        }

        if (!$schema) {
            throw new \Exception("Schema '$schemaName' not found.");
        }

        $class = $schema->getClass($className);

        if (!$class) {
            throw new \Exception("Class '$schemaName/$className' not found.");
        }

        return $class;

    }

    protected function parseReadQuery()
    {
        
        if ($this->isNext(LAABS_T_UNIQUE)) {
            $this->next();
            $this->query->summarise(true);
        }

        // Parse class (schema/class)
        $this->next();
        $this->expect($this->tokenTypes['name']);
        $class = $this->parseClass();
        $this->query->setClass($class);

        // If key given , parse key
        if ($this->isNext(LAABS_T_NS_SEPARATOR)) {

            $keyValue = $this->parsePrimaryKeyValue();

            $primaryKey = $this->query->getClass()->getPrimaryKey();
            $keyAssert = $primaryKey->getAssert();
            $this->query->addAssert($keyAssert);

            $keyObject = $primaryKey->getObject($keyValue);

            $this->query->bindParam($primaryKey->getName(), $keyObject, $primaryKey);
        }

        // If square brackets parse property list
        if ($this->isNext(LAABS_T_OPEN_BRACKET)) {
            // Move to open bracket
            $this->next();

            $properties = $this->parsePropertyList();
            $this->query->setProperties($properties);

            // Skip close bracket
            $this->next();
        }

        // Assert
        if ($this->isNext(LAABS_T_OPEN_PARENTHESIS)) {
            // Move to open parenthesis
            $this->next();
            // Move to assert
            $this->next();

            $assert = $this->parseAssert();
            $this->query->addAssert($assert);

            // Skip close parenthesis
            $this->next();
        }

        // Sorting
        if ($this->isNext(LAABS_T_SORT)) {
            // Goto SORT
            $this->next();
            $this->next();

            // Goto first column
            $sortings = $this->parseSortingList();

            $this->query->setSortings($sortings);
        }

        // Offset and limit
        // i ==> limit
        // i..j ==> offset..limit
        if ($this->isNext(LAABS_T_OFFSET)) {
            $this->next(); // OFFSET
            $this->next();
            $this->expect(LAABS_T_NUMBER);
            $this->query->setOffset($this->value());
        }

        if ($this->isNext(LAABS_T_LIMIT)) {
            $this->next();
            $this->next();
            $this->expect(LAABS_T_NUMBER);
            $this->query->setLength($this->value());
        }

        // LOCK
        if ($this->isNext(LAABS_T_LOCK)) {
            $this->next();
            $this->query->lock(true);
        }
    }

    protected function parseCountQuery()
    {
        // Parse class (schema/class)
        $this->next();
        $this->expect($this->tokenTypes['name']);
        $class = $this->parseClass();
        $this->query->setClass($class);

        // Assert
        if ($this->isNext(LAABS_T_OPEN_PARENTHESIS)) {
            // Move to open parenthesis
            $this->next();
            // Move to assert
            $this->next();

            $assert = $this->parseAssert();
            $this->query->addAssert($assert);

            // Skip close parenthesis
            $this->next();
        }
    }

    protected function parseSummariseQuery()
    {
        // Parse class (schema/class)
        $this->next();
        $this->expect($this->tokenTypes['name']);
        $class = $this->parseClass();
        $this->query->setClass($class);

        // If criteria given , parse
        if ($this->isNext(LAABS_T_NS_SEPARATOR)) {
            $this->next();
            $this->next();
            $property = $this->parseProperty();
            $this->query->summarise($property);
        } else {
            $this->query->summarise(true);
        }

        // Move to open bracket
        $this->next();
        // If square brackets parse property list
        $this->expect(LAABS_T_OPEN_BRACKET);
        
        $properties = $this->parsePropertyList();
        $this->query->setProperties($properties);

        // Skip close bracket
        $this->next();
        
        // Assert
        if ($this->isNext(LAABS_T_OPEN_PARENTHESIS)) {
            // Move to open parenthesis
            $this->next();
            // Move to assert
            $this->next();

            $assert = $this->parseAssert();
            $this->query->addAssert($assert);

            // Skip close parenthesis
            $this->next();
        }
    }

    protected function parseCreateQuery() 
    {
        // Parse class (schema/class)
        $this->next();
        $this->expect($this->tokenTypes['name']);
        $class = $this->parseClass();
        $this->query->setClass($class);

        $this->next();
        $this->expect(LAABS_T_OPEN_BRACE);

        $object = $this->parseNewObject();
        $this->query->bindParam($class->getName(), $object, $class);
        foreach (get_object_vars($object) as $propertyName => $propertyValue) {
            $this->query->addProperty($class->getProperty($propertyName));
        }

    }

    protected function parseUpdateQuery() 
    {
        // Parse class (schema/class)
        $this->next();
        $this->expect($this->tokenTypes['name']);
        $class = $this->parseClass();
        $this->query->setClass($class);

        // Parse key value into key assert + bind object of given
        if ($this->isNext(LAABS_T_NS_SEPARATOR)) {

            $keyValue = $this->parsePrimaryKeyValue();

            $primaryKey = $this->query->getClass()->getPrimaryKey();
            $keyAssert = $primaryKey->getAssert();
            $this->query->addAssert($keyAssert);

            $keyObject = $primaryKey->getObject($keyValue);

            $this->query->bindParam($primaryKey->getName(), $keyObject, $primaryKey);
        }

        if ($this->isNext(LAABS_T_OPEN_BRACE)) {

            $object = $this->parseObject();
            $this->query->bindParam($class->getName(), $object, $class);
            foreach (get_object_vars($object) as $propertyName => $propertyValue) {
                $this->query->addProperty($class->getProperty($propertyName));
            }

            // Move to closing brace
            $this->next();

        }

         // Assert
        if ($this->isNext(LAABS_T_OPEN_PARENTHESIS)) {
            // Move to open parenthesis
            $this->next();
            // Move to assert
            $this->next();

            $assert = $this->parseAssert();
            $this->query->addAssert($assert);

            // Skip close parenthesis
            $this->next();
        }
    }

    protected function parseDeleteQuery() 
    {
        // Parse class (schema/class)
        $this->next();
        $this->expect($this->tokenTypes['name']);
        $class = $this->parseClass();
        $this->query->setClass($class);

        // Parse key value into key assert + bind object of given
        if ($this->isNext(LAABS_T_NS_SEPARATOR)) {

            $keyValue = $this->parsePrimaryKeyValue();

            $primaryKey = $this->query->getClass()->getPrimaryKey();
            $keyAssert = $primaryKey->getAssert();
            $this->query->addAssert($keyAssert);

            $keyObject = $primaryKey->getObject($keyValue);

            $this->query->bindParam($primaryKey->getName(), $keyObject, $primaryKey);
        }

        // Assert
        if ($this->isNext(LAABS_T_OPEN_PARENTHESIS)) {
            // Move to open parenthesis
            $this->next();
            // Move to assert
            $this->next();

            $assert = $this->parseAssert();
            $this->query->addAssert($assert);

            // Skip close parenthesis
            $this->next();
        }


    }
    
    protected function parsePrimaryKeyValue()
    {
        $keyValue = array();
        do { 
            // Skip NS separator
            $this->next();

            // Move to key value
            $this->next();
            $keyValue[] = $this->value();
        } while ($this->isNext(LAABS_T_NS_SEPARATOR));
        
        return $keyValue;
    }
    
    protected function parsePropertyList()
    {
        $propertyList = array();
        
        while (!$this->isNext(LAABS_T_CLOSE_BRACKET)) {
            $this->next();
            $property = $this->parseProperty();
            $propertyList[] = $property;
            
            if ($this->isNext(LAABS_T_LIST_SEPARATOR)) {
                $this->next();
                continue;
            } else {
                return $propertyList;
            }          

        }

        return $propertyList;
    }

    protected function parseNewObject()
    {
        $class = $this->query->getClass();

        $object = $class->newInstance();
        
        while (!$this->isNext(LAABS_T_CLOSE_BRACE)) {
            $this->next();
            $property = $this->parseProperty();
            $propertyName = $property->getName();
            
            $this->next();
            $this->expect(LAABS_T_ASSOC_OPERATOR);

            $this->next();
            $this->expect($this->tokenTypes['constant']);
            switch ($this->code()) {
                case LAABS_T_ENCLOSED_STRING:
                    $value = $this->value();
                    $value = substr($value, 1, strlen($value)-2);
                    $value = str_replace('\"', '"', $value);
                    break;
                    
                case LAABS_T_NUMBER:
                    $value = $this->value();
                    break;

                case LAABS_T_TRUE:
                    $value = true;
                    break;

                case LAABS_T_FALSE:
                    $value = false;
                    break;

                case LAABS_T_NULL:
                    $value = null;
                    break;
            }

            $object->$propertyName = $value;

            if ($this->isNext(LAABS_T_LIST_SEPARATOR)) {
                $this->next();
                continue;
            } else {
                break;
            }          

        }

        return $object;
    }

    protected function parseObject()
    {
        $class = $this->query->getClass();

        $object = new \stdClass();
        
        while (!$this->isNext(LAABS_T_CLOSE_BRACE)) {
            $this->next();
            $property = $this->parseProperty();
            $propertyName = $property->getName();
            
            $this->next();
            $this->expect(LAABS_T_ASSOC_OPERATOR);

            $this->next();
            $this->expect($this->tokenTypes['constant']);
            switch ($this->code()) {
                case LAABS_T_ENCLOSED_STRING:
                    $value = $this->value();
                    $value = substr($value, 1, strlen($value)-2);
                    $value = str_replace('\"', '"', $value);
                    break;
                    
                case LAABS_T_NUMBER:
                    $value = $this->value();
                    break;

                case LAABS_T_TRUE:
                    $value = true;
                    break;

                case LAABS_T_FALSE:
                    $value = false;
                    break;

                case LAABS_T_NULL:
                    $value = null;
                    break;
            }

            $object->$propertyName = $value;

            if ($this->isNext(LAABS_T_LIST_SEPARATOR)) {
                $this->next();
                continue;
            } else {
                break;
            }          

        }

        return $object;
    }

    /**
     * Parse a sorting assert
     * @param string $sortingString
     * @param object $query
     * 
     * @return array
     */
    public function parseSortingList($sortingString=false, $query=null)
    {
        if ($sortingString) {
            $this->tokenize($sortingString);
            $this->query = $query;
        }

        $sortingList = array();
        
        do {
            switch ($this->value()) {
                case "-":
                case ">":
                    $order = LAABS_T_DESC;
                    $this->next();
                    break;
                
                case "+":
                case '<':
                    $order = LAABS_T_ASC;
                    $this->next();
                    break;

                default:
                    $order = LAABS_T_ASC;
            }

            $property = $this->parseProperty();
            $sorting = new Sorting($property, $order);
            $sortingList[] = $sorting;
            
            if ($this->isNext(LAABS_T_LIST_SEPARATOR)) {
                $this->next();
                $this->next();
                continue;
            } else {
                return $sortingList;
            }          

        } while ($this->tokens->valid());

        return $sortingList;
    }
    
    /**
     * Get an query assert object from queryString 
     * @param string $assertString The LQL assert to parse or null if called from current parsing
     * @param object $query        The Query object or null if icurrently parsing a query
     * 
     * @return object The assert
     */
    public function parseAssert($assertString=false, $query=null)
    { 
        if ($assertString) {
            $this->tokenize($assertString);
            $this->query = $query;
        }

        // Operand for logical : Assert or comparison
        if ($this->code() == LAABS_T_OPEN_PARENTHESIS) {
            $this->next();
            $operand = $this->parseAssert();
            $this->next();
        } else {
            $operand = $this->parseComparisonOperation();
        }

        if ($this->isNext($this->tokenTypes['logicalOperator'])) {
            do {
                // Move to logical op
                $this->next();

                // Add first comparison as left for logical operation
                $operand = $this->parseLogicalOperation($operand);
            } while ($this->isNext($this->tokenTypes['logicalOperator']));
        }

        return new Assert($operand);
    }
    
    protected function parseLogicalOperation($left)
    {       
        $logicalOperator = $this->parseLogicalOperator();
        
        if ($this->isNext(LAABS_T_OPEN_PARENTHESIS)) {
            $this->next();
            $this->next();

            $right = $this->parseAssert();

            $this->next();
        } else {
            $this->next();
            $right = $this->parseComparisonOperation();
        }

        $logical = new LogicalOperation($logicalOperator, $left, $right);

        return $logical;
    }

    protected function parseLogicalOperator()
    {
        $logicalOperator = $this->code();

        return $logicalOperator;
    }
    
    protected function parseComparisonOperation()
    {
        $this->expect($this->tokenTypes['operand']);
        $left = $this->parseOperand();

        if ($this->isNext($this->tokenTypes['comparisonOperator'])) {
            $this->next();
            $comparisonOperator = $this->code();
            // Check if right operand contains wildcards
            if ($this->isNext(LAABS_T_ENCLOSED_STRING)) {
                $rightOperand = $this->getLookahead();
                if (strpos($rightOperand->getValue(), "*") !== false) {
                    switch ($comparisonOperator) {
                        case LAABS_T_EQUAL:
                            $comparisonOperator = LAABS_T_CONTAINS;
                            break;

                        case LAABS_T_NOT_EQUAL:
                            $comparisonOperator = LAABS_T_NOT_CONTAINS;
                            break;
                    }
                }                
            } elseif ($this->isNext(LAABS_T_OPEN_BRACKET)) {
                switch ($comparisonOperator) {
                    case LAABS_T_EQUAL:
                        $comparisonOperator = LAABS_T_IN;
                        break;

                    case LAABS_T_NOT_EQUAL:
                        $comparisonOperator = LAABS_T_NOT_IN;
                        break;
                }
            }
            
            // Operand
            $this->next();
            $right = $this->parseOperand();
            
            if ($this->getLookaheadCode() == LAABS_T_RANGE_SEPARATOR) {
                switch ($comparisonOperator) {
                    case LAABS_T_EQUAL:
                        $comparisonOperator = LAABS_T_BETWEEN;
                        break;

                    case LAABS_T_NOT_EQUAL:
                        $comparisonOperator = LAABS_T_NOT_BETWEEN;
                        break;
                }
                $right = $this->parseRange($right);
            }

        } else {
            $comparisonOperator = LAABS_T_EQUAL;
            $right = new BooleanOperand(true);
        }
        
        $comparison = new ComparisonOperation($comparisonOperator, $left, $right);
        
        return $comparison;
    }
    
    protected function parseOperand()
    {
        $code = $this->code();

        switch($code) {
            case LAABS_T_VAR:
                return new Param(substr($this->value(), 1));

            case LAABS_T_STRING:
                return $this->parseString();
                
            case LAABS_T_ENCLOSED_STRING:
                $value = $this->value();
                $value = substr($value, 1, strlen($value)-2);
                $value = str_replace('\"', '"', $value);

                return new StringOperand($value);
                
            case LAABS_T_NUMBER:
                return new NumberOperand($this->value());

            case LAABS_T_TRUE:
                return new BooleanOperand(true);

            case LAABS_T_FALSE:
                return new BooleanOperand(false);

            case LAABS_T_NULL:
                return new NullOperand(null);

            case LAABS_T_OPEN_BRACKET:
                $list = $this->parseList();
                
                // skip closing bracket 
                $this->next();
                $this->expect(LAABS_T_CLOSE_BRACKET);

                return $list;

            case LAABS_T_NS_SEPARATOR:
                $value = $this->parseMethod();
                switch (\laabs\gettype($value)) {
                    case 'string':
                        return new StringOperand($value);

                    case 'integer':
                    case 'int':
                    case 'float':
                    case 'double':
                    case 'real':
                        return new NumberOperand($value);

                    case 'boolean':
                    case 'bool':
                        return new BooleanOperand($value);

                    case 'array':
                        return new ListOperand($value);

                    case 'NULL':
                        return new NullOperand($value);

                    default:
                        return new StringOperand("__UNDEFINED__");
                }

            case LAABS_T_OPEN_PARENTHESIS:
                $assert = $this->parseAssert();

                $this->next();

                return $assert;

            case LAABS_T_SQL:
            case LAABS_T_XPATH:
            case LAABS_T_PHP:
                return new LanguageExpression($code, trim($this->value()));

            case LAABS_T_LQL:
                //$this->next();
                $assertUri =  $this->value();
                $bundleName = strtok($assertUri, LAABS_URI_SEPARATOR);
                $assertName = trim(strtok(LAABS_URI_SEPARATOR));
                $conf = \core\Configuration\Configuration::getInstance();

                if ($assertName) {
                    if (isset($conf[$bundleName]["@assert." . $assertName])) {
                        $assertString = $conf[$bundleName]["@assert." . $assertName];
                    }
                } else {
                    if (isset($conf["@assert." . $bundleName])) {
                        $assertString = $conf["@assert." . $bundleName];
                    }
                }
                if ($assertString) {
                    $parser = clone($this);
                    $assert = $parser->parseAssert($assertString, $this->query);

                    $this->next();

                    return $assert;
                }

        }

    }

    protected function parseString()
    {
        switch (true) {
            // "string(" == function
            case $this->isNext(LAABS_T_OPEN_PARENTHESIS) :
                return $this->parseFunction();
            // "string/" == path
            case $this->isNext(LAABS_T_NS_SEPARATOR) :
                return $this->parsePath();

            // else == property OR constant number value
            default:
                if ($property = $this->parseProperty()) {
                    return $property;
                }

                return new StringOperand($this->value());
        }
    }

    /*protected function parseDateNum()
    {
        $year = $this->value();

        $this->next();
        // Remove date separator
        $month = substr($this->value(), 1);

        $this->next();
        // Remove date separator
        $day = substr($this->value(), 1);

        $gDate = $year . '-' . $month . '-' . $day;

        if ($this->getLookahead()->getValue()[0] != "T") {
            return new DateOperand($gDate);
        }

        // Move to T separator + hour
        $this->next();

        // substr Txx to hour
        $hour = substr($this->value(), 1);

        // Move to hour separator ":"
        $this->next();

        // Move to minutes
        $this->next();
        $min = $this->value();

        // Move to hour separator ":"
        $this->next();

        // Move to second . dec
        $this->next();
        $sec = $this->value();

        $time = $hour . ":" . $min . ":" . $sec;

        if ($this->getLookahead()->getValue()[0] != ",") {
            return new TimestampOperand($gDate . " " . $time);
        }

        // Decimal separator is coma
        // Move to dec separator
        $this->next();

        // move to decimal value
        $this->next();
        $dec = $this->value();
        
        return new TimestampOperand($gDate . " " . $time . "." . $dec);
    }

    protected function parseDateString($value)
    {
        try {
            $dateTime = \laabs::newDateTime($value); 
        } catch (\Exception $e) {
            return;
        }

        return new TimestampOperand((string) $dateTime);
    }*/

    protected function parseRange($from)
    {
        $this->next();

        $this->next();
        $to = $this->parseOperand();

        return new RangeOperand($from, $to);
    }
    
    protected function parseFunction()
    {
        $funcname = $this->value();

        // Move to parenthesis
        $this->next();
        
        $funcargs = $this->parseList();

        // Skip parenthesis
        $this->next();
        
        return new Func($funcname, $funcargs);
    }

    protected function parsePath()
    {
        // first step == property
        $property = $this->parseProperty();

        $path = "";
        do {
            // Move to next sep
            $this->next();
            $this->expect(LAABS_T_NS_SEPARATOR);

            $path .= LAABS_URI_SEPARATOR;
            
            // Move to next step name
            $this->next();
            $this->expect(LAABS_T_STRING);
           
            $path .= $this->value();
        
        } while ($this->isNext(LAABS_T_NS_SEPARATOR));

        return new Path($property, $path);
    }

    protected function parseMethod()
    {
        $this->next();
        $this->expect(LAABS_T_STRING);

        $methodUri = $this->value();

        do {
            // Move to next step
            $this->next();
            $this->expect(LAABS_T_NS_SEPARATOR);

            $methodUri .= LAABS_URI_SEPARATOR;
            
            // Move to next step name
            $this->next();
            $this->expect(LAABS_T_STRING);
           
            $methodUri .= $this->value();
        
        } while ($this->isNext(LAABS_T_NS_SEPARATOR));

        // Parse args if parenthesis found
        $args = array();
        if ($this->isNext(LAABS_T_OPEN_PARENTHESIS)) {
            $args = array();
            $this->next();
            if (!$this->isNext(LAABS_T_CLOSE_PARENTHESIS)) {
                do {
                    // Move to next list item
                    $this->next();
                    $this->expect($this->tokenTypes['listitem']);

                    $value = $this->value();
                    $args[] = $value;
                
                } while (!$this->isNext(LAABS_T_CLOSE_PARENTHESIS));
            }

            $this->next();
        }

        $methodRouter = new \core\Route\MethodRouter($methodUri);

        $service = $methodRouter->service->call();

        $value = $methodRouter->method->callArgs($service, $args);

        return $value;

    }
    
    protected function parseProperty()
    {
        $this->expect($this->tokenTypes['name']);
        $propertyName = $this->value();
        if (isset($this->query)) {
            return $this->query->getClass()->getProperty($propertyName);
        } else {
            return $propertyName;
        }
    }
    

    protected function parseList()
    {
        $list = array();
        $valueList = array();
        
        switch(true) {
            // Simple list item, expect at least 2 values
            case $this->isNext($this->tokenTypes['listitem']): 
                
                $this->next();
                $this->expect($this->tokenTypes['listitem']);

                $value = $this->parseOperand();
                $valueList[] = $value;

                while (!$this->isNext(LAABS_T_CLOSE_BRACKET)) {
                    $this->next();
                    $this->expect(LAABS_T_LIST_SEPARATOR);
                    $this->next();
                    $value = $this->parseOperand();
                    $valueList[] = $value;
                };
                break; 

            // Method call, expect to receive a list
            case $this->isNext(LAABS_T_NS_SEPARATOR) :
                
                $this->next();

                $values = $this->parseMethod();

                foreach ((array) $values as $value) {
                    switch (gettype($value)) {
                        case 'integer':
                        case 'double':
                            $item = new NumberOperand($value);
                            break;

                        case 'boolean':
                            $item = new BooleanOperand($value);
                            break;

                        case 'NULL':
                            $item = new NullOperand($value);
                            break;

                        case 'string':
                        default:
                            $item = new StringOperand($value);
                    }
                    $valueList[] = $itme;
                }
                break;

            // Sub query
            case $this->isNext(LAABS_T_READ) :
                $this->next();
                $parser = new Parser($this->tokens);
                $value = $parser->parseQuery();

                return $value;
        }

        return new ListOperand($valueList);
    }

    /**************************************************************************
     * Token reader methods
     *************************************************************************/

    /**
     * Get current token code
     * @return mixed
     */
    protected function code() 
    {
        return $this->tokens->current()->getCode();
    }

    /**
     * Get current token value
     * @return mixed
     */
    protected function value() 
    {
        return $this->tokens->current()->getValue();
    }

    /**
     * Move to next token
     */
    protected function next() 
    {
        $this->tokens->next();
    }

    /**
     * Check if one of the given token codes exists at the current position
     * @param array $tokenCodes A list of token codes to check for
     *
     * @return boolean True if one of the given token codes is the current token, false otherwise
     */
    protected function expect($tokenCodes)
    {

        if (is_scalar($tokenCodes)) {
            $tokenCodes = array($tokenCodes);
        }
         
        if (!$this->tokens->valid()) {
            if (count($tokenCodes) == 1) {
                throw new \core\Exception("Unexpected end of string at offset ". strlen($this->queryString).". Expected " . $this->tokenName($tokenCodes[0]));
            } else {
                throw new \core\Exception("Unexpected end of string at offset ". strlen($this->queryString).". Expected one of " . implode(', ', $this->tokenNames($tokenCodes)));
            }
        }
        
        $tokenCode = $this->code();
        $tokenName = $this->tokenName($tokenCode);
        if (!in_array($tokenCode, $tokenCodes)) {
            $tokenOffset = $this->tokens->current()->getOffset();
            $trail = substr($this->queryString, $tokenOffset, 30);
            if (count($tokenCodes) == 1) {
                throw new \core\Exception("Unexpected token $tokenName at offset $tokenOffset near [$trail]. Expected " . $this->tokenName($tokenCodes[0]));
            } else {
                throw new \core\Exception("Unexpected token $tokenName at offset $tokenOffset near [$trail]. Expected one of " . implode(', ', $this->tokenNames($tokenCodes)));
            }
        }

    }
    
    /**
     * Move the internal array pointer to the next given token
     * @param mixed $tokenCode The code of the token to find
     *
     * @return boolean True if the token can be found, false otherwise
     */
    protected function moveTo($tokenCode)
    {
        while ($this->tokens->valid()) {
            if ($this->code() === $tokenCode) {
                return true;
            }
            $this->next();
        }

        return false;
    }
        
    
    /**
     * Get the lookahead token.
     * @param int $number The number of tokens to look ahead.
     *
     * @return Token The lookahead token or null if it not exists.
     */
    protected function getLookahead($number=1)
    {
        if (!$this->tokens->valid()) {
            return null;
        }
        $index = $this->tokens->key() + $number;
        
        if (isset($this->tokens[$index])) {
            $lookahead = $this->tokens[$index];
        } else {
            $lookahead = null;
        }
        
        return $lookahead;
    }
    
    /**
     * Get the code of the lookahead token
     * @param int $number The number of tokens to look ahead
     *
     * @return mixed The code of the lookahead token or null if it not exists
     */
    protected function getLookaheadCode($number = 1)
    {
        $lookahead = $this->getLookahead($number);
        
        if ($lookahead) {
            return $lookahead->getCode();
        }
    }
    
    
    /**
     * Check if the token is the next token in the token stack
     * @param mixed $tokenCodes The token code to check for or an array of codes
     * @param int   $number     The number of tokens to look ahead
     *
     * @return boolean
     */
    protected function isNext($tokenCodes, $number = 1)
    {
        if (is_scalar($tokenCodes)) {
            $tokenCodes = array($tokenCodes);
        }

        return in_array($this->getLookaheadCode($number), $tokenCodes);
    }
    
    
    protected function tokenNames($tokenCodes)
    {
        $tokenNames = array();
        foreach ($tokenCodes as $tokenCode) {
            $tokenNames[] = $this->tokenName($tokenCode);
        }
        
        return $tokenNames;
    }

    /**
     * Get the name of a token based on the value value
     * @param string $tokenValue
     *
     * @return mixed The name
     */
    public function tokenName($tokenValue)
    {
        if (isset($this->tokenNames[$tokenValue])) {
            return $this->tokenNames[$tokenValue];
        }
            
        return $tokenValue;
    }

    protected function getTrail($limit=false)
    {
        $offset = $this->tokens->current()->getOffset();
        $trail = substr($this->queryString, $offset);

        return $trail;
    }
    
}