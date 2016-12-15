<?php

namespace core\Language;

class Path
{
    use \core\ReadonlyTrait;
    
    /**
     * The property
     *
     * @var property
     */
    public $property;

    /**
     * The steps
     *
     * @var array
     */
    public $steps;  
    
    /**
     * The class constructor
     * @param property $property
     * @param string   $path
     */
    public function __construct($property, $path)
    {
        $this->property = $property;

        $this->steps = \laabs\explode(LAABS_URI_SEPARATOR, $path);
    }
}