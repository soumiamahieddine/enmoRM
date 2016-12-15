<?php
/**
 * Class file for Extensions definitions
 * @package core\Reflection
 */
namespace core\Reflection;

/**
 * Class for Extensions definitions
 */
class Extensions
{
    /* Constants */

    /* Properties */
    protected static $extensions;

    /* Methods */

    /**
     *  Retrieve the list of Extension objects
     */
    public static function extensions()
    {
        if (self::$extensions === null) {
            self::$extensions = array();
            foreach (\laabs::getExtensions() as $extension) {
                self::$extensions[$extension] = Extension::getInstance($extension);
            }
        }

        return self::$extensions;
    }

    /**
     * Retrieve the most extended class name (\base\extension\class) found within all extensions
     * @param string $name The base name of the searched class
     * 
     * @return string The full name of the first class encountered in extension directories or default
     */
    public static function extendedClass($name)
    {
        /* Search class in extensions */
        foreach (self::Extensions() as $extension) {
            if ($extendedname = $extension->getClassName($name)) {
                return $extendedname;
            }
        }
        
        /* Search base class */
        if (class_exists($name)) {
            return $name;
        }
    }

    /**
     * Retrieve the most extended Interface name (\base\extension\name) found within all extensions
     * @param string $name The base name of the searched api
     * 
     * @return string The full name of the first name encountered in extension directories or default
     */
    public static function extendedInterface($name)
    {
        /* Search class in extensions */
        foreach (self::Extensions() as $extension) {
            if ($extendedname = $extension->getInterface($name)) {
                return $extendedname;
            }
        }

        /* Search base class */
        if (interface_exists($name)) {
            return $name;
        }

    }

    /**
     * Retrieve the first real path (base\extension\path) of a given file within all extensions
     * @param string $path   The path to find
     * @param bool   $unique Retrieve unique file names only or list all extensions
     * 
     * @return string The full path of the first path encountered in extension directories or default
     */
    public static function extendedPath($path, $unique = true)
    {
        $extendedPaths = array();

        /* Search path in extensions */
        foreach (self::Extensions() as $extension) {
            if ($extendedPath = $extension->getPath($path)) {
                if ($unique) {
                    return $extendedPath;
                } else {
                    $extendedPaths[] = $extendedPath;
                }
            }
        }

        /* Search base path */
        if (\laabs\file_exists($path)) {
            if ($unique) {
                return $path;
            } else {
                $extendedPaths[] = $path;
            }
        }

        if ($unique) {
            return false;
        } else {
            return $extendedPaths;
        }
    }

    /**
     * Retrieve the first real function of a given service name within all extensions
     * @param string $function The name of the function to find
     * 
     * @return string The full name of the first function encountered in extension directories or default
     */
    public static function extendedFunction($function)
    {
        /* Search function in extensions */
        foreach (self::Extensions() as $extension) {
            if ($extendedFunction = $extension->getFunction($function)) {
                return $extendedFunction;
            }
        }

        /* Search base path */
        if (function_exists($function)) {
            return $function;
        }
    }

    /**
     * Retrieve the first defined constant of a given constant name within all extensions
     * @param string $constant The name of the constant to find
     * 
     * @return string The full name of the first constant encountered in extension directories or default
     */
    public static function extendedConstant($constant)
    {
        /* Search constant in extensions */
        foreach (self::Extensions() as $extension) {
            if ($extendedConstant = $extension->getConstant($constant)) {
                return $extendedConstant;
            }
        }

        /* Search base path */
        if (defined($constant)) {
            return $constant;
        }
    }

