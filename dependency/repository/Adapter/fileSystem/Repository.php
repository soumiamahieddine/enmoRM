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
class Repository
    implements \dependency\repository\RepositoryInterface
{
    /* Properties */
    protected $name;

    protected $root;

    protected $collection = "<app>/<Y>/<m>/<d>/<1000>";

    protected $shred = true;

    /* Methods */
    /**
     * Constructor
     * @param string $name    The path (url, dir, ...)
     * @param array  $options The repository options
     *
     * @return void
     */
    public function __construct($name, array $options=null)
    {
        $root = str_replace("/", DIRECTORY_SEPARATOR, $name);

        if (!is_dir($root)) {
            throw new \Exception("Repository $name doesn't exist");
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

    /**
     * Create a resource
     * @param string $data       The resource contents
     * @param string $collection The name of a collection to which add resource
     *
     * @return string The URI of the resource
     */
    public function create($data, $collection=null)
    {
        // Retrieve current pattern directory name
        $address = $this->getDir($collection);
        
        $address .= DIRECTORY_SEPARATOR . $this->getName($address);

        $this->addFile($address, $data);

        /*if (!is_null($metadata)) {
            $this->addFile($address . '.metadata', json_encode($metadata, \JSON_PRETTY_PRINT));
        }*/

        return $address;
    }

    /**
     * Get a resource in repository
     * @param mixed   $address The address/uri/identifier of stored resource on repository
     * @param integer $mode    A bitmask of what to read 0=nothing - only touch | 1=data | 2=metadata | 3 data+metadata
     * 
     * @return mixed The contents of resource
     */
    public function read($address, $mode=1)
    {
        switch ($mode) {
            case 0 :
                return $this->checkFile($address);

            case 1 :
                return $this->readFile($address);

            case 2 :
                return $this->readFile($address . '.metadata');
            case 3 :
                $data = array();
                $data[0] = $this->readFile($address);
                if ($this->checkFile($address . ".metadata")) {
                    $data[1] = json_decode($this->readFile($address . '.metadata'));
                } else {
                    $data[1] = null;
                }

                return $data;

            default :
                throw new \Exception("This mode '$mode' isn't avalaible");
        }
        
    }

     /**
     * Update a resource
     * @param string $address  The URI of the resource
     * @param string $data     The content
     * @param object $metadata The new metadata to update or insert 
     *
     * @return bool
     */
    public function update($address, $data=null, $metadata=null)
    {
        if (!is_null($data)) {
            $this->updateFile($address, $data);
        }
        
        if (!is_null($metadata)) {
            if ($this->checkFile($address . ".metadata")) {
                $this->updateFile($address . '.metadata', json_encode($metadata, \JSON_PRETTY_PRINT));
            } else {
                $this->addFile($address . '.metadata', json_encode($metadata, \JSON_PRETTY_PRINT));
            }
        }

        return true;
    }

     /**
     * Delete a resource
     * @param string $address The URI of the resource
     *
     * @return bool
     */
    public function delete($address)
    {
        $this->deleteFile($address);
        
        if ($this->checkFile($address . ".metadata")) {
            $this->deleteFile($address. ".metadata");
        }
        
        return true;
    }

    /* 
     * Non public methods
     */
    /**
     * Get the directory to store
     * @param string $collection The name or pattern for the collection
     * 
     * @return string The diretory name
     */
    protected function getDir($collection=false)
    {
        if ($collection) {
            $dirPattern = str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $collection);
        } else {
            $dirPattern = str_replace(LAABS_URI_SEPARATOR, DIRECTORY_SEPARATOR, $this->collection);
        }

        // Create sub path from pattern
        $dir = false;
        $steps = explode(DIRECTORY_SEPARATOR, $dirPattern);
        foreach ($steps as $step) {
            if (preg_match_all("/\<[^\>]+\>/", $step, $variables)) {
                foreach ($variables[0] as $variable) {
                    $token = substr($variable, 1, -1);
                    switch (true) {
                        case $token == 'app':
                            $step = str_replace($variable, \laabs::getApp(), $step);
                            break;

                        case $token == 'inst':
                            if ($instanceName = \laabs::getInstanceName()) {
                                $step = str_replace($variable, \laabs::getInstanceName(), $step);
                            } else {
                                $step = "inst";
                            }
                            break;

                        case ctype_digit($token):
                            $step = $this->getPackage($dir, $token);
                            break;

                        default:
                            $step = str_replace($variable, date($token), $step);
                    }

                }
            }

            $dir .= DIRECTORY_SEPARATOR . $step;

            $illegal = array_merge(array_map('chr', range(0, 31)), array("<", ">", ":", '"', "|", "?", "*", ".."));
            $dir = str_replace($illegal, "_", $dir);

            if (!is_dir($this->root . DIRECTORY_SEPARATOR . $dir)) {
                mkdir($this->root . DIRECTORY_SEPARATOR . $dir, 0777, true);
            } 
        }

        return $dir;
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
                    mkdir($this->root . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $package, 0755, true);

                    return $package;
                }
            }
            
        }

        $package = str_pad('1', 8, "0", STR_PAD_LEFT);
        mkdir($this->root . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $package, 0755, true);

        return $package;
    }

    protected function getName($address)
    {  
        $current = 0;
        $length = 8;
        
        $decnames = preg_grep("#^\d+$#", scandir($this->root . DIRECTORY_SEPARATOR . $address));
        
        if (count($decnames) > 0) {
            sort($decnames); 
            $current = intval(end($decnames));
        }

        return str_pad($current+1, $length, "0", STR_PAD_LEFT);
    }

    protected function addFile($address, $data)
    {
        $filename = $this->root . DIRECTORY_SEPARATOR . $address;

        if (!$fp = fopen($filename, 'x')) {
            throw new \Exception("Can't open file at address $address for creation.");
        }
        $wl = fwrite($fp, $data);
        if (!$wl) {
            throw new \Exception("Can't write at address $address.");
        }

        if ($wl != strlen($data)) {
            if (!unlink($filename)) {
                throw new \Exception("Error writing at address $address, and the partial resource couldn't be deleted.");
            }
            throw new \Exception("Error writing at address $address.");
        }
        fclose($fp);

        if (hash('md5', $data) != hash_file('md5', $filename)) {
            if (!unlink($filename)) {
                throw new \Exception("Error writing at address $address, but the partial resource couldn't be deleted.");
            }
            throw new \Exception("Error writing at address $address.");
        }
    }

    protected function checkFile($address)
    {
        $filename = $this->root . DIRECTORY_SEPARATOR . $address;

        if (!file_exists($filename)) {
            return false;
        }

        return true;
    }

    protected function readFile($address)
    {
        $filename = $this->root . DIRECTORY_SEPARATOR . $address;

        if (!file_exists($filename)) {
            throw new \Exception("Can not find resource at address $address");
        }

        if (!$data = file_get_contents($filename)) {
            throw new \Exception("Can not read at address $address");
        }

        return $data;
    }

    protected function updateFile($address, $data)
    {
        $filename = $this->root . DIRECTORY_SEPARATOR . $address;

        if (!file_exists($filename)) {
            throw new \Exception("This file $address doesn't exist");
        }

        $backup = $filename . '.save';

        if (rename($filename, $backup)) {
            throw new \Exception("Impossible to create the temporary file at address $address");
        }

        if (!$fp = fopen($filename, 'x')) {
            throw new \Exception("File at address $address coul not be opened");
        }

        if (!$wl = fwrite($fp, $data)) {
            fclose($fp);
            if (!unlink($filename)) {
                throw new \Exception("Impossible to write at address $address - The backup " . $address . ".save" . " is available");
            }

            rename($backup, $filename);
            throw new \Exception("Impossible to write file. The file doesn't have a modification");
        }

        if ($wl != strlen($data)) {
            fclose($fp);
            if (!unlink($filename)) {
                throw new \Exception("Error in writing the file $address and it's impossible to delete but a backup " . $address . ".save" . " is available");
            }
            
            rename($backup, $filename);
            throw new \Exception("Error in writing the file. The file doesn't have a modification");
        }

        if (!hash('md5', $data) != hash_file('md5', $filename)) {
            fclose($fp);
            if (!unlink($filename)) {
                throw new \Exception("Error, the hash of data isn't equals at file hash and it's impossible to delete but a backup " . $address . ".save" . " is avaialble");
            }

            rename($backup, $filename);
            throw new \Exception("Error, the hash of data isn't equals at file hash. The file doesn't have a modification");
        }

        if (!unlink($backup)) {
            throw new \Exception("Impossible to delete a backup " . $address . ".save");
        }

        fclose($fp);
    }

    private function deleteFile($address)
    {  
        $filename = $this->root . DIRECTORY_SEPARATOR . $address;

        if (!file_exists($filename)) {
             throw new \Exception("No resource found at address $address");
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
            throw new \Exception("Can not delete at address $address");
        }

        return true;
    }

    protected function writeMetadata($address, $metadata)
    {
        $dir = $this->root . DIRECTORY_SEPARATOR . dirname($address);

        $mdf = $dir . DIRECTORY_SEPARATOR . 'metadata';
        $mdh = fopen($mdf, "a");

        // Get lock on file
        $mdl = false;
        while ($mdl == false) {
            $mdl = flock($mdh, \LOCK_EX);
        }

        $stat = fstat($mdh);

        $jsonMetadata = $address . "=" . json_encode($metadata, \JSON_PRETTY_PRINT) . "\n";

        fwrite($mdh, $jsonMetadata);

        flock($mdh, LOCK_UN);

        fclose($mdh);
    }

    protected function readMetadata($address)
    {
        $dir = $this->root . DIRECTORY_SEPARATOR . dirname($address);

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
            if (strpos($line, $address) === 0) {
                $jsonMetadata = substr($line, strlen($address) + 1);

                $metadata = json_decode($jsonMetadata);
            }
        }

        flock($mdh, LOCK_UN);

        fclose($mdh);

        return $metadata;
    }

    protected function updateMetadata($address, $metadata)
    {
        $dir = $this->root . DIRECTORY_SEPARATOR . dirname($address);

        $mdf = $dir . DIRECTORY_SEPARATOR . 'metadata';
        if (!is_file($mdf)) {
            return $this->writeMetadata($address, $metadata);
        }

        $jsonMetadata = $address . '=' . json_encode($metadata, \JSON_PRETTY_PRINT) . "\n";
        
        $mdh = fopen($mdf, "r+");

        $mdl = false;
        while ($mdl == false) {
            $mdl = flock($mdh, \LOCK_EX);
        }

        $lines = array();
        $found = false;
        while (!feof($mdh)) {
            $line = fgets($mdh);
            if (strpos($line, $address) === 0) {
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

    protected function deleteMetadata($address)
    {
        $dir = $this->root . DIRECTORY_SEPARATOR . dirname($address);

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
            if (strpos($line, $address) === 0) {
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
