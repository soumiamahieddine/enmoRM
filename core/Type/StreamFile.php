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

    public function decodeBase64()
    {
        $foutput = fopen('php://temp', 'rw');
        stream_filter_append($this->stream, 'convert.base64-decode');
        rewind($this->stream);
        stream_copy_to_stream($this->stream, $foutput);
        rewind($foutput);

        $this->stream = $foutput;
    }

    public function save($messageDirectory)
    {
        $data = stream_get_contents($this->stream);
        file_put_contents($messageDirectory . DIRECTORY_SEPARATOR . $this->name, $data);
    }
}
