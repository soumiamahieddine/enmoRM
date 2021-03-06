<?php

namespace core\Exception;
/**
 * Exception for bad requests
 * Invalid message
 * Forbidden content
 */
class BadRequestException
    extends \core\Exception
{

    /**
     * Constructor
     * @param string    $message
     * @param integer   $code
     * @param Exception $previous
     * @param array     $variables
     */
    public function __construct($message="Bad request", $code=400, $previous=null, $variables=array())
    {
        parent::__construct($message, $code, $previous, $variables);
    }
}