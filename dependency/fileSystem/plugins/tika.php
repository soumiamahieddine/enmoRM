<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of FDI.
 *
 * FDI is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * FDI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with FDI. If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\fileSystem\plugins;

/**
 * The metadata and text extraction tool
 * 
 * @author Prosper DE LAURE Maarch <prosper.delaure@maarch.org>
 */

class tika implements \dependency\fileSystem\ExctractInterface {

    protected $tikaJarFile;

    /**
     * Constructor
     * @param string $tikaJarFile The tikaJarFile
     */
 	public function __construct($tikaJarFile)
    {
		$this->tikaJarFile = $tikaJarFile;
    }

    /**
     * @param $filename
     * @return string
     */
    public function getHTML($filename){
        return $this->run("--html", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public function getJson($filename){
        return $this->run("--json", $filename);
    }
    
    /**
     * @param string $filename
     * @return string
     */
    public function getText($filename) {
        return $this->run("--text", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public function getMetadata($filename){
        return $this->run("--metadata", $filename);
    }

	/**
     * @param string $option
     * @param string $fileName
     *
     * @return string
     * @throws \Exception
     */
    protected function run($option, $fileName)
    {
        $command = 'java -jar ' . $this->tikaJarFile . ' ' . $option . ' "' . $fileName . '"';

        $return = null;

        exec($command, $output, $return);

        if ($return !== 0) {
            $exception = new \dependency\fileSystem\Exception("Failed to load apache tika on the resource");
            $exception->errors = $output;

            throw $exception;
            
        }
        
        $output = implode("\n", $output);
        $output = str_replace("\x92", "'", $output);
        $output = str_replace("\x9C", "oe", $output);
        $output = utf8_encode($output);

        return $output;
    }
    
}
