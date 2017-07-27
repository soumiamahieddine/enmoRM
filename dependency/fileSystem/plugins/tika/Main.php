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

/**
 * The metadata and text extraction tool
 * 
 * @author Prosper DE LAURE Maarch <prosper.delaure@maarch.org>
 */

class Tika {

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
        return self::run("--html", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public function getJson($filename){
        return self::run("--json", $filename);
    }
    
    /**
     * @param string $filename
     * @return string
     */
    public function getText($filename) {
        return self::run("--text", $filename);
    }

    /**
     * @param $filename
     * @return string
     */
    public function getMetadata($filename){
        return self::run("--metadata", $filename);
    }

	/**
     * @param string $option
     * @param string $fileName
     *
     * @return string
     * @throws \Exception
     */
    protected static function run($option, $fileName)
    {
        
        $shellCommand = 'java -jar ' . $this->tikaJarFile . ' ' . $option . ' "' . $fileName . '"';
        $process = new Process($shellCommand);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new \Exception($process->getErrorOutput());
        }

        return $process->getOutput();
    }
    
}
