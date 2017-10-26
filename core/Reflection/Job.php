<?php
/**
 * Class file for batch Job definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for batch Job definitions
 * 
 * @extends \core\Reflection\Service
 */
class Job
    extends Service
{

    /* Constants */

    /* Properties */
    public $handler;

    public $savePath;

    /* Methods */
    /**
     * Constructor of the job
     * @param string $name      The name of the job
     * @param string $class     The class of the job
     * @param object $container The job container object
     */
    public function __construct($name, $class, $container)
    {
        $uri = LAABS_JOB . LAABS_URI_SEPARATOR . $name;

        parent::__construct($uri, $class, $container);

        $docComment = $this->getDocComment();
        if (isset($this->tags['repository'])) {
            $repository = $this->tags['repository'][0];
            preg_match("#(?<class>[^\s]+)\s(?<path>[^\s]+)#", $repository, $matches);
            $this->handler = trim($matches['class']);
            $this->savePath = trim($matches['path']);
        }
    }

    /**
     * Checks if the job has a workflow method (wf agent)
     * @return bool
     */
    public function hasWorkflow()
    {
        return $this->hasStep('__invoke');
    }

    /**
     * Get the observer method
     * @return bool
     */
    public function getWorkflow()
    {
        return $this->getStep('__invoke');
    }

    /**
     * Checks if step is defined on the job
     * @param string $name The name of the step
     * 
     * @return bool
     */
    public function hasStep($name)
    {
        return $this->hasMethod($name);
    }

    /**
     * Get all steps defined on the job
     * 
     * @return array An array of all the \core\Reflection\Step objects
     */
    public function getSteps()
    {
        $reflectionMethods = $this->getMethods(\ReflectionMethod::IS_PUBLIC);
        $steps = array();
        foreach ($reflectionMethods as $name => $reflectionMethod) {
            if ($reflectionMethod->isConstructor()
                || $reflectionMethod->isDestructor()
                || $reflectionMethod->isStatic()
                || $reflectionMethod->isAbstract()
                || $reflectionMethod->name == '__invoke'
                || substr($reflectionMethod, -9) == 'Exception'
            ) {
                continue;
            }

            $steps[] = new Step($reflectionMethod->name, $this->name, $this->container);
        }

        return $steps;
    }

    /**
     * Get a step definition
     * @param string $name The name of the job
     * 
     * @return object the \core\Reflection\Step object
     * 
     * @throws core\Reflection\Exception if the job is unknown or not public
     */
    public function getStep($name)
    {
        if (!$this->hasStep($name)) {
            throw new \Exception("Undefined step '$this->container/$this->name/$name'");
        }

        $step = new Step($name, $this->name, $this->container);

        if (!$step->isPublic()) {
            throw new \Exception("Step '$name' is not public");
        }

        return $step;
    }

    /**
     * Call the job with parameters
     * Send a LAABS_JOB_EXEC before call and a LAABS_JOB_RESULT after call
     * @param array $passedArgs An indexed or associative array of arguments to be passed to the job
     * 
     * @return object The Job object
     * 
     * @see newInstance();
     */
    public function create(array $passedArgs=null)
    {
        \core\Observer\Dispatcher::notify(LAABS_JOB_CREATE, $this, $passedArgs);

        $jobObject = $this->newInstance($passedArgs);

        return $jobObject;
    }

}
