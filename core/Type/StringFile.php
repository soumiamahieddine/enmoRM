<?php

namespace core\Type;

class StringFile extends abstractFile
{
    protected $data;

    public function __construct($data, $name, $size = null, $encoding = null, $type = null)
    {
        $this->data = $data;
        $this->name = $name;
        $this->size = $size;
        $this->encoding = $encoding;
        $this->type = $type;
    }
}
