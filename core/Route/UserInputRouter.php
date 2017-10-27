<?php

namespace core\Route;

class UserInputRouter
    extends PresentationRouter
{
    /* Properties */
    public $composer;

    public $userInput;

    /* Methods */
    public function __construct($route)
    {
        parent::__construct($route);

        if (!$composer = array_shift($this->steps) . LAABS_URI_SEPARATOR . array_shift($this->steps)) {
            throw new Exception("Invalid user input route: no composer name");
        }
        
        $this->setComposer($composer);
        if (!$userInput = array_shift($this->steps)) {
            throw new Exception("Invalid user input route: no user input name");
        }

        $this->setUserInput($userInput);
    }

    public function setComposer($name)
    {
        if ($this->presentation->hasComposer($name)) {
            $this->composer = $this->presentation->getComposer($name);
        } else {
            throw new Exception("Undefined composer '$name' in user input route '$this->uri'");
        }
    }   

    public function setUserInput($name) 
    {
        if ($this->composer->hasUserInput($name)) {
            $this->userInput = $this->composer->getUserInput($name);
        } else {
            throw new Exception("Undefined user input '$name' in user input route '$this->uri'");
        }
    }

}