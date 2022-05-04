<?php
/*
 * Copyright (C) 2021 Maarch
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
 * @author Jerome Boucher <jerome.boucher@maarch.org>
 */

class Tesseract implements \dependency\fileSystem\FullTextInterface
{
    protected $executable;

    public function __construct($tesseractExecutable = false)
    {
        if (!$tesseractExecutable) {
            switch (DIRECTORY_SEPARATOR) {
                // Windows installation
                case '\\':
                    $this->executable = 'C:\Program Files (x86)\Tesseract-OCR\Tesseract.exe';
                    break;
                case "/":
                default:
                    $this->executable = "tesseract";
            }
        } else {
            $this->executable = $zipExecutable;
        }
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public function getText($filename, $options = null)
    {
        return $this->run($filename, $options);
    }

    /**
     * @param string $fileName
     * @param string $options
     *
     * @return string
     * @throws \Exception
     */
    protected function run($fileName, $options = null)
    {
        $languages = null;
        if (!is_null($options)) {
            $languages = implode('+', $options['languages']);
        }
        $command = $this->executable . ' ' . $fileName . ' stdout -l ' . $languages;
        $return = null;
        exec($command, $output, $return);

        if ($return !== 0) {
            $exception = new \dependency\fileSystem\Exception("Failed to load tesseract on the resource");
            $exception->errors = $output;

            throw $exception;
        }

        $output = implode("\n", $output);

        return $output;
    }
}
