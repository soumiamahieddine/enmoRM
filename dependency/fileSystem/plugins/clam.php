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
 * Class to launch clam anti-virus tool
 *
 * @package FileSystem
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class clam
{

    protected $scan;

    protected $database;

    /**
     * Construct environment
     * @param string $clamScan The path to clam executable
     * @param string $clamDb   The path to clam database
     * 
     * @return void
     */
    public function __construct($clamScan, $clamDb)
    {
        $this->clamscan = $clamScan;

        $this->database = $clamDb;
    }

    /**
     * Check directory or file
     * @param mixed   $path      The path to the directory to check or a filename or a file list
     * @param boolean $recursive Recurse to sub-directories
     * 
     * @return boolean
     */
    public function scan($path, $recursive=true)
    {
        $tokens = array();
        $tokens[] = '"'. $this->clamscan . '"';
        $tokens[] = '--database "' . $this->database. '"';
        if ($recursive) {
            $tokens[] = '--recursive';
        }

        if (is_array($path)) {
            $tmpfilename = tempnam(sys_get_temp_dir(), "tmp");
            $tmpfile = fopen($tmpfilename, "w+");
            foreach ($path as $filename) {
                fwrite($tmpfile, $filename . PHP_EOL);
            }
            fclose($tmpfile);

            $tokens[] = '--file-list="' . $tmpfilename . '"';
        } else {
            $tokens[] = '"' . $path . '"';
        }
        

        $command = implode(' ', $tokens);

        $output = array();
        $return = null;

        exec($command, $output, $return);

        if ($return !== 0) {
            $exception = new \dependency\fileSystem\Exception("Failed to check viruses: execution error.");
            $exception->errors = $output;
            
            throw $exception;
        }

        $results = array();

        foreach ($output as $i => $line) {
            if ($i==0 || empty($line)) {
                // loading virus database
                continue;
            }
            if ($line[0] == "-") {
                break;
            }

            $sep = strrpos($line, ":");
            $filename = substr($line, 0, $sep);
            switch (substr($line, $sep+2)) {
                case 'OK':
                    $result = 0;
                    break;

                default:
                    $result = 1;
                    break;
            }
            $results[$filename] = $result;          

        }
        
        return $results;
    }

    /**
     * Update database
     */
    public function update()
    {
        $maxExecutionTime = ini_set('max_execution_time', 120);

        $tokens = array();
        $tokens[] = '"'. $this->freshclam . '"';
        $tokens[] = '--datadir="' . $this->database . '"';

        $tokens[] = '--config-file="' . $this->config . '"';

        $command = implode(' ', $tokens);

        $output = array();
        $return = null;

        exec($command, $output, $return);

        if ($return !== 0) {
            throw new \dependency\fileSystem\Exception("Failed to update virus database: execution error.");
        }

        ini_set('max_execution_time', $maxExecutionTime);
    }

} // END class jhove 
