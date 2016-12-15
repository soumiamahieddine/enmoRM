<?php

namespace core\Route;

class ActionRouter
    extends BundleRouter
{
    /* Properties */
    public $controller;

    public $action;

    public $parameters;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        if (!$controller = array_shift($this->steps)) {
            throw new Exception("Invalid action route: no controller name");
        }
        $this->setController($controller);

        if (!$action = array_shift($this->steps)) {
            throw new Exception("Invalid action route: no action name");
        }

        $this->setAction($action);
    }

    public function setController($name)
    {
        if ($this->bundle->hasController($name)) {
            $this->controller = $this->bundle->getController($name);
        } else {

            throw new Exception("Undefined controller '$name' in action route '$this->uri'");
        }
    }

    public function setAction($name) 
    {
        if ($this->controller->hasAction($name)) {
            $this->action = $this->controller->getAction($name);
        } else {
            throw new Exception("Undefined action '$name' in action route '$this->uri'");
        }
    }

}