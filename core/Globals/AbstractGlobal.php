<?php
namespace core\Globals;
/**
 * undocumented class
 *
 * @package Core\Globals
 */
class AbstractGlobal
{
    /* Constants */
    const NAME = 'GLOBALS';
    /* Properties */

    /* Methods */
    /**
     *  Checks if a value is set
     *
     * @param string $path The path of the value
     * 
     * @return bool
     */
    public static function exists($path) 
    {

        $steps = explode(LAABS_URI_SEPARATOR, $path);
        $name = array_pop($steps);
        
        try {
            $node = &self::getNode($steps); 
        } catch (\Exception $exception) {
            return false;
        }

        return array_key_exists($name, (array) $node);
    }

    /**
     *  Set a value into global
     *
     * @param string $path  The path of the value
     * @param mixed  $value The value
     * 
     * @return bool
     */
    public static function set($path, $value = null) 
    {
        $steps = explode(LAABS_URI_SEPARATOR, $path);
        $name = array_pop($steps);

        try {
            $node = &self::getNode($steps, true); 
        } catch (\Exception $exception) {
            return false;
        }

        $node[$name] = $value;

        return true;
    }

    /**
     *  Retrieve a value from global
     *
     * @param string $path The path of the value
     * 
     * @return mixed The value
     */
    public static function get($path) 
    {
        $steps = explode(LAABS_URI_SEPARATOR, $path);
        $name = array_pop($steps);

        try {
            $node = &self::getNode($steps); 
        } catch (\Exception $exception) {
            return;
        }

        return $node[$name];
        
    }
    /**
     *  Retrieve a value from global
     *
     * @param string $path The path of the value
     * 
     * @return void
     */
    public static function delete($path) 
    {
        $steps = explode(LAABS_URI_SEPARATOR, $path);
        $name = array_pop($steps);

        try {
            $node = &self::getNode($steps); 
        } catch (\Exception $exception) {
            return false;
        }

        unset($node[$name]);

        return true;
    }

    /**
     * Navigate to the target node from step names
     * @param array $steps  The array of step names
     * @param bool  $create Create the node path if unreachable
     *
     * @return the node 
     **/
    protected static function &getNode($steps, $create=false)
    {
        if (!isset($GLOBALS[static::NAME])) {
            throw new \core\Exception("Invalid path");
        }    

        $node = &$GLOBALS[static::NAME];

        while ($step = current($steps)) {

            if (!array_key_exists($step, $node)) {
                if ($create) {
                    $node[$step] = array();
                } else {
                    throw new \core\Exception("Invalid path");
                }
            }

            $node = &$node[$step];

            if (!is_array($node)) {
                throw new \core\Exception("Invalid path");
            }

            next($steps);
        }

        return $node;

    }
}