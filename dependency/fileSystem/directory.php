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
 * Class for file system directory
 */
class directory
    extends \splFileInfo
{
    
    protected $dirname;


    /**
     * Create the instance
     * @param string $dirname The path of the directory to read
     */
    public function __construct($dirname)
    {
        $dirname = str_replace("/", DIRECTORY_SEPARATOR, $dirname);

        parent::__construct($dirname);

        $this->dirname = $dirname;

        if (!is_dir($dirname)) {
            throw new Exception("$dirname is not a valid directory");
        }

        $this->handler = opendir($dirname);

        if (!$this->handler) {
            throw new Exception("Unable to open directory $dirname");
        }
    }

    /**
     * Static function to create a new directory on file system and return the directory object
     * @param string $dirname   The path to dir
     * @param int    $mode      The permissions
     * @param bool   $recursive Create all path
     * 
     * @return \dependency\fileSystem\directory
     */
    public static function create($dirname, $mode=0777, $recursive=true)
    {
        if (is_dir($dirname)) {
            throw new Exception("Directory $path already exists.");
        }

        $created = mkdir($dirname, $mode, $recursive);

        if (!$created) {
            throw new Exception("Directory $dirname could not be created.");
        }

        return new directory($dirname);
    }

    /**
     * Delete the file and the handler
     * 
     * @return bool
     */
    public function delete()
    {
        $files = \glob($this->dirname . DIRECTORY_SEPARATOR . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                $dir = new directory($file);
                $dir->delete();
                unset($dir);
            } else {
                unlink($file);
            }
        }
        unset($files);

        $deleted = rmdir($this->dirname);

        if (!$deleted) {
            throw new Exception("Directory $this->dirname could not be deleted");
        }

        return true;
    }
    
    /**
     * Read the next entry name in directory
     * @param bool $filesonly Return file names only
     * 
     * @return array of file/subdir paths
     */
    public function read($filesonly=false)
    {
        while (($filename = readdir($this->handler))) {
            if ($filename == '.' || $filename == '..') {
                continue;
            }
            
            $filepath = $this->dirname  . DIRECTORY_SEPARATOR . $filename;

            if ($filesonly && is_dir($filepath)) {
                continue;
            }
            
            return $filepath;
        }
    }

    /**
     * Rewind directory index
     */
    public function rewind()
    {
        rewinddir($this->handler);
    }

    /**
     * Read the directory and returns the next filename
     * @param bool $filesonly Return file names only
     * 
     * @return sring the filename
     */
    public function scan($filesonly=false)
    {
        return scandir($this->dirname);
    }

    /**
     * Read a n-uplet of files by name
     * @param bool $filesonly
     * 
     * @return array
     */
    public function readN($filesonly=false)
    {
        $filepath = $this->read($filesonly);

        $pathinfo = pathinfo($filepath);

        $filename = $pathinfo['filename'];

        $nuplet = $this->search($filename . ".*", $filesonly);

        return $nuplet;
    }

    /**
     * Search files with given path
     * @param string $pattern   The search pattern
     * @param bool   $filesonly Retrieve files only
     * @param bool   $regexp    Use regexp instead of name glob pattern
     * 
     * @return array
     */
    public function search($pattern, $filesonly=false, $regexp=false)
    {
        if (!$regexp) {
            $filenames = glob($this->dirname . DIRECTORY_SEPARATOR . $pattern);
            if ($filesonly) {
                foreach ($filenames as $index => $filename) {
                    if (is_dir($filename)) {
                        unset($filenames[$index]);
                    }
                }
            }
        } else {
            $filenames = array();
        
            foreach (scandir($this->dirname) as $filename) {
                if (preg_match($pattern, $filename)) {
                    $filenames[] = $filename;
                }
            }
        }

        return $filenames;
    } 

    /**
     * Count files
     * 
     * @return integer
     */
    public function count()
    {
        return count(scandir($this->dirname)) -2;
    }

    /**
     * Copy entire dir
     * @param string $todir
     */
    public function copy($todir) 
    { 
        $copydir = $todir . DIRECTORY_SEPARATOR . basename($this->dirname);
        @mkdir($copydir, 777, true); 

        $dirhdl = opendir($this->dirname); 
        while (false !== ($file = readdir($dirhdl))) { 
            if ($file == '.' || $file == '..') { 
                continue;
            }

            if (is_dir($this->dirname . DIRECTORY_SEPARATOR . $file)) { 
                $subdir = new directory($this->dirname . DIRECTORY_SEPARATOR . $file);
                $subdir->copy($copydir . DIRECTORY_SEPARATOR . $file); 
            } else { 
                copy($this->dirname . DIRECTORY_SEPARATOR . $file, $copydir . DIRECTORY_SEPARATOR . $file); 
            }  
        } 

        closedir($dirhdl); 
    } 

}