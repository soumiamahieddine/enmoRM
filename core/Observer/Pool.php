<?php

namespace core\Observer;

class Pool
    extends \SplObjectStorage
{

    /* Constants */

    /* Properties */
    protected $subject;

    /* Methods */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * Notify a pool of observers
     * @param object &$object The subject of observation
     * @param mixed  &$info   The data context
     * 
     * @return mixed
     */
    public function notify(&$object, &$info=null) 
    {
        $return = array();

        $this->rewind();

        while ($this->valid()) {
            $observer = $this->current();
            $observerInfo = $this->getInfo();
            $key = $observerInfo['class'] . "::" . $observerInfo['method'];
            $return[$key] = call_user_func_array(array($observer, $observerInfo['method']), array(&$object, &$info));
            $this->next();
        }

        return $return;
    }

}