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
        if (is_scalar($this->body)) {
            echo $this->body. PHP_EOL;
        } elseif (is_resource($this->body)) {
            $output = fopen('php://output', 'w+');
            stream_copy_to_stream($this->body, $output);
            echo PHP_EOL;
        }
    }
}