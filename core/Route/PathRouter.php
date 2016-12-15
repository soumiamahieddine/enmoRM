<?php

namespace core\Route;

class PathRouter
    extends ContainerRouter
{
    /* Properties */

    public $api;

    public $path;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        $path = array_pop($this->steps);

        $this->setApi(implode(LAABS_NS_SEPARATOR, $this->steps));

        $this->setPath($path);
    }

    protected function setApi($name)
    {
        if ($this->container->hasApi($name)) {
            $this->api = $this->container->getApi($name);
        } else {
            throw new Exception("Undefined api '$name' in route '$this->uri'");
        }
    }

    protected function setPath($name)
    {
        if ($this->api->hasPath($name)) {
           $this->path = $this->api->getPath($name);
        } else {
            throw new Exception("Undefined path '$name' in route '$this->uri'");
        }
    }

}