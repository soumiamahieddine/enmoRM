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

    public function decodeBase64()
    {
        $inputStream = fopen($this->uri, 'r');
        $outputStream = fopen('php:temp', 'wb+');
        stream_copy_to_stream($inputStream, $outputStream);
        rewind($outputStream);

        $streamFile = \laabs::newFileFromStream(
            $outputStream,
            $this->name,
            $this->size,
            $this->encoding,
            $this->type
        );
        fclose($inputStream);
        fclose($outputStream);

        return $streamFile->decodeBase64();
    }

    public function save($messageDirectory)
    {
        $data = stream_get_contents($this->stream);
        file_put_contents($messageDirectory . DIRECTORY_SEPARATOR . $this->name, $data);
    }
}
