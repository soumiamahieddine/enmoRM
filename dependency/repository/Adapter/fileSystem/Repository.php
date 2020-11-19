<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency repository.
 *
 * Dependency repository is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency repository is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency repository.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\repository\Adapter\fileSystem;

/**
 * Class for repository
 *
 * @package Dependency\Repository
 * @author  Maarch Alexis Ragot <alexis.ragot@maarch.org>
 */
class Repository implements \dependency\repository\RepositoryInterface
{
    /* Properties */
    protected $name;

    protected $root;

    protected $shred = true;

    /* Methods */
    /**
     * Constructor
     * @param string $name    The path (url, dir, ...)
     * @param array  $options The repository options
     *
     * @return void
     */
    public function __construct($name, array $options = null)
    {
        $root = str_replace("/", DIRECTORY_SEPARATOR, $name);

        if (!is_dir($root) || !is_writable($root)) {
            throw new \Exception("Repository $name doesn't exist or is not writable");
        }

        $this->name = $name;

        $this->root = $root;

        if ($options) {
            foreach ($options as $optionName => $value) {
                if (property_exists(__CLASS__, $optionName)) {
                    $this->$optionName = $value;
                }
            }
        }
    }

        // CONTAINER
    /**
     * Delete a container
     * @param string $name     The name of container
     * @param mixed  $metadata The object or array of metadata
     *
     * @return mixed The address/uri/identifier of created container on repository
     */
    public function createContainer($name, $metadata = null)
    {
        $name = str_replace("/", DIRECTORY_SEPARATOR, $name);

        $dir = $this->getDir($name);

        if ($metadata) {
            $contents = json_encode($metadata, \JSON_PRETTY_PRINT);

            $this->addFile($dir.DIRECTORY_SEPARATOR.'.metadata', $contents);
        }

        return $dir;
    }

    /**
     * Update a container metadata
     * @param string $name     The name of container
     * @param mixed  $metadata The object or array of metadata
     *
     * @return bool
     */
    public function updateContainer($name, $metadata)
    {
    }

    /**
     * Read a container metadata
     * @param string $name The name of container
     *
     * @return mixed The object or array of metadata if available
     */
    public function readContainer($name)
    {
    }

    /**
     * Delete a container
     * @param string $name The name of container
     *
     * @return bool
     */
    public function deleteContainer($name)
    {
    }

    // OBJECTS

    /**
     * Create an object
     * @param string $data The resource contents
     * @param string $path The path
     *
     * @return string The real path
     */
    public function createObject($data, $path)
    {
        $path = str_replace('/', DIRECTORY_SEPARATOR, $path);

        $realpath = $this->addFile($path, $data);

        return $realpath;
    }

    /**
     * Get a resource in repository
     * @param mixed   $path The path/uri/identifier of stored resource on repository
     * @param integer $mode A bitmask of what to read 0=nothing - only touch | 1=data | 2=metadata | 3 data+metadata
     *
     * @return mixed The contents of resource
     */
    public function readObject($path, $mode = 1)
    {
        switch ($mode) {
            case 0:
                return $this->checkFile($path);

            case 1:
                return $this->readFile($path);

            case 2:
                return $this->readFile($path . '.metadata');
            case 3:
                $data = array();
                $data[0] = $this->readFile($path);
                if ($this->checkFile($path . ".metadata")) {
                    $data[1] = json_decode($this->readFile($path . '.metadata'));
                } else {
                    $data[1] = null;
                }

                return $data;

            default:
                throw new \Exception("This mode '$mode' isn't avalaible");
        }
    }

