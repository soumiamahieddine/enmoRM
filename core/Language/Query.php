<?php
namespace core\Language;
/**
 * class for create, read, update end delete queries on model
**/
class Query
{
    /* Constants */

    /* Properties */
    /**
     * The operation code
     * @var string
     */
    protected $code;

    /**
     * The object class
     * @var string
     */
    protected $class;

    /**
     * The property set
     * @var array
     */
    protected $properties = [];

    /**
     * The collection asserts
     * @var array
     */
    protected $asserts;

    /**
     * The sorting rules
     * @var array
     */
    protected $sortings;

    /**
     * The resultset start offset
     * @var array
     */
    protected $offset;

    /**
     * The resultset length
     * @var array
     */
    protected $length;
    
    protected $summarise = false;

    protected $lock = false;

    protected $returns;
    
    protected $params = array();

    /* Methods */
    /**
     * Construct a new query from LQL
     * @param string $queryString A Laabs Query Language query string to parse
     * 
     * @return a new instance of query object
     */ 
    public static function parse($queryString)
    {
        $parser = new Parser();
        $query = $parser->parseQuery($queryString);

        return $query;
    }

    /**
     * 
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * 
     */
    public function getCode()
    {
        return $this->code;
    }


    /* Class */
    /**
     * 
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * 
     */
    public function getClass()
    {
        return $this->class;
    }

    /* Properties */
    /**
     * 
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }

    /**
     * 
     */
    public function addProperty($property)
    {
        $this->properties[] = $property;
    }

    /**
     * 
     */
    public function getProperties()
    {
        return $this->properties;
    }
    
    /**
     * 
     */
    public function setAsserts(array $asserts)
    {
        $this->asserts = $asserts;
    }
    /**
     * 
     */
    public function addAssert($assert)
    {
        $this->asserts[] = $assert;
    }

    /**
     * 
     */
    public function getAsserts()
    {
        return $this->asserts;
    }
    
    /**
     * 
     */
    public function summarise($do=null)
    {
        if (is_null($do)) {
            return $this->summarise;
        }
        
        $this->summarise = $do;
    }

    /**
     * 
     */
    public function lock($do=null)
    {
        if (is_null($do)) {
            return $this->lock;
        }
        
        $this->lock = $do;
    }

    /* Sortings */
    /**
     * 
     */
    public function addSorting(Sorting $sorting)
    {
        $this->sortings[] = $sorting;
    }

    /**
     * 
     */
    public function setSortings(array $sortings)
    {
        $this->sortings = $sortings;
    }


    /**
     * 
     */
    public function getSortings()
    {
        return $this->sortings;
    }

    /**
     * 
     */
    public function setLength($length)
    {
        $this->length = (int) $length;
    }

    /**
     * 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * 
     */
    public function setOffset($offset)
    {
        $this->offset = (int) $offset;
    }

    /**
     * 
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * 
     */
    public function setReturns(array $returns)
    {
        $this->returns = $returns;
    }
    /**
     * 
     */
    public function addReturn($return)
    {
        $this->returns[] = $return;
    }

    /**
     * 
     */
    public function getReturns()
    {
        return $this->returns;
    }

    /**
     * 
     */
    public function addParam($name, $type='string', $length=null)
    {
        $value = null;
        $this->params[$name] = new Param($name, $value, $type, $length);
    }


    /**
     * 
     */
    public function bindParam($name, &$value=null, $type=null)
    {
        $this->params[$name] = new Param($name, $value, $type);
    }

    /**
     * 
     */
    public function getParams()
    {
        return $this->params;
    }

    

}