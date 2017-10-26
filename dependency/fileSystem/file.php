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
namespace dependency\fileSystem;
/**
 * Class for a file in file system
 * 
 * @package Dependency/fileSystem
 */
class file
    extends \splFileObject
{
    /**
     * Constructor
     * @param string $filename
     * @param string $openmode
     */
    public function __construct($filename, $openmode="r") 
    {
        parent::__construct($filename, $openmode);
    }

    /**
     * Read contents of the file. fread is a method of splFileObject for php >= 5.5.11
     * @param integer $length The length to read
     * 
     * @return string
     */
    public function fread($length)
    {
        $fp = fopen($this->getPathname(), "r");

        return fread($fp, $length);
    }

    /**
     * Get the entire contents of the file
     */
    public function getContents($context = null, $offset=-1, $maxlen=null)
    {
        return file_get_contents($this->getPathname(), $context, $offset, $maxlen);
    }

    /**
     * Get the file format
     * 
     * @return object The Droid format
     */
    public function getFormat()
    {
        $droid = \laabs::newService('dependency/fileSystem/plugins/droid/droid');

        return $droid->match($this->getContents());
    }

    /**
     * Validate the conformity of the format 
     * 
     * @return string
     */
    public function validateFormat($module)
    {
        $contents = $this->getContents();
        $tmpfilename = \laabs\tmpnam();
        file_put_contents($contents, $tmpfilename);
        
        $jhove = \laabs::newService('dependency/fileSystem/plugins/jhove/jhove');
        
        return $jhove->file($tmpfilename, $module);
    }

}