     /**
     * Update a resource
     * @param string $path     The URI of the resource
     * @param string $data     The content
     * @param object $metadata The new metadata to update or insert
     *
     * @return bool
     */
    public function updateObject($path, $data = null, $metadata = null)
    {
        if (!is_null($data)) {
            $this->updateFile($path, $data);
        }

        if (!is_null($metadata)) {
            if ($this->checkFile($path . ".metadata")) {
                $this->updateFile($path . '.metadata', json_encode($metadata, \JSON_PRETTY_PRINT));
            } else {
                $this->addFile($path . '.metadata', json_encode($metadata, \JSON_PRETTY_PRINT));
            }
        }

        return true;
    }

     /**
     * Delete a resource
     * @param string $path The URI of the resource
     *
     * @return bool
     */
    public function deleteObject($path)
    {
        $this->deleteFile($path);

        if ($this->checkFile($path . ".metadata")) {
            $this->deleteFile($path. ".metadata");
        }

        return true;
    }

    /*
     * Non public methods
     */
    /**
     * Get the directory to store
     * @param string $pattern The name or pattern for the collection
     *
     * @return string The diretory name
     */
    protected function getDir($pattern)
    {
        // Create sub path from pattern
        $dir = false;
        $steps = explode(DIRECTORY_SEPARATOR, $pattern);

        foreach ($steps as $step) {
            $step = $this->getName($step, $dir);

            $dir .= DIRECTORY_SEPARATOR . $step;
        }

        if (!is_dir($this->root . DIRECTORY_SEPARATOR . $dir)) {
            mkdir($this->root . DIRECTORY_SEPARATOR . $dir, 0775, true);
        }

        return $dir;
    }

    protected function getName($name, $dir)
    {
        if (preg_match_all("/\<[^\>]+\>/", $name, $variables)) {
            foreach ($variables[0] as $variable) {
                $token = substr($variable, 1, -1);
                switch (true) {
                    case $token == 'app':
                        $name = str_replace($variable, \laabs::getApp(), $name);
                        break;

                    case $token == 'instance':
                        if ($instanceName = \laabs::getInstanceName()) {
                            $name = str_replace($variable, \laabs::getInstanceName(), $name);
                        } else {
                            $name = "instance";
                        }
                        break;

                    case ctype_digit($token):
                        $name = $this->getPackage($dir, $token);
                        break;

                    case substr($token, 0, 5) == 'date(':
                        $format = substr($token, 5, -1);
                        $name = str_replace($variable, date($format), $name);
                        break;
                }
            }
        }

        $illegal = array_merge(array_map('chr', range(0, 31)), array("<", ">", ":", '"', "|", "?", "*", ".."));
        $name = str_replace($illegal, "_", $name);

        return $name;
    }

    protected function getPackage($dir, $packSize)
    {
        // Retrieve current file package directory name
        $packages = scandir($this->root . DIRECTORY_SEPARATOR . $dir);
        for ($i=count($packages)-1; $i>0; $i--) {
            if ($packages[$i] == "." || $packages[$i] == "..") {
                continue;
            }

            if (!ctype_digit($packages[$i])) {
                continue;
            }

            $packagefile = $this->root . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $packages[$i];

            if (is_dir($packagefile)) {
                $size = count(scandir($packagefile)) - 2;

                if ($size < $packSize) {
                    return $packages[$i];
                } else {
                    $package = str_pad(intval($packages[$i])+1, 8, "0", STR_PAD_LEFT);
                    //mkdir($this->root . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $package, 0775, true);

                    return $package;
                }
            }
        }

        $package = str_pad('1', 8, "0", STR_PAD_LEFT);
        //mkdir($this->root . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $package, 0775, true);

        return $package;
    }

