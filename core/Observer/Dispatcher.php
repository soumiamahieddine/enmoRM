<?php
namespace core\Observer;

class Dispatcher
{
    /* Constants */

    /* Properties */
    protected static $pools=array();

    /* Methods */
    /* SplObjectStorage Interface -- NOT COMPATIBLE WITH SPLSUBJECT */

    /**
     * Attach an observer to event dispatching
     * @param IObserver $observer An Observer Service class
     * @param string    $update   The name of service method to call on event
     * @param string    $subject  The name of subject to observer
     * 
     * @return void
     */
    public static function attach($observer, $update, $subject)
    {
        if (!method_exists($observer, $update)) {
            throw new Exception("Undefined Observer update method '$update'");
        }

        if (!isset(self::$pools[$subject])) {
            self::$pools[$subject] = new Pool($subject);
        }

        $info = array('class' => get_class($observer), 'method' => $update);

        self::$pools[$subject]->attach($observer, $info);
    }

    /**
     * Detach an observer from its pools
     * @param object $observer The observer to detach
     * @param mixed  $subject  If provided, the observer will only be detached from the pool
     */
    public static function detach($observer, $subject=null)
    {
        if ($subject) {
            self::$pools[$subject]->detach($observer);
        } else {
            foreach (self::$pools as $pool) {
                if ($pool->contains($observer)) {
                    $pool->detach($observer);
                }
            }
        }
    }

    /* Dispatching Interface */
    /**
     * Dispatch an event to a pool of observers
     * @param mixed  $subject The type of subject (constant or string)
     * @param object &$object The observed object
     * @param mixed  &$info   The context of data
     *
     * @return array The returns from observers of the pool
     */ 
    public static function notify($subject, &$object=null, &$info=null)
    {
        if (isset(self::$pools[$subject])) {
            return self::$pools[$subject]->notify($object, $info);
        }
    }

    /**
     * Get a pool
     * @param string $subject
     * 
     * @return mixed
     */
    public static function getPool($subject=false)
    {
        if ($subject) {
            return self::$pools[$subject];
        } 

        return self::$pools;
    }

}