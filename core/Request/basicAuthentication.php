<?php
namespace core\Request;
/**
 * Class to represent basic http authentication
 */
class basicAuthentication
    extends abstractAuthentication
{
    public static $mode = LAABS_BASIC_AUTH;

    public $username;

    public $password;

    public function __construct($username, $password)
    {
        $this->username = $username;

        $this->password = $password;
    }
}