    protected function addFile($path, $data)
    {
        $dir = dirname($path);
        $dir = $this->getDir($dir);

        // Sanitize name
        $name = basename($path);
        $name = $this->getName($name, $dir);

        $filename = $this->root . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $name;

        if (!$fp = fopen($filename, 'x')) {
            throw new \Exception("Can't open file at path $path for creation.");
        }

        if (is_string($data)) {
            $wl = fwrite($fp, $data);
            if (!$wl) {
                throw new \Exception("Can't write at path $path.");
            }

            if ($wl != strlen($data)) {
                if (!unlink($filename)) {
                    throw new \Exception("Error writing at path $path, and the partial resource couldn't be deleted.");
                }
                throw new \Exception("Error writing at path $path.");
            }

            if (hash('md5', $data) != hash_file('md5', $filename)) {
                if (!unlink($filename)) {
                    throw new \Exception("Error writing at path $path, but the partial resource couldn't be deleted.");
                }
                throw new \Exception("Error writing at path $path.");
            }
        } elseif (is_resource($data)) {
            rewind($data);
            $wl = stream_copy_to_stream($data, $fp);
            rewind($data);
        }

        fclose($fp);

        return $dir . DIRECTORY_SEPARATOR . $name;
    }

    protected function checkFile($path)
    {
        $filename = $this->root . DIRECTORY_SEPARATOR . $path;

        if (!file_exists($filename)) {
            return false;
        }

        return true;
    }

    protected function readFile($path)
    {
        $filename = $this->root . DIRECTORY_SEPARATOR . $path;

        if (!file_exists($filename)) {
            throw new \Exception("Can not find resource at path $path");
        }

        if (!$data = fopen($filename, 'r')) {
            throw new \Exception("Can not read at path $path");
        }

        return $data;
    }

    protected function updateFile($path, $data)
    {
        $filename = $this->root . DIRECTORY_SEPARATOR . $path;

        if (!file_exists($filename)) {
            throw new \Exception("This file $path doesn't exist");
        }

        $backup = $filename . '.save';

        if (rename($filename, $backup)) {
            throw new \Exception("Impossible to create the temporary file at path $path");
        }

        if (!$fp = fopen($filename, 'x')) {
            throw new \Exception("File at path $path coul not be opened");
        }

        if (!$wl = fwrite($fp, $data)) {
            fclose($fp);
            if (!unlink($filename)) {
                throw new \Exception("Impossible to write at path $path - The backup " . $path . ".save" . " is available");
            }

            rename($backup, $filename);
            throw new \Exception("Impossible to write file. The file doesn't have a modification");
        }

        if ($wl != strlen($data)) {
            fclose($fp);
            if (!unlink($filename)) {
                throw new \Exception("Error in writing the file $path and it's impossible to delete but a backup " . $path . ".save" . " is available");
            }

            rename($backup, $filename);
            throw new \Exception("Error in writing the file. The file doesn't have a modification");
        }

        if (!hash('md5', $data) != hash_file('md5', $filename)) {
            fclose($fp);
            if (!unlink($filename)) {
                throw new \Exception("Error, the hash of data isn't equals at file hash and it's impossible to delete but a backup " . $path . ".save" . " is avaialble");
            }

            rename($backup, $filename);
            throw new \Exception("Error, the hash of data isn't equals at file hash. The file doesn't have a modification");
        }

        if (!unlink($backup)) {
            throw new \Exception("Impossible to delete a backup " . $path . ".save");
        }

        fclose($fp);
    }

    private function deleteFile($path)
    {
        $filename = $this->root . DIRECTORY_SEPARATOR . $path;

        if (!file_exists($filename)) {
             throw new \Exception("No resource found at path $path");
        }

        if ($this->shred) {
            $fp = fopen($filename, 'w');
            $fileSize = filesize($filename);

            $pass = 0;
            //while ($pass != 6) {
                $randNum = rand(0, 255);
                $compNum = 255 - $randNum;
                $lastNum = -1;

                do {
                    $lastNum = rand(0, 255);
                } while ($lastNum == $compNum);

                // First pass with random num
                $randContents = str_repeat(chr($randNum), $fileSize);
                fseek($fp, 0);
                fwrite($fp, $randContents);

                // Second pass with complement of random num
                $compContents = str_repeat(chr($compNum), $fileSize);
                fseek($fp, 0);
                fwrite($fp, $compContents);


                // Third pass with a new random num
                $lastContents = str_repeat(chr($lastNum), $fileSize);
                fseek($fp, 0);
                fwrite($fp, $lastContents);

                $pass++;
            //}

            fclose($fp);
        }


        if (!unlink($filename)) {
            throw new \Exception("Can not delete at path $path");
        }

        return true;
    }

