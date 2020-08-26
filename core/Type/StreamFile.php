<?php

namespace core\Type;

class StreamFile extends abstractFile
{
    protected $stream;

    public function __construct($stream, $name, $size = null, $encoding = null, $type = null)
    {
        $this->stream = $stream;
        $this->name = $name;

        $this->size = $size;
        $this->encoding = $encoding;
        $this->type = $type;
    }
}
