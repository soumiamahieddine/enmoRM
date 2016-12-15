<?php
namespace core\Request;
/**
 * Class to represent digest http authentication
 */
class digestAuthentication
    extends abstractAuthentication
{
    public static $mode = LAABS_DIGEST_AUTH;

    public $username;

    public $nonce;

    public $uri;

    public $response;

    public $qop;

    public $nc;

    public $cnonce;

    public function __construct($username, $nonce, $uri, $response, $qop, $nc, $cnonce)
    {
        $this->username = $username;

        $this->nonce = $nonce;

        $this->uri = $uri;

        $this->response = $response;

        $this->qop = $qop;

        $this->nc = $nc;

        $this->cnonce = $cnonce;
    }
}