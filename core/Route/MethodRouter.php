<?php

namespace core\Route;

class MethodRouter
    extends ContainerRouter
{
    /* Properties */

    public $service;

    public $method;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        $method = array_pop($this->steps);

        $this->setService(implode(LAABS_NS_SEPARATOR, $this->steps));

        $this->setMethod($method);
    }

    protected function setService($name)
    {
        if ($this->container->hasService($name)) {
            $this->service = $this->container->getService($name);
        } else {
            throw new Exception("Undefined service '$name' in method route '$this->uri'");
        }
    }

    protected function setMethod($name)
    {
        if ($this->service->hasMethod($name)) {
           $this->method = $this->service->getMethod($name);
        } else {
            throw new Exception("Undefined method '$name' in method route '$this->uri'");
        }
    }

}