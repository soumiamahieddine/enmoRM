<?php
/**
 * Class file for Step definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class Laabs Step
 * 
 * @extends \core\Reflection\Method
 */
class Step
    extends Method
{

    /* Constants */

    /* Properties */
    public $returnName;

    /* Methods */
    /**
     * Constructor of the batch step
     * @param string $step      The name of the step
     * @param string $class     The class of the job that declares the step
     * @param string $container The name of the service container for context transmission to other service calls
     */
    public function __construct($step, $class, $container)
    {
        parent::__construct($step, $class, $container);

        if (isset($this->tags['return'])) {
            //if (preg_match('#@return (?<type>[^\s]+)\s+\$(?<name>[\w_]+)#', $docComment, $matches)) {
            $this->returnType = strtok($this->tags['return'][0], " ");
            $this->returnName = trim(strtok(''));
        }

    }
    /**
     * Call the step
     * @param object $jobObject  Job of the step
     * @param array  $passedArgs Arguments array for the step
     * 
     * @return mixte 
     */
    public function exec($jobObject = null, array $passedArgs = null)
    {
        \core\Observer\Dispatcher::notify(LAABS_STEP_EXEC, $this, $passedArgs);

        $result = $this->invokeArgs($jobObject, $passedArgs);

        \core\Observer\Dispatcher::notify(LAABS_STEP_RESULT, $response);

        return $result;
    }

}
