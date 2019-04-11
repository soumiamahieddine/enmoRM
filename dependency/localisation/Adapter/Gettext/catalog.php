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

namespace dependency\localisation\Adapter\Gettext;

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
     **/
    public function __construct($catalogUri, $lang)
    {
        ini_set('auto_detect_line_endings', true);

        // Load default catalog
        $defaultCatalogFile = LAABS_PRESENTATION.DIRECTORY_SEPARATOR.\laabs::getPresentation().DIRECTORY_SEPARATOR;
        $defaultCatalogFile .= LAABS_RESOURCE.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.'messages.po';

        if (file_exists($defaultCatalogFile)) {
            $this->loadFile($defaultCatalogFile);
        }

        // Load requested catalog
        $domain = strtok($catalogUri, LAABS_URI_SEPARATOR);
        $catalog = strtok(LAABS_URI_SEPARATOR);

        /* Search for resources on extensions */
        $catalogFile = LAABS_PRESENTATION.DIRECTORY_SEPARATOR.\laabs::getPresentation().DIRECTORY_SEPARATOR;
        $catalogFile .= LAABS_RESOURCE.DIRECTORY_SEPARATOR.'locale'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$domain.DIRECTORY_SEPARATOR.$catalog.".po";

        $catalogFiles = \core\Reflection\Extensions::extendedPath($catalogFile, false);
        if (count($catalogFiles) == 0) {
            return;
            throw new \dependency\localisation\Exception("Catalog $catalogUri not found for language $lang");
        }

        foreach (array_reverse($catalogFiles) as $catalogFile) {
            $this->loadFile($catalogFile);
        }

        ini_set('auto_detect_line_endings', false);
    }

    /**
     * Load a source file for translation catalo
     * @param string $catalogFile The uri to a ressource to load
     *
     * @return void
     **/
    public function loadFile($catalogFile)
    {
        $handle = fopen($catalogFile, 'r');

        $po = new PO();
        $block = [];
        while (!feof($handle)) {
            $line  = trim(fgets($handle));
            if (!empty($line)) {
                if ($line[0] == '"' && count($po) == 0) {
                    $po->header[] = substr($line, 1, -1);
                } else {
                    $block[] = $line;
                }
            } elseif (!empty($block)) {
                $po[] = $this->parseMsg($block);
                $block = [];
            }
        }
        if (!empty($line)) {
            $po[] = $this->parseMsg($block);
        }

        return $po;
    }

    protected function parseMsg($block)
    {
        $poMsg = new POMsg();

        while (($line = current($block)) !== false) {
            if (!empty($line)) {
                if ($line[0] !=  '"') {
                    $key  = strtok($line, ' ');
                    $data  = ($tail = substr(strtok(''), 1, -1)) ? $tail : null;
                    switch ($key) {
                        case '#':
                            $poMsg->header[] = trim(substr($line, 1));
                            break;

                        case 'msgid':
                            $poMsg->msgid = $data;
                            break;

                        case 'msgstr':
                            $poMsg->msgstr[] = $data;
                            break;

                        case 'msgctxt':
                            $poMsg->msgctxt = $data;
                            break;

                        case 'msgid_plural':
                            $poMsg->msgid_plural = $data;
                            break;

                        default:
                            if (preg_match("/\w+\[\d+\]/", $key)) {
                                $poMsg->msgstr[] = $data;
                            }
                    }
                } else {
                    $data  = substr($line, 1, -1);
                    switch ($key) {
                        case 'msgid':
                            $poMsg->msgid .= PHP_EOL.$data;
                            break;

                        case 'msgstr':
                            if (count($poMsg->msgstr)) {
                                $poMsg->msgstr[count($poMsg->msgstr)-1] .= PHP_EOL.$data;
                            }
                            break;
                    }
                }
            }

            next($block);
        }
        if (isset($poMsg->msgctxt)) {
            $this->messages[$poMsg->msgctxt."/".$poMsg->msgid] = $poMsg;
        } else {
            $this->messages[$poMsg->msgid] = $poMsg;
        }

        return $poMsg;
    }
}
