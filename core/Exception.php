<?php
/**
 * Class file for Laabs core Exception
 * @package core
 */
namespace core;

/**
 * The Exception class
 * 
 * @package Core
 * @see     \Exception
 */
class Exception
    extends \Exception
    implements \JsonSerializable
{
    /* Propriétés */
    protected $variables = array();

    public $errors = array();
    
    /**
     * @access protected
     * @var string The format of message
     */
    protected $format;
    
    /* Méthodes */
    /**
     * Constructor for a new Exception
     * Sends an event type LAABS_EXCEPTION to observers
     * @param string $message   The message of Exception
     * @param int    $code      The code of Exception
     * @param object $previous  The previous Exception for backtrace
     * @param array  $variables The variables
     * 
     * @return void 
     */
    public function __construct ($message = "", $code = 0, \Exception $previous = null, $variables=array())
    {
        parent::__construct($message, $code, $previous);

        $this->format = $message;
        $this->variables = $variables;

        if ($this->variables) {
            $this->message = vsprintf($message, $this->variables);
        } else {
            $this->message = $message;
        }
        
        \core\Observer\Dispatcher::notify(LAABS_EXCEPTION, $this);
    }

    /**
     * Get the error message format
     * @return string
     */
    public function &getFormat()
    {
        return $this->format;
    }

    /**
     * Magic method for string conversion
     * @return string
     */
    public function __toString()
    {
        $string = parent::__toString();

        foreach (get_object_vars($this) as $name => $value) {
            if (!in_array($name, array_keys(get_class_vars('Exception')))) {
                $string .= PHP_EOL . $name . ": " . print_r($value, true);
            }
        }

        return $string;
    }

    /**
     * Magic method for string conversion
     * @return string
     */
    public function jsonSerialize()
    {
        $jsonSerializable = array(
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
        );

        foreach (get_object_vars($this) as $name => $value) {
            if (!in_array($name, array_keys(get_class_vars('Exception')))) {
                $jsonSerializable[$name] = $value;
            }
        }
        
        return $jsonSerializable;
    }

    /**
     * Set a message
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->format = $message;

        if ($this->variables) {
            $this->message = vsprintf($message, $this->variables);
        } else {
            $this->message = $message;
        }
    }

    /**
     * Get the error message format variables.
     * @return array
     */
    public function &getVariables()
    {
        return $this->variables;
    }

    /**
     * Set new variables
     * @param array $variables
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;

        $this->message = vsprintf($this->format, $this->variables);
    }
}
