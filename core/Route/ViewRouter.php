<?php

namespace core\Route;

class ViewRouter
    extends PresentationRouter
{
    /* Properties */
    public $presenter;

    public $view;

    /* Methods */
    public function __construct($route)
    {
        parent::__construct($route);

        if (!$presenter = array_shift($this->steps) . LAABS_URI_SEPARATOR . array_shift($this->steps)) {
            throw new Exception("Invalid view route: no prensenter name");
        }
        
        $this->setPresenter($presenter);
        if (!$view = array_shift($this->steps)) {
            throw new Exception("Invalid view route: no view name");
        }

        $this->setView($view);
    }

    public function setPresenter($name)
    {
        if ($this->presentation->hasPresenter($name)) {
            $this->presenter = $this->presentation->getPresenter($name);
        } else {
            throw new Exception("Undefined presenter '$name' in view route '$this->uri'");
        }
    }   

    public function setView($name) 
    {
        if ($this->presenter->hasView($name)) {
            $this->view = $this->presenter->getView($name);
        } else {
            throw new Exception("Undefined view '$name' in view route '$this->uri'");
        }
    }

}