<?php
/**
 * Class file for Laabs Batch Kernel
 * @package core\Kernel
 */
namespace core\Kernel;
/**
 * Class Laabs Batch process Kernel
 *
 * @extends core\Kernel\AbstractKernel
 */
class BatchKernel
    extends AbstractKernel
{
    /* Constants */

    /* Properties */
    public $job;

    public $jobInstance;

    public $jobExecution;

    public $stepExecution;

    public $jobReturn;

    /* Methods */
    /**
     * Create the Request object
     * @param string $requestMode The code of request mode to create (http/cli)
     * @param string $requestType The contentType definition code used in request
     */
    protected function getRequest($requestMode='cli', $requestType='txt')
    {
        $this->request = new \core\Request\BatchRequest();
    }

    /**
     * Create the response object
     * @param string $responseType     The contentType definition code used for response
     * @param string $responseLanguage The ContentLanguage definition code for response
     */
    protected function getResponse($responseType='txt', $responseLanguage=false)
    {
        $this->response = new \core\Response\BatchResponse();
    }

    /**
     * Run the kernel to process request
     * 
     * @return mixed The return of batch process
     */
    public static function run()
    {
        /* Initalize components app/dependecy/bundle */
        self::$instance ? self::$instance->initPackages() : null;

        /* Set event dispatcher and attach observers */
        self::$instance ? self::$instance->attachObservers() : null;

        try {
            
            self::$instance->execCmd();

        } catch (\Exception $exception) {
            if (self::$instance) {
                if (!self::$instance->handleException($exception)) {
                    self::$instance->output();

                    return;
                } else {
                    return;
                }
            } else {
                return;
            }
        }

        // output 
        self::$instance->output();
    }

    /**
     * Process request
     */
    protected function execCmd()
    {
        switch ($this->request->method) {
            case 'RUN':
                $this->runJob();
                break;

            case 'RESTART':
                $this->restartJob();
                break;

            case 'LIST':
                $this->listJobs();
                break;

            case 'INFO':
                $this->jobInfo();
                break;
        }
    }

    /**
     * Run a new batch job
     */
    public function runJob()
    {
        \laabs::log("Run job " . $this->request->uri);
        
        $jobName = $this->request->uri;

        $jobRouter = new \core\Route\JobRouter($jobName);

        $this->job = $jobRouter->job;

        /* Start Instance */
        \core\JobInstance::name($jobName);
        \core\JobInstance::id(\laabs\uniqid());

        \core\JobInstance::start();

        $jobParameters = $this->request->query;

        $this->jobInstance = $this->job->create($jobParameters);

        if ($this->job->hasWorkflow()) {
            $workflow = $this->job->getWorkflow();
        } else {
            $workflow = null;
        }
        
        $steps = $this->job->getSteps();
        
        $step = current($steps);

        do {
            
            $stepParams = $step->getParameters();
            $stepArgs = array();
            foreach ($stepParams as $stepParam) {
                $paramName = $stepParam->name;
                if (array_key_exists($paramName, $GLOBALS['JOB_INSTANCE'])) {
                    $stepArgs[$paramName] = $GLOBALS['JOB_INSTANCE'][$paramName];
                }
            }

            $stepArgs = $step->getCallArgs($stepArgs);

            \laabs::notify(LAABS_STEP_EXEC, $step, $stepArgs);

            if ($workflow) {
                $workflowResult = $workflow->call($this->jobInstance, $step->name, $stepArgs);
                if ($workflowResult === false) {
                    continue;
                }
            }

            try {
                $stepReturn = $step->callArgs($this->jobInstance, $stepArgs);
            } catch (\Exception $exception) {
                $this->handleException($exception);
            }

            if (isset($step->returnName)) {
                $GLOBALS['JOB_INSTANCE'][$step->returnName] = $stepReturn;
            }

            $result = array($stepReturn);
            \laabs::notify(LAABS_STEP_RESULT, $step, $result);

        } while ($step = next($steps));
        

        \core\Observer\Dispatcher::notify(LAABS_JOB_RESULT, $this->jobInstance, $stepReturn);

        try {
            \core\JobInstance::write_close();
        } catch (\Exception $e) {
            $dmpfile = \laabs::getTmpDir() . DIRECTORY_SEPARATOR . 'job_' . \core\JobInstance::id() . '.dmp';
            file_put_contents($dmpfile, print_r($GLOBALS['JOB_INSTANCE'], true));

            throw new \core\Exception("Unable to save job instance. Instance dumped in tmp directory", 0, $e);
        }

        $this->response->setBody($stepReturn);
    }

    /**
     * List jobs on a repo
     * 
     * @return mixed
     */
    public function listJobs()
    {
        $jobName = $this->request->uri;

        $jobRouter = new \core\Route\JobRouter($jobName);

        $this->job = $jobRouter->job;

        $filterArgs = $this->request->arguments;

        if (isset($this->job->handler)) {
            $handler = new $this->job->handler();
        } else {
            $handler = new \core\repository\php();
        }

        if (isset($this->job->savePath)) {
            $savePath = $this->job->savePath;
        } else {
            $savePath = $handler->save_path();
        }

        $jobIds = $handler->scan($savePath, 'laabs_job_');

        foreach ($jobIds as $jobId) {
            $this->response->setBody($jobId);
        }
    }

    /**
     * Get jobs info
     * 
     * @return mixed
     */
    public function jobInfo()
    {
        $jobName = \laabs\dirname($this->request->uri);
        $jobId = \laabs\basename($this->request->uri);

        $jobRouter = new \core\Route\JobRouter($jobName);

        $this->job = $jobRouter->job;

        /* Start Instance */
        if (isset($this->job->savePath)) {
            \core\JobInstance::save_path($this->job->savePath);
        }
        if (isset($this->job->handler)) {
            \core\JobInstance::set_handler(new $this->job->handler());
        }

        \core\JobInstance::id($jobId);

        \core\JobInstance::start();
            
    }

    /**
     * Restart an existing job
     * 
     * @return mixed
     */
    public function restartJob()
    {
        $jobPath = $this->request->uri;

        $repositoryName = strtok($jobPath, LAABS_URI_SEPARATOR);
        $jobId = strtok(LAABS_URI_SEPARATOR);

        $jobParameters = $this->request->arguments;
     
        $repositoryHandler = new $repositoryDefinition->class();

        $job = $repositoryHandler->open($repositoryDefinition->path, $jobId);
        if (!$job) {
            throw new \core\Exception("No job found in repository $repositoryName with id $jobId");
        }
        
        $jobReturn = $job->restart();

        $this->response->setBody($jobReturn);        
    }

    /**
     * Handle Exception sent by job
     * @param \Exception $exception The Exception thrown by the job
     *
     * @return bool
     */
    public function handleException(\Exception $exception)
    {
        \core\Observer\Dispatcher::notify(LAABS_BUSINESS_EXCEPTION, $exception);

        $exceptionName = \laabs\basename(get_class($exception));
        
        if ($this->job->hasMethod($exceptionName)) {

            $exceptionStep = $this->job->getStep($exceptionName);
            $exceptionReturn = $exceptionStep->callArgs($this->jobInstance, array($exception));

        } else {

            $this->response->setBody((string) $exception);

            $this->output();

            exit;
        }

    }

    /**
     * Output
     */
    protected function output()
    {
        // Buffer will return void if "LAABS_CLEAN_BUFFER" directive set for app
        $this->useBuffer();

        $responseContents = $this->response->send();
        $out = fopen('php://output', 'w'); //output handler
        fputs($out, $responseContents); //writing output operation
        fclose($out); //closing handler
    }

    

}