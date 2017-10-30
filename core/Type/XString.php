<?php
namespace core\Type;
/**
 * Class for eXtended string data
 */
class XString
    implements \JsonSerializable
{
    /**
     * The value
     * @var mixed
     */
    protected $value;

    /**
     * An associative array of attributes
     * @var array
     */
    protected $attributes;

    /**
     * Construct a new json data
     * @param mixed $value
     * @param array $attributes
     */
    public function __construct($value=false, $attributes=array())
    {
        $this->setValue($value);

        $this->attributes = $attributes;
           
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return array('value' => $this->value, 'attributes' => $this->attributes);
    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        $jsonString = json_encode(
            array('value' => $this->value, 'attributes' => $this->attributes), 
            \JSON_UNESCAPED_UNICODE + \JSON_UNESCAPED_SLASHES
        );

        if ($jsonString === false) {
            return false;
        }

        switch (json_last_error()) {
            case JSON_ERROR_NONE:
                return $jsonString;
            break;
            case JSON_ERROR_DEPTH:
                $message = 'The maximum stack depth has been exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                $message = 'Invalid or malformed JSON';
                break;
            case JSON_ERROR_CTRL_CHAR:
                $message = 'Control character error, possibly incorrectly encoded';
                break;
            case JSON_ERROR_SYNTAX:
                $message = 'Syntax error';
                break;
            case JSON_ERROR_UTF8:
                $message = 'Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            case JSON_ERROR_RECURSION:
                $message = 'One or more recursive references detected in the value to be encoded';
                break;
            case JSON_ERROR_INF_OR_NAN:
                $message = 'One or more NAN or INF values in the value to be encoded';
                break;
            case JSON_ERROR_UNSUPPORTED_TYPE:
                $message = 'A value of a type that cannot be encoded was given';
                break;
            default:
                $message = 'Unknown error';
        }

        trigger_error("Error encoding JSON: " . $message, E_USER_ERROR);
    }

    /**
     * Getter for properties
     * @param string $name The name
     * 
     * @return mixed
     */
    public function __get($name)
    {
        if ($name == 'value') {
            return $this->value;
        }

        if ($name == 'attributes') {
            return $this->attributes;
        }
    }

    /**
     * Get the value
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     * @param string $value The value to set
     */
    public function setValue($value)
    {
        if (!is_string($value)) {
            throw new \core\Exception("Invalid XString : Value is not a valid string");
        }
        
        $this->value = $value;
    }

    /**
     * Check the attribute
     * @param string $name The name of attribute
     * 
     * @return boolean
     */
    public function hasAttribute($name)
    {
        return (isset($this->attributes[$name]));
    }

    /**
     * Get the attribute
     * @param string $name The name of attribute
     * 
     * @return string
     */
    public function getAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
    }

    /**
     * Set the attribute
     * @param string $name  The name of attribute
     * @param string $value The value of attribute
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Remove the attribute
     * @param string $name The name of attribute
     */
    public function removeAttribute($name)
    {
        if (isset($this->attributes[$name])) {
            unset($this->attributes[$name]);
        }
    }

}