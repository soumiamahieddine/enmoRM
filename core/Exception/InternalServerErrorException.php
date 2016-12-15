<?php

namespace core\Exception;
/**
 * Exception for unknown error
 */
class InternalServerErrorException
    extends \core\Exception
{
    /**
     * Constructor
     * @param string    $message
     * @param integer   $code
     * @param Exception $previous
     */
    public function __construct($message="Internal server error", $code=500, $previous=null)
    {
        parent::__construct($message, $code, $previous);
    }
}