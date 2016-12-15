<?php

namespace core\Exception;
/**
 * Exception for required authentication
 */
class ForbiddenException
    extends \core\Exception
{
    /**
     * Constructor
     * @param string    $message
     * @param integer   $code
     * @param Exception $previous
     */
    public function __construct($message="Forbidden", $code=403, $previous=null)
    {
        parent::__construct($message, $code, $previous);
    }
}