    protected function writeMetadata($path, $metadata)
    {
        $dir = $this->root . DIRECTORY_SEPARATOR . dirname($path);

        $mdf = $dir . DIRECTORY_SEPARATOR . 'metadata';
        $mdh = fopen($mdf, "a");

        // Get lock on file
        $mdl = false;
        while ($mdl == false) {
            $mdl = flock($mdh, \LOCK_EX);
        }

        $stat = fstat($mdh);

        $jsonMetadata = $path . "=" . json_encode($metadata, \JSON_PRETTY_PRINT) . "\n";

        fwrite($mdh, $jsonMetadata);

        flock($mdh, LOCK_UN);

        fclose($mdh);
    }

    protected function readMetadata($path)
    {
        $dir = $this->root . DIRECTORY_SEPARATOR . dirname($path);

        $mdf = $dir . DIRECTORY_SEPARATOR . 'metadata';
        if (!is_file($mdf)) {
            $metadata = null;
        }
        $mdh = fopen($mdf, "r");

        $mdl = false;
        while ($mdl == false) {
            $mdl = flock($mdh, \LOCK_EX);
        }

        while (!feof($mdh)) {
            $line = fgets($mdh);
            if (strpos($line, $path) === 0) {
                $jsonMetadata = substr($line, strlen($path) + 1);

                $metadata = json_decode($jsonMetadata);
            }
        }

        flock($mdh, LOCK_UN);

        fclose($mdh);

        return $metadata;
    }

    protected function updateMetadata($path, $metadata)
    {
        $dir = $this->root . DIRECTORY_SEPARATOR . dirname($path);

        $mdf = $dir . DIRECTORY_SEPARATOR . 'metadata';
        if (!is_file($mdf)) {
            return $this->writeMetadata($path, $metadata);
        }

        $jsonMetadata = $path . '=' . json_encode($metadata, \JSON_PRETTY_PRINT) . "\n";

        $mdh = fopen($mdf, "r+");

        $mdl = false;
        while ($mdl == false) {
            $mdl = flock($mdh, \LOCK_EX);
        }

        $lines = array();
        $found = false;
        while (!feof($mdh)) {
            $line = fgets($mdh);
            if (strpos($line, $path) === 0) {
                $found = true;
                $line = $jsonMetadata;
            }

            $lines[] = $line;
        }

        if ($found) {
            $contents = implode('', $lines);
            fseek($mdh, 0);
            fwrite($mdh, $contents);
            ftruncate($mdh, strlen($contents));
        }

        flock($mdh, LOCK_UN);

        fclose($mdh);
    }

    protected function deleteMetadata($path)
    {
        $dir = $this->root . DIRECTORY_SEPARATOR . dirname($path);

        $mdf = $dir . DIRECTORY_SEPARATOR . 'metadata';
        if (!is_file($mdf)) {
            return;
        }

        $mdh = fopen($mdf, "r+");

        $mdl = false;
        while ($mdl == false) {
            $mdl = flock($mdh, \LOCK_EX);
        }

        $lines = array();
        $found = false;
        while (!feof($mdh)) {
            $line = fgets($mdh);
            if (strpos($line, $path) === 0) {
                $found = true;
                $line = false;
            }

            $lines[] = $line;
        }

        if ($found) {
            $contents = implode('', $lines);
            fseek($mdh, 0);
            fwrite($mdh, $contents);
            ftruncate($mdh, strlen($contents));
        }

        flock($mdh, LOCK_UN);

        fclose($mdh);
    }
}
