<?php
/**
 * Class file for Resource definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Resource definitions
 */
class Resource
{
    use \core\ReadonlyTrait;

    /* Properties */
    public $name;
    public $path;

    public $container;

    /* Methods */
    /**
     * Constructs a new resource
     * @param string $resource  The name of the resource
     * @param string $path      The path of the resource
     * @param string $container The name of the container
     * 
     * @return void
     */
    public function __construct($resource, $path, $container)
    {
        $this->name = $resource;

        $this->path = $path;

        $this->container = $container;
    }

    /**
     * Get the contents of the resource
     * 
     * @return string The function returns the read data or FALSE on failure
     */
    public function getContents()
    {
        return file_get_contents($this->path);
    }

    /**
     * Get the mime type of the resource
     * 
     * @return string The mime type of the resource
     */
    public function getMimetype()
    {
        $finfo = new \finfo();

        $type = $finfo->file($this->path, FILEINFO_MIME_TYPE);
        $encoding = $finfo->file($this->path, FILEINFO_MIME_ENCODING);

        if (strtok($type, "/") == "text") {
            switch(strtolower($this->getExtension())) {
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

        return $type . "; charset=" . $encoding;
    }

    /**
     * Get the extension of the resource
     * 
     * @return string The extension of resource
     */
    public function getExtension()
    {
        return substr($this->path, strrpos($this->path, ".")+1);
    }

    /**
     * Get the real path of the resource
     * 
     * @return string The real path of the resource
     */
    public function getRealPath()
    {
        return realpath($this->path);
    }

    /**
     * Get the path of the resource
     * 
     * @return string The real path of the resource
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get the size of the resource
     * 
     * @return int The size of the resource in bytes
     */
    public function getSize()
    {
        return filesize($this->path);
    }

}