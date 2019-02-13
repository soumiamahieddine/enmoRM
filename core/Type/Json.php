<?php
namespace core\Type;
/**
 * Class for json data
 */
class Json
    implements \JsonSerializable
{
    /**
     * The value
     * @var mixed
     */
    protected $value;

    /**
     * A bitmask of php json options
     * @var integer
     */
    protected $options;

    /**
     * Construct a new json data
     * @param mixed   $value
     * @param integer $options
     */
    public function __construct($value=false, $options=0)
    {
        $this->value = $value;

        $this->options = $options;       
    }

    /**
     * Load data as value
     * @param mixed $value
     */
    public function load($value)
    {
        $this->value = $value;
    }

    /**
     * Serialize into json representation
     * @return string
     */
    public function jsonSerialize()
    {
        return $this->value;
    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        $jsonString = json_encode($this->value, \JSON_UNESCAPED_UNICODE + \JSON_UNESCAPED_SLASHES);

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
     * Get the value
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Check value
     * @param string $name  The name of property
     */
    public function __isset($name)
    {
        return isset($this->value->$name);
    }

    /**
     * Set value
     * @param string $name  The name of property
     * @param mixed  $value The value to set
     */
    public function __set($name, $value)
    {
        $this->value->$name = $value;
    }

    /**
     * Get value
     * @param string $name The name of property
     * 
     * @return mixed The value
     */
    public function __get($name)
    {
        return $this->value->$name;
    }

    /**
     * Call local or storage method
     * @param string $method The name of method
     * @param array  $args   The method args
     * 
     * @return mixed The return of called method
     */
    public function __call($method, array $args=array())
    {
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $args);
        }

        return call_user_func_array(array($this->value, $method), $args);
    }
}