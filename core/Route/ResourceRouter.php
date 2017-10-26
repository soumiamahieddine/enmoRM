<?php

namespace core\Route;
/**
 * undocumented class
 *
 * @package Core
 * @author  Cyril Vazquez <cyrilvazquez@maarch.org>
 **/ 
class ResourceRouter
    extends ContainerRouter
{
    /* Constants */
    const PREFIX = null;

    /* Properties */
    public $resource;

    /* Methods */
    /**
     * undocumented function
     * @param string $uri The uri of resource
     * 
     * @return void
     **/
    public function __construct($uri) 
    {
        parent::__construct($uri);

        $path = array();
        if (static::PREFIX) {
            $path[] = static::PREFIX;
        }
        while ($step = array_shift($this->steps)) {
            $path[] = $step;
        }

        $filename = implode(LAABS_URI_SEPARATOR, $path);

        $this->resource = $this->container->getResource($filename);
    }

    /**
     * undocumented function
     *
     * @return \core\Resource
     * @author 
     **/
    public function getResource()
    {
        return $this->resource;
    }

}