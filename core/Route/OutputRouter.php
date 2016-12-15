<?php

namespace core\Route;

class OutputRouter
    extends BundleRouter
{
    /* Properties */
    public $serializer;
    public $output;

    /* Methods */
    public function __construct($route, $type)
    {
        parent::__construct($route);

        if (!$serializer = array_shift($this->steps)) {
            throw new Exception("Invalid output route: no serializer name");
        }
        $this->setSerializer($serializer, $type);

        if (!$output = array_shift($this->steps)) {
            throw new Exception("Invalid output route: no output name");
        }
        $this->setOutput($output);
    }

    public function setSerializer($name, $type)
    {
        if ($this->bundle->hasSerializer($name, $type)) {
            $this->serializer = $this->bundle->getSerializer($name, $type);
        } else {
            throw new Exception("Undefined serializer '$type/$name' in output route '$this->uri'");
        }

    }

    public function setOutput($name) 
    {
        if ($this->serializer->hasOutput($name)) {
            $this->output = $this->serializer->getOutput($name);
        } else {
            throw new Exception("Undefined output '$name' in output route '$this->uri'");
        }
    }

}