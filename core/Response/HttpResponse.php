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

    public function guessContentType()
    {
        $finfo = new \finfo();
        $type = $finfo->buffer($this->body, FILEINFO_MIME_TYPE);

        if (strtok($type, "/") == "text") {
            switch(strtolower($this->contentType)) {
                case 'css':
                case 'less':
                    $type = "text/css";
                    break;

                case 'js':
                    $type = "application/javascript";
                    break;

                case 'csv':
                    $type = "text/csv";
                    break;
            }
        }

        $encoding = $finfo->buffer($this->body, FILEINFO_MIME_ENCODING);
        $contentType = $type . "; charset=" . $encoding;
        $this->setContentType($contentType);
    }

    /**
     * Guess the request content type
     */
    public function guessResponseType()
    {
        $contentTypes = \laabs::getContentTypes();

        $contentType = $this->headers['Content-Type'];
        $mimeType = strtok($contentType, ";");
        if (isset($contentTypes[$mimeType])) {
            $this->type = $contentTypes[$mimeType];
        } else { 
            throw new \Exception("Could not find a request content handler for the request content type '$contentType'");
        }
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
        echo $this->body;
    }

}