<?php

namespace core\Response;
/**
 * Http response 
 */
class HttpResponse
    extends AbstractResponse
{
    /* -------------------------------------------------------------------------
    - Constants
    ------------------------------------------------------------------------- */
    /* Status 1 - Information */
    /* Status 2 - Success */
    /* Status 3 - Redirection */
    /* Status 4 - Client Error */
    /* Status 5 - Server Error */

    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */

    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    public function __construct()
    {
        $this->mode = 'http';
        
        $this->code = 200;
    }

    /* Http Response Code */
    public function getHeader($name)
    {
        if (isset($this->headers[$name])) {
            return $this->headers[$name];
        }
    }

    public function setHeader($name, $value=null, $replace=false) 
    {
        if (is_null($value)) {
            if (isset($this->headers[$name])) {
                unset($this->headers[$name]);
            }

            return true;
        }

        if (!$replace && isset($this->headers[$name])) {
            return false;
        } else {
            $this->headers[$name] = $value;
        }

        return true;
    }

    public function setContentType($contentType)
    {
        $this->setHeader('Content-Type', $contentType, true);
    }

    public function getContentType()
    {
        return $this->getHeader('Content-Type');
    }

    public function setGzip($bool)
    {
        if ($bool) {
            $this->setHeader('Content-Encoding', 'gzip');
        } else {
            $this->setHeader('Content-Encoding');
        }
    }

    public function getGzip()
    {
        return $this->getHeader('Content-Encoding');
    }

    public function setCacheControl($control, $maxAge = null, $mustRevalidate = true)
    {
        $value = $control;
        if ($maxAge !== null) {
            $value .= ", max-age=$max-age";
        }
        if ($mustRevalidate) {
            $value .= ", must-revalidate";
        }

        $this->setHeader('Cache-Control', $value);
    }

    public function getCacheControl()
    {
        return $this->getHeader('Cache-Control');
    }

    /* Send */
    public function send()
    {
        http_response_code($this->code);

        if (!headers_sent()) {
            foreach ($this->headers as $field => $value) {
                header($field . ": " . $value);
            }
        }

        if (is_scalar($this->body)) {
            echo $this->body;
        } elseif (is_resource($this->body)) {
            $output = fopen('php://output', 'w+');
            stream_copy_to_stream($this->body, $output);
        }
    }
}
