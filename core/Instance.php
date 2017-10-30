<?php

/**
 * Class file for Laabs App Instance management
 * @package core
 */
namespace core;

/**
 * Class for Laabs App Instance management
 */
class Instance
{

    /* constants */

    const STS_NONE = 1;

    const STS_ACTIVE = 2;

    /* Properties */

    /**
     * @var mixed $savePath
     * Information about the instance storage address as awaited by the handler
     */
    protected static $savePath;

    /**
     * @var string $id
     * The id set for current instance
     */
    protected static $id;

    /**
     * @var string $name
     * The name set for current instance
     */
    protected static $name;

    /**
     * @var string $status
     * The status of instance : NONE | ACTIVE
     */
    protected static $status = 1;

    /**
     * @var object $handler
     * The instance handler object
     */
    protected static $handler;

    /**
     * @var string $hash
     * The instance hash to check for modifications before writing
     */
    protected static $hash;

    /* Méthodes */

    /**
     * Return the save path
     * @return mixed Information for handler to open the instance storage
     */
    public static function save_path()
    {

        if (!isset(self::$savePath)) {
            self::$savePath = \laabs::getInstanceSavePath();
        }

        return self::$savePath;
    }

    /**
     * Starts the instance
     * @return bool The instance could be started
     */
    public static function start()
    {
        if (\laabs::instanceDisable()) {
            return;
        }

        /* Instanciate Handler if no custome handler attached */

        if (!isset(self::$handler)) {
            $handlerClass = \laabs::getInstanceHandler();
        }
        self::$handler = new $handlerClass();

        if (!self::$handler instanceof \core\Repository\RepositoryInterface) {
            return false;
        }

        /* Calc id */
        if (!self::$id) {
            self::$id = 'laabs_instance_' . \laabs::getApp() . '_' . base_convert(md5(getmypid()), 16, 36);
        }

        if (self::$handler->open(self::save_path(), self::$id)) {
            self::$status = self::STS_ACTIVE;
            $instanceData = self::$handler->read(self::$id);
            self::$hash = md5($instanceData);
            $GLOBALS['INSTANCE'] = self::decode($instanceData);

            return true;
        }

        return false;

    }

    /**
     * Return the status
     * @return string The status
     */

    public static function status()
    {

        return self::$status;

    }

    /**
     * Decode data from instance
     * @param string $instanceData The data to be decoded
     * 
     * @return mixed the decoded data
     */

    public static function decode($instanceData)
    {

        return \unserialize($instanceData);

    }

    /**
     * Decode data for the instance
     * @param mixed $instanceData The data to encode
     * 
     * @return mixed the encoded data
     */
    public static function encode($instanceData)
    {

        return \serialize($instanceData);

    }

    /**
     * Get or set the id of the instance
     * @param string $instanceId The instance id to set or null to get the current instance id
     * 
     * @return string The instance id
     */

    public static function id($instanceId=null)
    {

        if (!$instanceId && self::$status == self::STS_ACTIVE) {
            return self::$id;
        }

        if ($instanceId && self::$status == self::STS_NONE) {
            return (self::$id = $instanceId);
        }

        return false;

    }

    /**
     * Get or set the name of the instance
     * @param string $instanceName The instance name to set or null to get the current instance name
     * 
     * @return string The instance name
     */
    public static function name($instanceName=null)
    {

        if (!$instanceName && self::$status == self::STS_ACTIVE) {
            return self::$name;
        }

        if ($instanceName && self::$status == self::STS_NONE) {
            return (self::$name = $instanceName);
        }

        return false;

    }

    /**
     * Set the handler object for the instance if not already started
     * @param object $instanceHandler The instance handler object. It must extend RepositoryInterface
     * 
     * @return string The instance name
     */
    public static function set_handler(RepositoryInterface $instanceHandler)
    {

        if (self::$status == self::STS_NONE) {
            self::$handler = $instanceHandler;
        }
    }

    /**
     * Write into instance and close the handler
     */
    public static function write_close()
    {
        if (self::$status == self::STS_NONE) {
            return;
        }

        $instanceData = self::encode($GLOBALS['INSTANCE']);

        if (self::$hash != md5($instanceData)) {
            self::$handler->write(self::$id, $instanceData);
        }
        self::$handler->close();

    }

    /**
     * Initializes instance data
     */
    public static function destroy()
    {

        $GLOBALS['INSTANCE'] = array();

        self::$hash = null;

    }

    /**
     * Regenerates a new id for the instance and destroys the storage if requested
     * @param bool $destroy Destroy the instance
     * 
     * @return bool
     */
    public static function regenerate_id($destroy = false)
    {
        if ($destroy) {
            self::destroy();
        }

        $instanceData = $GLOBALS['INSTANCE'];

        self::$id = 'laabs_instance_' . \laabs::getApp() . '_' . base_convert(md5(getmypid()), 16, 36);

        if (self::$handler->open(self::save_path(), self::$id)) {
            self::$status = self::STS_ACTIVE;

            return true;
        }

    }

}