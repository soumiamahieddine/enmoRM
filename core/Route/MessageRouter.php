<?php

namespace core\Route;

class MessageRouter
    extends PresentationRouter
{
    /* Properties */
    public $composer;

    public $message;

    /* Methods */
    public function __construct($route)
    {
        parent::__construct($route);

       if (!$composer = array_shift($this->steps) . LAABS_URI_SEPARATOR . array_shift($this->steps)) {
            throw new Exception("Invalid message route: no composer name");
        }
        
        $this->setComposer($composer);
        if (!$message = array_shift($this->steps)) {
            throw new Exception("Invalid message route: no message name");
        }

        $this->setMessage($message);
    }

    public function setComposer($name)
    {
        if ($this->presentation->hasComposer($name)) {
            $this->composer = $this->presentation->getComposer($name);
        } else {
            throw new Exception("Undefined composer '$name' in message route '$this->uri'");
        }
    }   

    public function setMessage($name) 
    {
        if ($this->composer->hasMessage($name)) {
            $this->message = $this->composer->getMessage($name);
        } else {
            throw new Exception("Undefined message '$name' in message route '$this->uri'");
        }
    }

}