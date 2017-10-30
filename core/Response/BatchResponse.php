<?php

namespace core\Response;

class BatchResponse
    extends AbstractResponse
{
    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */

    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    public function __construct()
    {
        $this->mode = 'cli';

    }

    public function setBody($body)
    {
        $this->body .= print_r($body, true) . PHP_EOL;
    }

    public function send()
    {
        echo $this->body;
    }

}