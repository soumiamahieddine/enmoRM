<?php

namespace core\Route;

class ServiceRouter
    extends ContainerRouter
{
    /* Properties */
    public $service;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        $this->setService(implode(LAABS_URI_SEPARATOR, $this->steps));
    }

    public function setService($name)
    {
        if ($this->container->hasService($name)) {
            $this->service = $this->container->getService($name);
        } else {
            throw new Exception("Undefined service '$name' in service route '$this->uri'");
        }

    }

}