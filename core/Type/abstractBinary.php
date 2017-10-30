<?php
namespace core\Type;
/**
 * Abstract class for binary data
 */
abstract class abstractBinary
    implements \JsonSerializable
{
    
    /**
     * The data
     * @var string
     */
    protected $data;

    /**
     * The type of data
     * @var string
     */
    protected $encoding;

    /**
     * The charset of text data
     * @var string
     */
    protected $charset;

    /**
     * Construct a new xml object
     * @param string $data     The data
     * @param string $mimetype The mime type of the data
     */
    public function __construct($data, $mimetype=null)
    {
        $this->data = $data;

        if (!$mimetype) {
            $finfo = new \finfo();
            $this->encoding = $finfo->buffer($data, FILEINFO_MIME_TYPE);
            $this->charset = $finfo->buffer($data, FILEINFO_MIME_ENCODING);
        } else {
            $this->encoding = strtok($mimetype, ";");
            $this->charset = strtok(";");
        }
    }

    /**
     * Get string
     * @return string
     */
    public function __toString()
    {
        return $this->data;
    }

    /**
     * Get size
     * @return integer
     */
    public function getLength()
    {
        return strlen($this->data);
    }

    /**
     * Get encoding
     * @return string
     */
    public function getEncoding()
    {
        return strlen($this->encoding);
    }

}