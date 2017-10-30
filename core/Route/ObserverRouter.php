<?php

namespace core\Route;

class ObserverRouter
    extends ContainerRouter
{
    /* Properties */

    public $observer;

    public $handler;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        $handler = array_pop($this->steps);

        if($this->container instanceof \core\Reflection\Bundle) {
            $this->setObserver(LAABS_OBSERVER . LAABS_URI_SEPARATOR . implode(LAABS_URI_SEPARATOR, $this->steps));
        } else {
            $this->setObserver(implode(LAABS_URI_SEPARATOR, $this->steps));
        }

        $this->setHandler($handler);
    }

    protected function setObserver($name)
    {
        if ($this->container->hasService($name)) {
            $this->observer = $this->container->getService($name);
        } else {
            throw new Exception("Undefined observer '$name' in observation route '$this->uri'");
        }
    }

    protected function setHandler($name)
    {
        if ($this->observer->hasMethod($name)) {
           $this->handler = $this->observer->getMethod($name);
        } else {
            throw new Exception("Undefined handler '$name' in observation route '$this->uri'");
        }
    }

}