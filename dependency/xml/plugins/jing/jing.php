<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency xml.
 *
 * Dependency xml is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency xml is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency xml.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\Xml\plugins\jing;
/**
 * Plugin for RelaxNg validation tool jing
 */
class jing
{

    protected $executable;
    protected $errors;

    /**
     * Construct environment
     *
     * @return void
     * @author 
     */
    public function __construct()
    {
        $this->executable = "java -jar " . __DIR__ . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . "jing.jar";
    }

    /**
     * Validate file
     * @param string $schema A schema file
     * @param mixed  $xml    An xml file or an array of filenames
     * 
     * @return boolean
     */
    public function validate($schema, $xml)
    {
        $tokens = array();
        $tokens[] = $this->executable;
        $tokens[] = '"'.$schema.'"';
        $xmls = (array) $xml;
        foreach ($xmls as $xml) {
            $tokens[] = '"'.$xml.'"';
        }
        
        $command = implode(' ', $tokens);

        $output = array();
        $return = null;

        exec($command, $output, $return);

        if ($return !== 0) {
            foreach ($output as $line) {
                $sep = strpos($line, ": ");
                $position = substr($line, 0, $sep);
                $message = substr($line, $sep+2);
                
                $posparts = explode(':', $position);
                $offset = array_pop($posparts);
                $line = array_pop($posparts);
                $filename = implode(':', $posparts);
                
                $this->errors[] = $message;
            }

            return false;
        }

        return true;
    }

    /**
     * Get errors
     * 
     * @return array The validation errors
     */
    public function getErrors()
    {
        return $this->errors;
    }

}