    /**
     * List contents of a given directory in extensions and base dir
     * @param string $dir       The directory name
     * @param bool   $unique    Request list of unique file names only
     * @param bool   $filesonly Request list of files only excluding folders
     * 
     * @return array The filenames
     */
    public static function extendedContents($dir, $unique = false, $filesonly = false)
    {
        // Return array of contents
        $contents = array();

        /* Search dir in extensions */
        foreach (self::Extensions() as $extension) {
            $extendedContents = $extension->getContents($dir, $filesonly);
            if ($unique) {
                $extendedContents = array_diff($extendedContents, $contents);
            }
            $contents = array_merge($contents, $extendedContents);
        }

        /* Search path in base path */
        $baseContents = array();
        $baseDir = /* LAABS_BUNDLE . DIRECTORY_SEPARATOR . */ $dir;
        if (!is_dir($baseDir)) {
            return array_keys($contents);
        }
        foreach (scandir($baseDir) as $entry) {
            if ($entry == "." || $entry == "..") {
                continue;
            }
            $filename = $baseDir . DIRECTORY_SEPARATOR . $entry;
            if ($filesonly  && is_dir($filename)) {
                continue;
            }
            $baseContents[$filename] = $entry;
        }


        if ($unique) {
            $baseContents = array_diff($baseContents, $contents);
        }

        $contents = array_merge($contents, $baseContents);

        return array_keys($contents);
    }

    /**
     * List classes of a given namespace in extensions and base dir
     * @param string $namespace The namespace
     * @param bool   $unique    Request list of unique class names only
     * 
     * @return array The classes
     */
    public static function extendedClasses($namespace, $unique = false)
    {
        // Return array of contents
        $classes = array();

        $dir = str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $namespace);
        $classFiles = self::extendedContents($dir, $unique, $filesonly = true);
        foreach ($classFiles as $classFile) {
            $className = \laabs\basename($classFile, ".php");
            $classDir = \laabs\dirname($classFile);
            $classFile = $classDir . DIRECTORY_SEPARATOR . $className;
            $classes[] = str_replace(DIRECTORY_SEPARATOR, LAABS_NS_SEPARATOR, $classFile);
        }

        return $classes;
    }

    /**
     * List interfaces of a given namespace in extensions and base dir
     * @param string $namespace The namespace
     * @param bool   $unique    Request list of unique class names only
     * 
     * @return array The interfaces
     */
    public static function extendedInterfaces($namespace, $unique = false)
    {
        // Return array of contents
        $interfaces = array();

        $dir = str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $namespace);
        $interfaceFiles = self::extendedContents($dir, $unique, $filesonly = true);
        foreach ($interfaceFiles as $interfaceFile) {
            if (substr($interfaceFile, -13) !== LAABS_INTERFACE . '.php') {
                continue;
            }
            $name = \laabs\basename($interfaceFile, ".php");
            $interfaceDir = \laabs\dirname($interfaceFile);
            $interfaceFile = $interfaceDir . DIRECTORY_SEPARATOR . $name;
            $interfaces[] = str_replace(DIRECTORY_SEPARATOR, LAABS_NS_SEPARATOR, $interfaceFile);
        }

        return $interfaces;
    }

    /**
     * Retrieve a file extensions on real path
     * @param string $filename The filename to find
     * 
     * @return array Filenames found
     */
    public static function extendedFilenames($filename)
    {
        $filenames = array();
        /* Search path in extensions */
        foreach (self::Extensions() as $extension) {
            if ($extendedFilename = $extension->getPath($filename)) {
                $filenames[] = $extendedFilename;
            }
        }

        /* Search path in base path */
        $baseFilename = /*LAABS_BUNDLE . DIRECTORY_SEPARATOR . */$filename;
        if (\laabs\file_exists($baseFilename)) {
            $filenames[] = $baseFilename;
        }

        return $filenames;
    }

    /**
     * Extended class instanciation
     * Uses Extension system to instanciate an object using the most extended class found
     * @param string $class The name of the class to instanciate
     * @param mixed  $arg   Optional arguments that will be trannsmitted to class constructor
     * 
     * @return object The object instanciated
     */
    public function newInstance($class, $arg = null)
    {
        /* Get all args passed */
        $args = func_get_args();

        /* Remove first arg i.e. class name and keep only constructor args */
        array_shift($args);

        /* Create ReflectionClass object of real class */
        $reflectionClass = new \ReflectionClass(self::extendedClass($class));

        /* Return a new isntance of class with args */
        return $reflectionClass->newInstanceArgs($args);
    }

}