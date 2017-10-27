<?php

namespace core\Route;

class JobRouter
    extends ContainerRouter
{
    /* Properties */

    public $job;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        $jobName = array_shift($this->steps);

        $this->setJob($jobName);
    }

    protected function setJob($name)
    {
        if ($this->container->hasJob($name)) {
            $this->job = $this->container->getJob($name);
        } else {
            throw new Exception("Undefined job '$name' in route '$this->uri'");
        }
    }

}