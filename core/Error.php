<?php
/**
 * Laabs Error class
 * @package core
 */
namespace core;

/**
 * Class Error
 * Represents a PHP or business error triggered by the script
 */
class Error
    implements \JsonSerializable
{

    /* Constants */

    /* Properties */
    /**
     * @access protected
     * @var string The message of error
     */
    protected $message;

    /**
     * @access protected
     * @var string The format of message
     */
    protected $format;
        
    /**
     * @access protected
     * @var array The message variables
     */
    protected $variables;
    
    /**
     * @access protected
     * @var mixed The code of error
     */
    protected $code;
    
    /**
     * @access protected
     * @var string The PHP script file name where error is triggered
     */
    protected $file;

    /**
     * @access protected
     * @var int The PHP script file line where error is triggered
     *
     */
    protected $line;

    /**
     * @access protected
     * @var mixed The context of data when error occured
     */
    protected $context;


    /* Methods */
    /**
     * Creates an Error object that represents a PHP error
     * @param string $message   The message of error, constant or formatted
     * @param array  $variables The variable arguments for formatted message
     * @param mixed  $code      The code of error
     * @param string $file      The PHP script file name where error is triggered
     * @param int    $line      The PHP script file line where error is triggered
     * @param mixed  $context   The context of data when error occured
     *
     * @return void
     * 
     * @access public
     *
     */
    public function __construct($message, $variables = [], $code = 0, $file = null, $line = null, $context = null)
    {
        $this->format = $message;
        $this->variables = $variables;
        $this->code    = $code;
        $this->file    = $file;
        $this->line    = $line;
        $this->context = $context;

        if ($this->variables) {
            $this->message = vsprintf($message, $this->variables);
        } else {
            $this->message = $message;
        }
    }

    /**
     * Returns a string representation
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }
    
    /**
     * Get the error message. 
     * @return string
     */
    public function &getMessage()
    {
        return $this->message;
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
     * Set the error message format
     * @param string $newMessage The new format string
     * @return string
     */
    public function setMessage($newMessage)
    {
        $this->format = $newMessage;

        if ($this->variables) {
            $this->message = vsprintf($newMessage, $this->variables);
        } else {
            $this->message = $newMessage;
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
    
    /**
     * Get the error code.
     * 
     * @return string The code
     */
    public function getCode()
    {
        return $this->code;
    }
    
    /**
     * Get the filename where the error occured.
     * 
     * @return string The file name
     */
    public function getFile()
    {
        return $this->file;
    }
    
    /**
     * Get the line number where the error occured.
     * 
     * @return int The line number
     */
    public function getLine()
    {
        return $this->line;
    }
    
    /**
     * Get the context of data when the error occured
     * 
     * @return array The array of variables available in the scope
     */
    public function &getContext()
    {
        return $this->context;
    }
   
    /**
     * Magic method for string conversion
     * @return string
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
