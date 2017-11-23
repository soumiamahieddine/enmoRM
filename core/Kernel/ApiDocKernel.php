<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of Laabs.
 *
 * Laabs is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Laabs is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Laabs.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace Core\Kernel;
/**
 * Kernel for API discovery and documentation
 * 
 * @package Laabs
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class ApiDocKernel
    extends AbstractKernel
{
    /**
     * @var array The resources
     */
    public static $resources = [];

    /**
     * @var array The types
     */
    public static $types = [];

    /**
     * Run
     */
    public static function run()
    {
        /*foreach (\laabs::bundles() as $reflectionBundle) {   
            static::getResources($reflectionBundle);
        }*/
        static::getResources(\laabs::bundle('auth'));        

        $return = [
            'resources'=>static::$resources, 
            'types'=>static::$types
        ];

        $body = json_encode($return, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);

        header('Content-Type: application/json');
        //header('Content-Length: '.strlen($body));
        echo $body;
    }

    protected static function getResources($reflectionBundle)
    {
        foreach ($reflectionBundle->getApis() as $reflectionApi) {
            static::$resources[$reflectionApi->getName()] = static::getResource($reflectionApi);
        }
    }

    protected static function getResource($reflectionApi)
    {
        $resource = [];

        foreach ($reflectionApi->getPaths() as $reflectionPath) {
            $resource[$reflectionPath->name] = static::getPath($reflectionPath);
        }

        return $resource;
    }

    protected static function getPath($reflectionPath)
    {
        $route = [];

        $method = strtoupper($reflectionPath->method);
        switch ($reflectionPath->method) {
            case 'create':
                $route['method'] = 'POST';
                break;

            case 'read':
                $route['method'] = 'GET';
                break;

            case 'upate':
                $method = 'PUT';
                break;
        }

        $route['path'] = $reflectionPath->path;

        if (!empty($reflectionPath->variables)) {
            $route['variables'] = array_keys($reflectionPath->variables);
        }
        if (!empty($reflectionParameters = $reflectionPath->getParameters())) {
            foreach ($reflectionParameters as $reflectionParameter) {
                $route['parameters'][$reflectionParameter->getName()] = static::getParameter($reflectionParameter);
            }
        }
        if (!empty($returntype = $reflectionPath->getReturnType())) {
            $route['response'] = $returntype;
        }

        return $route;
    }

    protected static function getParameter($reflectionParameter)
    {
        $parameter = [
            'name' => $reflectionParameter->name,
            'type' => $reflectionParameter->type,
        ];

        if (!empty($reflectionParameter->type)) {
            static::getDataType($reflectionParameter->type);
        }
 
        return $parameter;
    }

    protected static function getDataType($typename)
    {
        if (substr($typename, -2) == '[]') {
            $typename = substr($typename, 0, -2);
        }

        if (\laabs::isBuiltInType($typename) || isset(static::$types[$typename])) {
            return;
        }

        if (strpos($typename, '/') !== false) {
            try {
                $reflectionType = \laabs::getMessage($typename);
            } catch (\Exception $e) {
                $reflectionType = \laabs::getClass($typename);
            }
            
            static::$types[$typename] = static::getComplexType($reflectionType);
        } else {
            static::$types[$typename] = static::getSimpleType($typename);
        }
    }

    protected static function getComplexType($reflectionType)
    {
        $type = [];
        $type['name'] = $reflectionType->name;
        
        foreach ($reflectionType->getProperties() as $reflectionProperty) {
            $type['properties'][$reflectionProperty->getName()] = static::getProperty($reflectionProperty);
        }

        return $type;
    }

    protected static function getSimpleType($name)
    {
        $type = [];
        $type['name'] = $name;
        $type['type'] = 'string';
        $type['pattern'] = "";

        return $type;
    }

    protected static function getProperty($reflectionProperty)
    {
        $property = [];
        $property['name'] = $reflectionProperty->name;
        $property['type'] = $reflectionProperty->type;
        
        if (!empty($reflectionParameter->type)) {
            static::getDataType($reflectionProperty->type);
        }

        return $property;
    }
}
