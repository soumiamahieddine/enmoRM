<?php

namespace core\Exception;
/**
 * Exception for resource not found
 */
class NotFoundException
    extends \core\Exception
{
    /**
     * Constructor
     * @param string    $message
     * @param integer   $code
     * @param Exception $previous
     */
    public function __construct($message="Not found", $code=404, $previous=null, $variables=array())
    {
        parent::__construct($message, $code, $previous, $variables);
    }
}