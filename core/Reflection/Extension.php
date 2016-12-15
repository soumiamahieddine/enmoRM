<?php
/**
 * Class file for Extension definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Extension definitions
 */
class Extension
{
    use \core\ReadonlyTrait;

    /* Constants */

    /* Properties */
    protected static $instances = array();

    public $name;

    /* Methods */
    /**
     * Get an extension instance singleton.
     * The instance singleton is identified by the name of extension
     * @param string $name The name of the extension to get instance of
     * 
     * @return \core\Reflection\Extension object
     */
    public static function getInstance($name)
    {
        if (!isset(self::$instances[$name])) {
            self::$instances[$name] = new Extension($name);
        }

        return self::$instances[$name];
    }

    /**
     * Constructs a new dependency bundle
     * @param string $name The name of the extension
     * 
     * @return void
     * 
     * @access protected
     */
    protected function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Checks if the bundle is defined on the extension
     * @param string $bundle The name of the bundle
     * 
     * @return bool
     */
    /*public function hasBundle($bundle)
    {
        return is_dir(LAABS_EXTENSION . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . LAABS_BUNDLE . DIRECTORY_SEPARATOR . $bundle);
    }*/

    /**
     * Get a bundle definition
     * @param string $bundle The name of the bundle
     * 
     * @return object the \core\Reflection\Bundle object
     */
    /*public function getBundle($bundle)
    {
        if (is_dir(LAABS_EXTENSION . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . LAABS_BUNDLE . DIRECTORY_SEPARATOR . $bundle)) {
            return Bundle::getInstance($bundle);
        }
    }*/

    /**
     * Checks if the Interface is defined on the extension
     * @param string $name The name of the Interface
     * 
     * @return bool
     */
    public function hasInterface($name)
    {
        return interface_exists(LAABS_EXTENSION . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . $name);
    }

    /**
     * Get a Interface definition
     * @param string $name The name of the Interface
     * 
     * @return string The path of the Interface
     */
    public function getInterface($name)
    {
        $extendedInterface = LAABS_EXTENSION . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . $name;

        if (interface_exists($extendedInterface)) {
            return $extendedInterface;
        }
    }
    
    /**
     * Checks if the class is defined on the extension
     * @param string $class The name of the class
     * 
     * @return bool
     */
    public function hasClass($class)
    {
        return class_exists(LAABS_EXTENSION . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . $class);
    }

    /**
     * Get a class definition
     * @param string $class The name of the class
     * 
     * @return string The path of the class
     */
    public function getClassName($class)
    {
        $extendedClass = LAABS_EXTENSION . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . $class;

        if (class_exists($extendedClass)) {
            return $extendedClass;
        }
    }

    /**
     * Checks if the function is defined on the extension
     * @param string $function The name of the function
     * 
     * @return bool
     */
    public function hasFunction($function)
    {
        return function_exists(LAABS_EXTENSION . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . $function);
    }

    /**
     * Checks if the file is defined on the extension
     * @param string $file The name of the file
     * 
     * @return bool
     */
    public function hasPath($file)
    {
        return \laabs\file_exists(LAABS_EXTENSION . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . $file);
    }

    /**
     * Get a file definition
     * @param string $file The name of the class
     * 
     * @return string The path of the file
     */
    public function getPath($file)
    {
        $extendedfile = LAABS_EXTENSION . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR . $file;
        
        if (\laabs\file_exists($extendedfile)) {
            return $extendedfile;
        }
    }

    /**
     * Checks if the constant is defined on the extension
     * @param string $constant The name of the constant
     * 
     * @return bool
     */
    public function hasConstant($constant)
    {

        return defined(LAABS_EXTENSION . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . $constant);

    }

    /**
     * Get a constant definition
     * @param string $constant The name of the constant
     * 
     * @return string The path of the constant
     */
    public function getConstant($constant)
    {
        $extendedConstant = LAABS_EXTENSION . LAABS_NS_SEPARATOR . $this->name . LAABS_NS_SEPARATOR . $constant;

        if (defined($extendedConstant)) {
            return $extendedConstant;
        }
    }

    /**
     * Get a contents of directory
     * @param string $dir       The directory
     * @param bool   $filesonly The option to get files only
     * 
     * @return array An associative array by the filename or directory
     */
    public function getContents($dir, $filesonly=false)
    {
        $contents = array();

        if (!$this->hasPath($dir)) {
            return $contents;
        }

        $extendedDir = $this->getPath($dir);
        foreach (scandir($extendedDir) as $entry) {
            if ($entry == "." || $entry == "..") {
                continue;
            }

            $filename = $extendedDir . DIRECTORY_SEPARATOR . $entry;

            if ($filesonly  && is_dir($filename)) {
                continue;
            }

            $contents[$filename] = $entry;
        }

        return $contents;
    }

}