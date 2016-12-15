<?php
namespace core\Route;

class AppRouter
    extends ContainerRouter
{
    /* Properties */
    public $app;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        $this->app = $this->container;
    }

}