<?php
/**
 * Class file for App Configuration Section
 * @package core\Configuration
 */
namespace core\Configuration;
/**
 * Class file for App Configuration Section
 * 
 * @package Core
 */
class Section
    extends \ArrayObject
{
    /* Constants */

    /* Properties */
    /**
     * The name of the section or null if config root
     * @var string
     */
    protected $name;


    /* Methods */
    /**
     * Constructor for a configuration section
     * @param string $name     The name of the configuration section
     * @param array  $contents The associative array of configuration sections or directives to load on construction or null
     * 
     * @return void
     */
    public function __construct($name = null, $contents = null)
    {
        $this->name = $name;

        if ($contents) {
            $this->load($contents);
        }
    }

    
    /**
     * Load an array of configuration sections or directives
     * @param array $contents The associative array of configuration sections or directives
     */
    public function load($contents)
    {
        foreach ($contents as $name => $content) {
            if (is_array($content)) {
                if (!isset($this[$name])) {
                    $this[$name] = new Section($name);
                }
                $this[$name]->load($content);
            } else {
                if (isset($this[$name])) {
                    continue;
                }

                // Try to find and replace constants
                if (defined($content)) {
                    $content = constant($content);
                }

                $content = trim(str_replace('&quot;', '\"', $content));
                $content = str_replace('\"', '"', $content);

                // Check name, if variable, content will be stored for later use
                if (preg_match("/^%[A-Za-z_]%$/", $name)) {
                    $varname = substr($name, 1, -1);
                    Configuration::getInstance()->registerVariable($varname, $content);
                    continue;
                }
                if (preg_match("/^\@var\.(\w+)$/", $name, $match)) {
                    $varname = $match[1];
                    Configuration::getInstance()->registerVariable($varname, $content);
                    continue;
                }
                
                // Find and replace variables
                if (preg_match_all("#%[A-Za-z_]+%#", $content, $variables)) {
                    foreach ($variables[0] as $variable) {
                        //$value = $this->getVariable($variable);
                        $varname = substr($variable, 1, -1);
                        $value = Configuration::getInstance()->getVariable($varname);
                        if (is_null($value)) {
                            throw new \Exception("Error retrieving variable '$varname' for configuration directive '$name'");
                        }
                        $content = str_replace($variable, $value, $content);
                    }
                }

                // Parse array
                if (preg_match("/^\[.+\]$/s", $content) || preg_match("/^\{.+\}$/s", $content)) {
                    $content = \laabs::decodeIni($content);
                }


                //preg_replace('#\\\\"#', '"', $value);

                // Finally set directive value
                $this->offsetSet($name, $content);
            }
        }
    }


    /**
     * Imports a configuration section
     * @param object $contents The core\Configuration\Section object to import
     */
    public function import(Section $contents)
    {
        foreach ($contents as $name => $content) {
            if ($content instanceof Section) {
                if (!isset($this[$name])) {
                    $this[$name] = new Section($name);
                }
                $this[$name]->import($content);
            } else {
                $this->offsetSet($name, $content);
            }
        }
    }

    /**
     * Exports a configuration section
     * @param string $namespace The namespace of the directives or section to export
     * 
     * @return configuration\Section
     */
    public function export($namespace = false)
    {
        $namespace = str_replace(LAABS_URI_SEPARATOR, ".", $namespace);

        $section = new Section($namespace);

        foreach ($this as $name => $content) {
            // Extract at root level
            if (!($content instanceof Section)) {
                
                /* directive defined in requested namespace : remove namespace and set local name */
                // ns.name && ns is requested => import as name
                // ns.name && not the requested ns => import as ns.name
                // name && ns not requested 
                if ($namespace) {
                    if (strpos($name, $namespace . ".") === 0) {
                        $section->offsetSet(str_replace($namespace . ".", "", $name), $content);
                    } elseif (strpos($name, ".") !== false) {
                        $section->offsetSet($name, $content);
                    }
                } else {
                    $section->offsetSet($name, $content);  
                }

            } elseif ($name == $namespace) {
                $section->import($content);
            }

        }

        return $section;
    }
}
