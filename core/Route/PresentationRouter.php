<?php
namespace core\Route;

class PresentationRouter
    extends AbstractRouter
{
    /* Properties */
    public $presentation;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        $this->presentation = \core\Reflection\Presentation::getInstance();
    }

}