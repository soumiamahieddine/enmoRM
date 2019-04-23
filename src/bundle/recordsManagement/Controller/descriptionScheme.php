<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\recordsManagement\Controller;

/**
 * Control of the recordsManagement descriptionClass
 *
 * @package recordsManagement
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class descriptionScheme
{
    protected $descriptionSchemes;

    /**
     * Constructor
     * @param array $descriptionSchemes
     *
     * @return void
     */
    public function __construct($descriptionSchemes)
    {
        $this->descriptionSchemes = get_object_vars(json_decode(json_encode($descriptionSchemes)));
    }

    /**
     * Get the description classes
     *
     * @return array The list of organization's roles
     */
    public function index()
    {
        return $this->descriptionSchemes;
    }

    /**
     * Get the description class
     * @param string $name
     *
     * @return object
     */
    public function read($name)
    {
        if (isset($this->descriptionSchemes[$name])) {
            return $this->descriptionSchemes[$name];
        }
    }

    /**
     * Get the description class properties
     * @param string $name
     *
     * @return array
     */
    public function getDescriptionFields($name = false)
    {
        if (empty($name)) {
            return \laabs::newController('recordsManagement/descriptionField')->index();
        }

        if (isset($this->descriptionSchemes[$name])) {
            $descriptionSchemeConfig = $this->descriptionSchemes[$name];
        } elseif (strpos($name, '/') !== false) {
            $descriptionSchemeConfig = new \stdClass();
            $descriptionSchemeConfig->type = 'php';
            $descriptionSchemeConfig->uri = $name;
        }

        switch ($descriptionSchemeConfig->type) {
            case 'php':
                return $this->getDescriptionFieldsFromPhpClass($descriptionSchemeConfig->uri);
        }

        return $fields;
    }

    protected function getDescriptionFieldsFromPhpClass($name)
    {
        $descriptionScheme = \laabs::getClass($name);
        $fields = [];

        foreach ($descriptionScheme->getProperties() as $descriptionSchemeProperty) {
            $fields[$descriptionSchemeProperty->name] = $this->getDescriptionFieldFromPhpClass($descriptionSchemeProperty);
        }

        return $fields;
    }

    protected function getDescriptionFieldFromPhpClass($schemeProperty)
    {
        $descriptionField = \laabs::newInstance('recordsManagement/descriptionField');
        $descriptionField->name = $schemeProperty->name;

        if (!empty($schemeProperty->summary)) {
            $descriptionField->label = $schemeProperty->summary;
        } else {
            $descriptionField->label = $schemeProperty->name;
        }

        $type = $schemeProperty->getType();
        $descriptionField->default = $schemeProperty->getDefault();

        if (isset($schemeProperty->enumeration)) {
            $descriptionField->enumeration = $schemeProperty->enumeration;
        }

        if (isset($schemeProperty->tags['internal'])) {
            $descriptionField->internal = true;
        }

        if (isset($schemeProperty->tags['readonly'])) {
            $descriptionField->readonly = true;
        }

        switch (true) {
            case substr($type, -2) == '[]':
                $descriptionField->type = 'array';
                $itemType = substr($type, 0, -2);
                $descriptionField->itemType = $this->getPropertyTypeName($itemType);
                break;

            case $type == 'string':
                if (isset($schemeProperty->tags['scheme'])
                    || isset($schemeProperty->tags['uses'])
                    || isset($schemeProperty->enumeration)
                    || isset($schemeProperty->index)) {
                    $descriptionField->type = 'name';
                } else {
                    $descriptionField->type = 'text';
                }
                break;

            case strpos($type, '/') !== false:
                $descriptionField->type = 'object';
                $descriptionField->properties = $this->getDescriptionFieldsFromPhpClass($type);
                break;

            default:
                $descriptionField->type = $this->getPropertyTypeName($type);
        }

        return $descriptionField;
    }

    protected function getPropertyTypeName($type)
    {
        switch (true) {
            case $type == 'string':
                return 'text';
                
            case $type == 'int':
            case $type == 'integer':
            case $type == 'float':
            case $type == 'real':
            case $type == 'double':
                return 'number';

            case $type == 'bool':
            case $type == 'boolean':
                return 'boolean';

            case $type == 'timestamp':
            case $type == 'datetime':
            case $type == 'date':
                return 'date';

            case strpos($type, '/') !== false:
                return '#'.$type;

            default:
                return $type;
        }
    }
}
