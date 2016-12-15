<?php
namespace core\Request;

class CliRequest
    extends AbstractRequest
{

    public function __construct()
    {
        /* Cli.php METHOD /bundle/controller/action/param1/param2 arg1=val1 arg2=val2 */
        $this->mode = 'cli';

        $this->script = \laabs\basename(reset($_SERVER['argv']));

        $this->getAuthentication();

        $this->method = next($_SERVER['argv']);

        $cmd = next($_SERVER['argv']);
        if ($cmd && $cmd[0] == LAABS_URI_SEPARATOR) {
            $cmd = substr($cmd, 1);
        }

        $this->uri = $cmd;

        $this->queryType = "arg";

        $this->contentType = "url";

        while ($arg = next($_SERVER['argv'])) {
            if (preg_match("#^(?<type>\w+):\\/\\/(?<body>.*)$#", $arg, $matches)) {
                switch ($matches['type']) {
                    case 'data':
                        $this->body = $matches['body'];
                        break;

                    case 'file':
                    case 'href':
                        $this->body = file_get_contents($matches['body']);
                        break;

                    case 'url':
                        $this->contentType = 'url';
                        $this->body = $matches['body'];
                        break;
                }
            } elseif ($arg[0] == '-') {
                $arg = substr($arg, 1);
                $sep = strpos($arg, ":");
                $name = substr($arg, 0, $sep);
                $value = substr($arg, $sep+1);
                switch ($name) {
                    case 'token':
                        $this->parseTokens($value);
                        break;

                    case 'tokenfile':
                        $this->parseTokenFile($value);
                        break;
                    default:
                        $this->parseHeader($name, $value);
                }

            } else {
                $argname = strtok($arg, LAABS_CLI_ARG_OPERATOR);
                $argvalue = strtok(LAABS_CLI_ARG_OPERATOR);
                if (!isset($this->query[$argname])) {
                    $this->query[$argname] = $argvalue;
                } else {
                    $this->query[$argname] = array($this->query[$argname], $argValue);
                }
            }
        }
    }

    protected function parseTokenFile($tokenFile)
    {
        if (!is_file($tokenFile)) {
            throw new \Exception("Token file $tokenFile not found");
        }

        $tokenString = file_get_contents($tokenFile);

        $this->parseTokens($tokenString);
    }

    protected function parseTokens($tokenString)
    {
        foreach (explode(';', $tokenString) as $tokenPair) {
            list($name, $value) = explode('=', trim($tokenPair));

            $key = \laabs::getCryptKey();

            $binToken = base64_decode($value);
            $jsonToken = \laabs::decrypt($binToken, $key);
            $token = \json_decode(trim($jsonToken));

            $this->token[$name] = $token;
            $GLOBALS['TOKEN'][$name] = $token;
        }
    }

    protected function parseHeader($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $this->getPriorisedList($value);
        }
    }

    /**
     * Utility to retrieve an associative array from separated value, with priority/quality index as in RFC2616
     * Linked list with order/priority retrieval
     *   For RFC2616 ranges with quality/priority
     *   <<< item1,item2;item3...itemN,q=0.8;...itemM,q=0.1
     *   >>> array(
     *           item1 => 1.0,
     *           item2 => 1.0,
     *           item3 => 0.8,
     *           ...
     *           itemN => 0.8,
     *           ...
     *           itemM => 0.1,
     *       )
     * @param string $value The value
     *
     * @return array
     */
    protected function getPriorisedList($value)
    {
        $list = [];

        // Check in $_SERVER
        $priorised = [];
        foreach (\laabs\explode(LAABS_CONF_LIST_SEPARATOR, $value) as $accept) {
            $q = '1.0';
            foreach (\laabs\explode(",", $accept) as $acceptItem) {
                if ($acceptItem[0] == 'q') {
                    $q = substr($acceptItem, 2);
                    continue;
                }
                $priorised[$q][] = $acceptItem;
            }
        }

        krsort($priorised);

        foreach ($priorised as $priority => $items) {
            foreach ($items as $item) {
                $list[$item] = $priority;
            }
        }

        return $list;
    }
}
