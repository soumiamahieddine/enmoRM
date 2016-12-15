<?php

namespace core\Reflection;

class Exception
    extends Service
{
    /* Propriétés */
    /*
    protected string $message;
    protected int $code;
    protected string $file;
    protected int $line;
    */
    /* Méthodes */
    /**
     * Constructor of the exception
     * @param string $name      The name of the exception
     * @param string $classname The class of the exception
     * @param object $bundle    The bundle object
     */
    public function __construct($name, $classname, $bundle)
    {
        $uri = LAABS_EXCEPTION . LAABS_URI_SEPARATOR . $name;

        parent::__construct($uri, $classname, $bundle);
    }

}