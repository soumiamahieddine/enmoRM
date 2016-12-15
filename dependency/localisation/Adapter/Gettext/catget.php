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
 * Stringset is a simple class for storing a gettext catalog.
 */
class catget
{
    /**
     * The collection of strings and their translations
     * @var array[]
     */
    private $set;
    public function __construct() {
        $this->set = array();
    }
    /**
     * Size of the collection
     * @return integer
     */
    public function size() {
        return count($this->set);
    }
    /**
     * Add an entry to the catalog.
     * @param array $entry
     * @return void
     */
    public function add(array $entry) {
        if (!isset($entry['msgid'])) {
            throw new Exception("Invalid entry: missing msgid");
        }
        $id = $entry['msgid'];
        $plural_id = isset($entry['msgid_plural']) ? $entry['msgid_plural'] : null;
        $context = isset($entry['msgctxt']) ? $entry['msgctxt'] : null;
        $flags = isset($entry['flags']) ? $entry['flags'] : array();
        $strings = array();
        foreach ($entry as $key => $value) {
            if (substr($key, 0, 6) === 'msgstr') {
                if (is_array($value)) {
                    $strings = array_merge($strings, $value);
                } else {
                    $strings[] = $value;
                }
            }
        }
        if (count($strings) === 0) {
            throw new Exception("Invalid entry: missing msgstr");
        }
        $this->set[] = array(
            'id' => $id,
            'plural' => $plural_id,
            'context' => $context,
            'flags' => $flags,
            'strings' => $strings
        );
    }
    /**
     * Sort the entries in lexical order.
     * @return void
     */
    public function sort() {
        usort($this->set, function ($first, $second) {
            $ids = strcmp($first['id'], $second['id']);
            if ($ids === 0) {
                if ($first['context'] === null && $second['context'] === null) {
                    return 0;
                } else if ($first['context'] === null) {
                    return -1;
                } else if ($second['context'] === null) {
                    return 1;
                } else {
                    return strcmp($first['context'], $second['context']);
                }
            } else {
                return $ids;
            }
        });
    }
    /**
     * Retrieve the entire catalog.
     * @return array[]
     */
    public function catalog() {
        return $this->set;
    }
    /**
     * Retrieve an item at the given index.
     * @param integer $index
     * @return array
     */
    public function item($index) {
        return $this->set[$index];
    }
}
