<?php
namespace core\Request;
/**
 * Class to represent app authentication
 */
class remoteAuthentication
    extends abstractAuthentication
{
    public static $mode = LAABS_REMOTE_AUTH;

    public $remoteUser;
    public $authType;

    public function __construct($remoteUser, $authType)
    {
        $this->remoteUser = $remoteUser;
        $this->authType = $authType;
    }
}