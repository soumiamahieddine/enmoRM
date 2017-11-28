<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency fileSystem.
 *
 * Dependency fileSystem is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency fileSystem is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency fileSystem.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\fileSystem\plugins;
/**
 * Class to launch jhove JSTOR/Harvard Object Validation Environment
 *
 * @package FileSystem
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class jhove
{

    protected $executable;

    protected $modules;

    public $errors;

    /**
     * Construct environment
     * @param string $javaHome     The root directory of the JRE/JDK
     * @param array  $jhoveModules The configuration of modules 
     *
     * @return void
     * @author 
     */
    public function __construct($javaHome, array $jhoveModules=array())
    {
        //putenv('JHOVE_HOME='.__DIR__.'');
        putenv('JAVA_HOME="'.$javaHome.'"');
        //putenv('CLASSPATH=' . __DIR__ . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'JhoveApp.jar');
        $classpath = 
            __DIR__ . DIRECTORY_SEPARATOR . 'jhove' . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'JhoveApp.jar' 
            . PATH_SEPARATOR . __DIR__ . DIRECTORY_SEPARATOR . 'jhove' . DIRECTORY_SEPARATOR . 'classes'
            . PATH_SEPARATOR . '.';
        $this->executable = "java -cp " . $classpath . " Jhove";

        $this->modules = $jhoveModules;
    }

    /**
     * Validate format
     * @param mixed  $filename A file, array of filenames or directory
     * @param string $module   The module to use
     * 
     * @return boolean
     */
    public function validate($filename, $module=false)
    {
        $tokens = array();
        $tokens[] = $this->executable;
        $tokens[] = "-h xml";
        $tokens[] = "-l OFF";
        $tokens[] = "-c " . __DIR__ . DIRECTORY_SEPARATOR . 'jhove' . DIRECTORY_SEPARATOR ."conf". DIRECTORY_SEPARATOR . "jhove.conf";
        if ($module) {
            $tokens[] = "-m ".$module."-hul";
        }
        $filenames = (array) $filename;
        foreach ($filenames as $filename) {
            $tokens[] = '"' . $filename . '"';
        }

        $tokens[] = '2>&1';

        $command = implode(' ', $tokens);

        $output = array();
        $return = null;

        exec($command, $output, $return);
        
        if ($return !== 0) {
            throw new \dependency\fileSystem\Exception("Failed to validate file format: execution error.", null, null, $output);
        }

        $this->errors = array();
        
        while (($line=reset($output)) && (substr($line, 0, 5) !== '<?xml')) {
            array_shift($output);
        }

        $xml = implode('', $output);
        $doc = simplexml_load_string($xml);

        $result = true;
        foreach ($doc->repInfo as $repInfo) {
            $filename = \urldecode((string) $repInfo->attributes()->uri);
            $status = (string) $repInfo->status;
            if ($status == "Well-Formed and valid") {
                return true;
            }

            if ($status == "Well-Formed, but not valid") {
                return true;
            }

            if (count($repInfo->messages->message) > 0) {
                $result = false;

                foreach ($repInfo->messages->message as $message) {
                    $this->errors[$filename][] = (string) $message->attributes()->severity . ": " . (string) $message;
                }
            }
        }

        return $result;
    }

    /**
     * Get messages
     * 
     * @return array The validation errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get the usable module for a given puid
     * @param string $puid The pronom format identifier
     * 
     * @return string $the name of the module
     */
    public function getModule($puid)
    {
        
        foreach ($this->modules as $name => $formats) {
            if (in_array($puid, $formats)) {
                return $name;
            }
        }
    }

} // END class jhove 
