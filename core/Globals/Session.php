<?php

namespace core\Globals;

class Session
    extends AbstractGlobal
{
    /* Constants */
    const NAME = '_SESSION';

    /* Properties */

    /* Methods */
    public static function start($init=false) 
    {
        if (\laabs::sessionDisable()) {
            return;
        }

        if(session_status() != \PHP_SESSION_ACTIVE) {
            session_start();
        }

        if ($init) {
            self::init();
        }
    }

    public static function init() 
    {
        $_SESSION = array();
    }

}