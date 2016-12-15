<?php

namespace core\Route;

class ContainerRouter
    extends AbstractRouter
{
    /* Properties */
    public $container;

    /* Methods */
    public function __construct($uri)
    {
        parent::__construct($uri);

        $root = array_shift($this->steps);

        switch ($root) {
            case LAABS_DEPENDENCY :
                $dependency = array_shift($this->steps);
                if (!\laabs::hasDependency($dependency)) {
                    throw new \core\Exception("Error on service container route '$uri ': Dependency $dependency is not activated.");
                }
                $container = \core\Reflection\Dependency::getInstance($dependency);
                break;

            /*case LAABS_APP :
                $app = array_shift($this->steps);
                if (\laabs::getApp() != $app) {
                    throw new \core\Exception("Error on service container route '$uri ': $app is not the currently configured app.");
                }
                $container = \core\Reflection\App::getInstance();
                break;*/

            case LAABS_PRESENTATION :
                $container = \core\Reflection\Presentation::getInstance();
                break;

            default:
                $bundle = $root;
                if (!\laabs::hasBundle($bundle)) {
                    throw new \core\Exception("Error on service container route '$uri ': Bundle $bundle is not activated.");
                }
                $container = \core\Reflection\Bundle::getInstance($bundle);
        }

        // Forward container (bundle/container/app) to the next in uri (if dependency only)
        while (($step = array_shift($this->steps)) == LAABS_DEPENDENCY) {
            $dependency = array_shift($this->steps);
            if (!\laabs::hasDependency($dependency)) {
                throw new \core\Exception("Error on service container route '$uri ': Dependency $dependency is not activated.");
            }
            $container = \core\Reflection\Dependency::getInstance($dependency, $container);
        }

        $this->container = $container;

        // Unshift the last non dependency step
        array_unshift($this->steps, $step);
    }

}