<?php

namespace core\Exception;
/**
 * Exception for required authentication
 */
class UnauthorizedException
    extends \core\Exception
{
    /**
     * Constructor
     * @param string    $message
     * @param integer   $code
     * @param Exception $previous
     */
    public function __construct($message="Unauthorized", $code=401, $previous=null)
    {
        parent::__construct($message, $code, $previous);
    }
}