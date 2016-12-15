<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency localisation.
 *
 * Dependency localisation is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency localisation is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency localisation.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\localisation\Adapter\Csv;
/**
 * Language message catalog for CSV adapter
 *
 * @package Localisation
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */ 
class catalog
{

    public $pluralFormCount;
    public $pluralFormAssert;
    public $charset = 'UTF-8';

    public $messages = array();

    /**
     * Constructor
     * @param string $catalogUri The uri of catalog
     * @param string $lang       The language code to load
     *
     * @return void
     * @author 
     **/
    public function __construct($catalogUri, $lang)
    {
        ini_set('auto_detect_line_endings', true);

        // Load default catalog
        $defaultCatalogFile = LAABS_PRESENTATION . DIRECTORY_SEPARATOR
            . \laabs::getPresentation() . DIRECTORY_SEPARATOR
            . LAABS_RESOURCE . DIRECTORY_SEPARATOR 
            . 'locale' . DIRECTORY_SEPARATOR 
            . $lang . DIRECTORY_SEPARATOR . 'messages.csv';

        if (file_exists($defaultCatalogFile)) {
            $this->loadFile($defaultCatalogFile);
        }

        // Load requested catalog
        $domain = strtok($catalogUri, LAABS_URI_SEPARATOR);
        $catalog = strtok(LAABS_URI_SEPARATOR);

        /* Search for resources on extensions */
        $catalogFile = LAABS_PRESENTATION . DIRECTORY_SEPARATOR
            . \laabs::getPresentation() . DIRECTORY_SEPARATOR
            . LAABS_RESOURCE . DIRECTORY_SEPARATOR 
            . 'locale' . DIRECTORY_SEPARATOR 
            . $lang . DIRECTORY_SEPARATOR
            . $domain . DIRECTORY_SEPARATOR 
            . $catalog . ".csv";

        $catalogFiles = \core\Reflection\Extensions::extendedPath($catalogFile, false);

        if (count($catalogFiles) == 0) {
            return;
            throw new \dependency\localisation\Exception("Catalog $catalogUri not found for language $lang");
        }

        foreach ($catalogFiles as $catalogFile) {
            $this->loadFile($catalogFile);
        }
        

        ini_set('auto_detect_line_endings', false);
    }

    /**
     * Load a source file for translation catalo
     * @param string $catalogFile The uri to a ressource to load
     *
     * @return void
     * @author 
     **/
    public function loadFile($catalogFile) 
    {
        $handle = fopen($catalogFile, 'r');

        while ( ($data = fgetcsv($handle) ) !== false ) {
            if (count($data) == 1) {
                $headerName = strtok($data[0], ":");
                $headerValue = trim(strtok(""));
                switch (strtolower($headerName)) {
                    case 'plural-forms':
                        $vars = \laabs\explode(";", $headerValue);
                        $this->pluralFormCount = \laabs\explode("=", trim($vars[0]))[1];
                        $this->pluralFormAssert = \laabs\explode("=", trim($vars[1]))[1];
                        break;
                    case 'charset':
                        $this->charset = $headerValue;
                }

            } else {
                $msg = new msg($data);
                
                if ($msg->msgctxt) {
                    $qmsgid = (string) $msg->msgctxt . "/" . (string) $msg->msgid;
                } else {
                    $qmsgid = (string) $msg->msgid;
                }

                $qmsgid = preg_replace('/[^[:print:]]/', '', $qmsgid);
                                
                if (!array_key_exists($qmsgid, $this->messages)) {
                    $this->messages[$qmsgid] = $msg;
                } 
            }

            
        }
    }

}