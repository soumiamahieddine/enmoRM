<?php
/**
 * Class file for Model property
 * @package core
 */
namespace core\Reflection;
/**
 * Class that defines a service property
 * 
 * @uses \core\ReadonlyTrait
 */
class Property
    extends abstractProperty
{
    /* Properties */
    /**
     * The class declaring the property
     * @var string
     */
    public $model;

    /**
     * The type of the property
     * @var string
     */
    public $type;

    /** 
     * Property is simple or complex
     * @var boolean
     */
    public $stringifyable;

    /**
     * The substitution property name
     * @var string
     */
    public $substitution;

    /**
     * Declare property as part of the index, with order
     * @var string
     * @enumeration [asc, desc]
     */
    public $index;

    /**
     * The dafault value
     * @var mixed
     */
    public $default;

    /** 
     * Is nullable
     * @var bool
     */
    public $nullable = true;

    /** 
     * Is emptyable (string '' / int 0)
     * @var bool
     */
    public $emptyable = true;

    /** 
     * Character length
     * @var int
     */
    public $length;

    /** 
     * Min string length
     * @var int
     */
    public $minLength;

    /** 
     * Max string length
     * @var int
     */
    public $maxLength;

    /** 
     * pattern
     * @var string
     */
    public $pattern;

    /** 
     * Enumeration of possible values
     * @var array
     */
    public $enumeration;

    /** 
     * Numeric precision
     * @var int
     */
    public $precision;

    /** 
     * Numeric scale
     * @var int
     */
    public $scale;

    /** 
     * Min numeric value
     * @var int
     */
    public $minValue;

    /** 
     * Max numeric value
     * @var int
     */
    public $maxValue;

    /* Methods */
    /**
     * Constructor of the model property
     * @param string $name  The name of the property
     * @param string $class The class of the model  that declares the method
     * @param object $type  The declaring type
     */
    public function __construct($name, $class, $type)
    {
        $this->model = $type->getName();
        parent::__construct($class, $name);

        $defaults = $type->getDefaultProperties();

        if (array_key_exists($this->name, $defaults)) {
            $this->default = $defaults[$this->name];
        }

        $docComment = $this->getDocComment();

        foreach ((array) $this->tags as $tagname => $tagvalues) {
            switch($tagname) {
                case 'var':
                    $type = trim(strtok($tagvalues[0], ' '));
                    if (!empty($desc = trim(strtok('')))) {
                        if (empty($this->summary)) {
                            $this->summary = $desc;
                        } elseif (empty($this->description)) {
                            $this->description = $desc;
                        }
                    }
                    switch($type) {
                        case 'bool':
                            $type = 'boolean';
                            break;

                        case 'real':
                        case 'double':
                            $type = 'float';
                            break;

                        case 'int':
                            $type = 'integer';
                            break;
                    }
                    
                    $this->type = $type;

                    switch (true) {
                        case strpos($this->type, LAABS_URI_SEPARATOR) :
                        case substr($this->type, -2) == "[]" :
                        case $this->type == 'array':
                        case $this->type == 'object':
                        case $this->type == 'resource':
                            $this->stringifyable = false;
                            break;
                            
                        default:
                            $this->stringifyable = true;
                    }
                    break;

                case 'substitution':
                    $this->substitution = trim($tagvalues[0]);
                    break;

                case 'notnull':
                    $this->nullable = false;
                    break;

                case 'notempty':
                    $this->emptyable = false;
                    $this->nullable = false;
                    break;

                case 'length':
                    $this->length = (int) $tagvalues[0];
                    break;

                case 'minlength':
                    $this->minLength = (int) $tagvalues[0];
                    break;

                case 'maxlength':
                    $this->maxLength = (int) $tagvalues[0];
                    break;

                case 'pattern':
                    $this->pattern = trim($tagvalues[0]);
                    break;

                case 'enumeration':
                    try {
                        @eval('$enumeration = '.trim($tagvalues[0]).';');
                    } catch (\Error $e) {
                        $enumeration = \laabs\explode(',', substr(trim($tagvalues[0]), 1, -1));
                    }
                    $this->enumeration = $enumeration;
                    break;

                case 'precision':
                    $this->precision = (int) $tagvalues[0];
                    break;

                case 'scale':
                    $this->scale = (int) $tagvalues[0];
                    break;

                case 'minvalue':
                    $this->minValue = (int) $tagvalues[0];
                    break;

                case 'maxvalue':
                    $this->maxValue = (int) $tagvalues[0];
                    break;

                case 'index':
                    $this->index = (string) trim($tagvalues[0]);
                    break;
            }
        } 
            
    }

    /**
     *  Retrieves the declaring class name
     * @return string 
     */
    public function getClass() 
    {
        return $this->model;
    }

    /**
     *  Retrieves the declaring bundle name
     * @return string 
     */
    public function getSchema() 
    {
        return \laabs\basename($this->model);
    }

    /** 
     * Get the base property name
     * @return string
     */
    public function getBaseName()
    {
        if ($this->substitution) {
            $type = \laabs::getClass($this->model);
            $type = $type->getBaseType();
            
            $baseProperty = $type->getProperty($this->substitution);

            return $baseProperty->getBaseName();
        }

        return $this->name;
    }

    /** 
     * Get the base property
     * @return Property
     */
    public function getBaseProperty()
    {
        if ($this->substitution) {
            $type = \laabs::getClass($this->model);
            $type = $type->getBaseType();
            
            $baseProperty = $type->getProperty($this->substitution);

            return $baseProperty->getBaseProperty();
        }

        return $this;
    }

    /**
     * Checks if a property can be null
     *  @return bool 
     */
    public function isNullable() 
    {
        return $this->nullable;
    }

    /**
     * Checks if a property can be empty
     *  @return bool 
     */
    public function isEmptyable() 
    {
        return $this->emptyable;
    }

    /**
     * Check if the property has a default value
     * 
     * @return bool
     */
    public function hasDefault()
    {
        return isset($this->default);
    }

    /**
     * Get the default value
     * 
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * Get the exact length
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Get the max length
     * @return int
     */
    public function getMaxLength()
    {
        return $this->maxLength;
    }

    /**
     * Get the min length
     * @return int
     */
    public function getMinLength()
    {
        return $this->minLength;
    }

    /**
     * Get the pattern
     *
     * @return string
     */     
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Get the enumeration
     *
     * @return array
     */     
    public function getEnumeration()
    {
        return $this->enumeration;
    }

    /**
     * Get the numeric precision of a numeric column
     *
     * @return int
     */     
    public function getPrecision()
    {
        return $this->precision;
    }
    
    /**
     * Get the numeric scale of a numeric column
     *
     * @return int
     */     
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * Get the numeric min value
     *
     * @return float
     */     
    public function getMinValue()
    {
        if ($this->minValue) {
            return $this->minValue;
        }

        if ($this->type == 'int' || $this->type == 'integer') {
            return 0;
        }
        
        if ($this->precision) {
            $minValue = "-" . str_repeat('9', $this->precision - $this->scale);
            if ($scale > 0) {
                $minValue .= '.'.str_repeat('9', $this->scale);
            }

            return $minValue;
        }
    }

    /**
     * Get the numeric max value
     *
     * @return float
     */     
    public function getMaxValue()
    {
        if ($this->maxValue) {
            return $this->maxValue;
        }

        if ($this->type == 'int' || $this->type == 'integer') {
            return PHP_INT_MAX;
        }

        if ($this->precision) {
            $maxValue = str_repeat('9', $this->precision - $this->scale);
            if ($scale > 0) {
                $maxValue .= '.'.str_repeat('9', $this->scale);
            }

            return $maxValue;
        }
    }

    /**
     * Get the type of the parameter
     * @return string A class name, 'array', an array of classes class[] or null
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Checks if the property is scalar/resource/null
     * @return bool
     */
    public function isScalar()
    {
        return $this->stringifyable;
    }

    /**
     * Checks if the property is array
     * @return bool
     */
    public function isArray()
    {
        switch (true) {
            case substr($this->type, -2) == "[]" :
            case $this->type == 'array':
                return true;

            default:
                return false;
        }
    }

    /**
     * Check if property can be stringified
     * @return bool
     */
    public function isStringifyable()
    {
        return $this->stringifyable;
        
        /*if (isset($this->type)) {
            try {
                $class = \laabs::getClass($this->type);
                if ($class && $class->isStringifyable()) {
                    return true;
                }
            } catch (\Exception $e) {
                
            }
        }*/

        return false;
    }

}
