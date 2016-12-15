<?php
namespace core\Route;

abstract class AbstractRouter
{
    use \core\ReadonlyTrait;

    /* Properties */
    public $uri;

    public $reroutedUri;

    protected $steps;

    /* Methods */
    public function __construct($uri)
    {
        $this->uri = $uri;
        $this->steps = \laabs\explode(LAABS_URI_SEPARATOR, $uri);
    }

    public function reroute($uri)
    {
        $this->reroutedUri = $this->uri;

        $this->__construct($uri);
    }

}