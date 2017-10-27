<?php
namespace core\Request;

class BatchRequest
    extends AbstractRequest
{
    use \core\ReadonlyTrait;

    public function __construct()
    {
        /* Batch.php METHOD jobName|repoName arg1=val1 arg2=val2 */
        $this->mode = 'cli';
        
        // Batch.php or any frontal
        $this->script = \laabs\basename(reset($_SERVER['argv']));

        $this->getAuthentication();
        
        // Action : run job, restart job, list repo
        $this->method = strtoupper(next($_SERVER['argv']));

        // Job name | Repo name
        $this->uri = next($_SERVER['argv']);

        while ($arg = next($_SERVER['argv'])) {
            $this->query[strtok($arg, LAABS_CLI_ARG_OPERATOR)] = strtok(LAABS_CLI_ARG_OPERATOR);
        }
    }
}