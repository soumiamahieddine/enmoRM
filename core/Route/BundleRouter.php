<?php
namespace core\Route;

class BundleRouter
    extends AbstractRouter
{
    /* Properties */
    public $bundle;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        $bundle = array_shift($this->steps);
        
        $this->setBundle($bundle);
    }

    public function setBundle($name)
    {
        if (\laabs::hasBundle($name)) {
            $this->bundle = \core\Reflection\Bundle::getInstance($name);
        } else {
            throw new Exception("Undefined bundle '$name' in uri '$this->uri'");
        }
    }

}