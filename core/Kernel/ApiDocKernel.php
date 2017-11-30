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
     * @var array The open api description
     */
    public static $api;

    /**
     * Run
     */
    public static function run()
    {
        static::$api = new \StdClass();

        static::$api->openapi = "3.0.0";
        static::$api->servers = [
            'url' => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'],
        ];

        static::$api->paths = [];
        static::$api->definitions = [];

        foreach (\laabs::bundles() as $reflectionBundle) {   
            foreach ($reflectionBundle->getApis() as $reflectionApi) {
                foreach ($reflectionApi->getPaths() as $reflectionPath) {
                    static::getPath($reflectionPath);
                }
            }
        }

        $body = json_encode(static::$api, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);

        header('Content-Type: application/json');

        echo $body;
    }

    protected static function getPath($reflectionPath)
    {
        if (!isset(static::$api->paths[$reflectionPath->path])) {
            static::$api->paths[$reflectionPath->path] = [];
        }

        $path = new \StdClass();

        $method = static::getMethod($reflectionPath);
        
        static::getDescription($reflectionPath, $path);

        if (!empty($reflectionPath->variables)) {
            foreach ($reflectionPath->variables as $name => $def) {
                $parameter = new \StdClass();
                $parameter->name = $name;
                $parameter->in = 'path';

                $path->parameters[] = $parameter;
            }
        }

        if (!empty($reflectionParameters = $reflectionPath->getParameters())) {
            switch ($method) {
                case 'get':
                case 'delete':
                    $in = 'query';
                    
                    break;
            
                case 'post':
                case 'put':
                default:
                    $in = 'body';
            }

            foreach ($reflectionParameters as $reflectionParameter) {
                static::getParameter($reflectionParameter, $in, $path);
            }
        }

        /*if (!empty($returntype = $reflectionPath->getReturnType())) {
            $path['response'] = static::getDataType($returntype);
        }*/

        try {
            if (isset($reflectionPath->action)) {
                $actionRouter = new \core\Route\ActionRouter($reflectionPath->action);
            } else {
                $actionRouter = new \core\Route\ActionRouter($reflectionPath->getName());
            }

            static::getResponses($actionRouter->action, $path);
        } catch (\Exception $e) {

        }

        static::$api->paths[$reflectionPath->path][$method] = $path;
    }

    protected static function getMethod($reflectionPath)
    {
        $method = strtolower($reflectionPath->method);
        switch ($reflectionPath->method) {
            case 'create':
                return 'post';

            case 'read':
                return 'get';

            case 'update':
                return 'put';

            default :
                return $method;
        }
    }

    protected static function getParameter($reflectionParameter, $in, $path)
    {
        $parameter = new \StdClass();
        $parameter->name = $reflectionParameter->name;
        if ($doc = $reflectionParameter->doc) {
            $parameter->description = $reflectionParameter->doc;
        }
        $parameter->in = $in;

        if (!$reflectionParameter->isDefaultValueAvailable() || !$reflectionParameter->allowsNull()) {
            $parameter->required = true;
        }

        if (!empty($reflectionParameter->type)) {
            $parameter->schema = new \StdClass();

            static::getDataType($reflectionParameter->type, $parameter->schema);

            if (!count(get_object_vars($parameter->schema))) {
                unset($parameter->schema);
            }
        }
 
        $path->parameters[] = $parameter;
    }


    protected static function getResponses($reflectionAction, $path)
    {
        $path->responses = [];

        if ($returnType = $reflectionAction->getReturnType()) {
            $response = new \StdClass();
            $response->schema = new \StdClass();

            static::getDataType($returnType, $response->schema);

            if (!count(get_object_vars($response->schema))) {
                unset($response->schema);
            }
            
            $path->responses['200'] = $response;
        }
    }

    protected static function getDataType($typename, $component)
    {
        if (substr($typename, -2) == '[]') {
            $component->type = 'array';
            $component->items = new \StdClass();
            static::getDataType(substr($typename, 0, -2), $component->items);
        } else {
            switch (true) {
                case \laabs::isBuiltInType($typename):
                    $component->type = $typename;
                    break;
           
                case ($basetype = \laabs::getPhpType($typename)):
                    $component->type = $basetype;
                    static::getPattern($typename, $component);
                    break;

                case strpos($typename, '/') !== false:
                    $component->{'$ref'} = '#/definitions/'.$typename;
                    static::getTypeRef($typename);
            }
        }
    }

    protected static function getTypeRef($typename)
    {
        list ($bundle, $localname) = explode('/', $typename);

        if (!isset(static::$api->definitions[$bundle])) {
            static::$api->definitions[$bundle] = [];
        }
       
        if (!isset(static::$api->definitions[$bundle][$localname])) {
            try {
                $reflectionType = \laabs::getMessage($typename);
            } catch (\Exception $e) {
                $reflectionType = \laabs::getClass($typename);
            }

            static::$api->definitions[$bundle][$localname] = new \StdClass();

            static::getComplexType($reflectionType, static::$api->definitions[$bundle][$localname]);
        }        
    }

    protected static function getComplexType($reflectionType, $type)
    {
        static::getDescription($reflectionType, $type);
        
        $type->required = [];

        foreach ($reflectionType->getProperties() as $reflectionProperty) {
            if (!$reflectionProperty->isEmptyable() || !$reflectionProperty->isNullable()) {
                $type->required[] = $reflectionProperty->name;
            }

            $property = new \StdClass();
            static::getDescription($reflectionProperty, $property);
            
            
            if (!empty($reflectionProperty->type)) {
                static::getDataType($reflectionProperty->type, $property);
            }

            $type->properties[$reflectionProperty->getName()] = $property;
        }

        return $type;
    }

    protected static function getPattern($name, $type)
    {
        switch ($name) {
            case 'id':
                $type->pattern = '[a-Z_][a-Z0-9\-_]*';
                break;

            case 'timestamp':
            case 'datetime':
                $type->pattern = '(?![+-]?\d{4,5}-?(?:\d{2}|W\d{2})T)(?:|(\d{4}|[+-]\d{5})-?(?:|(0\d|1[0-2])(?:|-?([0-2]\d|3[0-1]))|([0-2]\d{2}|3[0-5]\d|36[0-6])|W([0-4]\d|5[0-3])(?:|-?([1-7])))(?:(?!\d)|T(?=\d)))(?:|([01]\d|2[0-4])(?:|:?([0-5]\d)(?:|:?([0-5]\d)(?:|\.(\d{3})))(?:|[zZ]|([+-](?:[01]\d|2[0-4]))(?:|:?([0-5]\d)))))';
                break;

            case 'qname':
                $type->pattern = '[a-Z_][a-Z0-9\-_]*\/[a-Z_][a-Z0-9\-_]*';
                break;
            
            case 'date':
                $type->pattern = '\d{4}\-(0[1-9]|1[012])\-(0[1-9]|[12][0-9]|3[01])';
                break;

            case 'duration':
                $type->pattern = '(\-\+)?P(\d+Y)?(\d+M)?(\d+D)?(T(\d+H)?(\d+M)?(\d+S)?)?';
                break;
        }
    }

    protected static function getDescription($reflection, $component)
    {
        if (isset($reflection->summary)) {
            $component->description = trim($reflection->summary.' '.$reflection->description);
        }
    }
}
