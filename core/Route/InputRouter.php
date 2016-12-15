<?php

namespace core\Route;

class InputRouter
    extends BundleRouter
{
    /* Properties */
    public $parser;
    
    public $input;

    /* Methods */
    public function __construct($route, $type)
    {
        parent::__construct($route);

        if (!$parser = array_shift($this->steps)) {
            throw new Exception("Invalid input route: no parser name");
        }
        $this->setParser($parser, $type);

        if (!$input = array_shift($this->steps)) {
            throw new Exception("Invalid input route: no input name");
        }
        $this->setInput($input);

    }

    public function setParser($name, $type)
    {
        if ($this->bundle->hasParser($name, $type)) {
            $this->parser = $this->bundle->getParser($name, $type);
        } else {
            throw new Exception("Undefined parser '$name' in input route '$this->uri'");
        }
    }

    public function setInput($name)
    {
        if ($this->parser->hasInput($name)) {
            $this->input = $this->parser->getInput($name);
        } else {
            throw new Exception("Undefined input '$name' in input route '$this->uri'");
        }
    }

}