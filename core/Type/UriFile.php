<?php

namespace core\Type;

class UriFile extends abstractFile
{
    protected $uri;

    public function __construct($uri, $name, $size = null, $encoding = null, $type = null)
    {
        $this->uri = $uri;
        $this->name = $name;

        $this->size = $size;
        $this->encoding = $encoding;
        $this->type = $type;
    }
}
