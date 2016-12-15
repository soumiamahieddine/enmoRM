<?php
/*
 * Copyright (C) 2016 Maarch
 *
 * This file is part of dependency Fulltext.
 *
 * Bundle documentManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle documentManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with dependency Fulltext. If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\fulltext;
/**
 * The indexed document
 * 
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class Document
{
    /**
     * @var array
     */
    public $fields;

    /**
     * @var array
     */
    public $keywordTypes = [];

    /**
     * Add a field
     * @param string $name
     * @param string $value
     * @param string $type
     */
    public function addField($name, $value, $type=false)
    {
        if (is_array($value)) {
            $value = implode("\n", $value);
        }

        switch (strtolower($type)) {
            case 'date':
                $value = \Laabs::newDate($value)->format('Ymd');
                $this->keywordTypes[$name] = 'date';
                $type = 'Keyword';
                break;

            case 'integer':
            case 'float':
            case 'number':
                if (strpos($value, ".") !== false) {
                    list($int, $dec) = explode('.', $value);
                } elseif (strpos($value, ",") !== false) {
                    list($int, $dec) = explode(',', $value);
                } else {
                    $int = $value;
                    $dec = '';
                }
                $value = str_pad(substr($int, 0, 16), 16, '0', STR_PAD_LEFT) . str_pad(substr($dec, 0, 14), 14, '0');

                $this->keywordTypes[$name] = 'number';
                $type = 'Keyword';
                break;
            
            case 'boolean':
                if (boolval($value)) {
                    $value = "1";
                } else {
                    $value = "0";
                }
                $this->keywordTypes[$name] = 'boolean';
                $type = 'Keyword';
                break;

            case 'name':
                $type = 'Keyword';
                break;

            case 'id':
                $type = 'UnIndexed';
                break;
                
            default:
                $type = 'Text';
        }

        $field = new Field($name, $value, $type);

        $this->fields[] = $field;
    }
}
