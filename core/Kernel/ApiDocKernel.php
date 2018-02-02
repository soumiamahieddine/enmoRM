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
        ini_set('max_execution_time', 120);

        static::$api = $api = new \StdClass();

        $api->swagger = "2.0";
        
        $api->info = static::getInfo();
        
        $api->host = $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
        
        $api->basePath = '/';
        
        $api->schemes = ['http', 'https'];
        
        $api->consumes = ['application/json'];
        
        $api->produces = ['application/json'];
        
        // Get Paths + definitions + parameters + responses
        static::getPaths();
        
        //$api->definitions = [];
        
        //$api->parameters = [];
        
        //$api->responses = [];
        
        $api->securityDefinitions = static::getSecurityDefinitions();
        
        //$api->security = [];
        
        $api->tags = [];
        
        $api->externalDocs = static::getExternalDocs();       

        $body = json_encode(static::$api, JSON_PRETTY_PRINT + JSON_UNESCAPED_SLASHES);

        header('Content-Type: application/json; charset=utf-8');
        header('Content-Length: '.strlen($body));

        echo $body;
    }

    protected static function getInfo()
    {
        $info = new \StdClass();
        $info->title = 'Maarch RM';
        $info->version = '2.1';
        $info->contact = $contact = new \StdClass();
            $contact->name = 'Cyril VAZQUEZ';
            $contact->email = 'cyril.vazquez@maarch.org';
            $contact->url = 'http://maarchrm.com';
        $info->license = $license = new \StdClass();
            $license->name = 'GNU Lesser General Public License V3';
            $license->url = 'http://www.gnu.org/licenses/lgpl.txt';

        return $info;
    }

    protected static function getSecurityDefinitions()
    {
        $securityDefinitions = [];

        $securityDefinitions['laabs'] = $securityScheme = new \StdClass();
        $securityScheme->type = 'apiKey';
        $securityScheme->description = "A service token provided by the administrator and send as a cookie named 'LAABS-AUTH'";
        $securityScheme->name = 'Cookie[LAABS-AUTH]';
        $securityScheme->in = 'header';

        return $securityDefinitions;
    }

    protected static function getExternalDocs()
    {
        $externalDocs = new \StdClass();
        $externalDocs->description = 'The Maarch RM documentation Git repository';
        $externalDocs->url = 'http://labs.maarch.org/maarch/maarchRM.doc/tree/2.1';

        return $externalDocs;
    }

    protected static function getPaths()
    {
        $parts = explode('/', $_SERVER['SCRIPT_NAME']);
        array_shift($parts);
        array_shift($parts);

        if (empty($parts)) {
            foreach (\laabs::bundles() as $reflectionBundle) {   
                static::getBundlePaths($reflectionBundle);
            }
        } else {
            $bundle = array_shift($parts);

            $reflectionBundle = \laabs::bundle($bundle);
            if (empty($parts)) {
                static::getBundlePaths($reflectionBundle);
            } else {
                $api = array_shift($parts);

                $reflectionApi = $reflectionBundle->getApi($api);

                static::getApiPaths($reflectionApi);
            }
        }
    }

    protected static function getBundlePaths($reflectionBundle)
    {
        foreach ($reflectionBundle->getApis() as $reflectionApi) {
            static::getApiPaths($reflectionApi);
        }
    }

    protected static function getApiPaths($reflectionApi)
    {
        foreach ($reflectionApi->getPaths() as $reflectionPath) {
            
            $method = static::getMethod($reflectionPath);

            if (!$method) {
                continue;
            }

            if (!isset(static::$api->paths['/'.$reflectionPath->path])) {
                static::$api->paths['/'.$reflectionPath->path] = [];
            }

            static::$api->paths['/'.$reflectionPath->path][$method] = static::getOperation($reflectionPath);
        }
    }

    protected static function getOperation($reflectionPath)
    {
        $operation = new \StdClass();
        
        //$operation->tags = [];
        
        static::getDescription($reflectionPath, $operation);
        
        //$operation->externalDocs = [];
        
        $operation->operationId = $reflectionPath->domain.'/'.$reflectionPath->interface.'/'.$reflectionPath->name;
        
        $operation->parameters = static::getOperationParameters($reflectionPath);

        $operation->responses = [];
        if ($response = static::getOperationResponse($reflectionPath)) {
            $operation->responses['200'] = $response;
        } else {
            $operation->responses['default'] = $response = new \StdClass();
            $response->description = "No documentation available";
        }

        return $operation;
    }

    protected static function getMethod($reflectionPath)
    {
        $method = strtolower($reflectionPath->method);
        switch ($method) {
            case 'create':
                return 'post';

            case 'read':
                return 'get';

            case 'update':
                return 'put';

            case 'delete' :
                return 'delete';
        }
    }

    protected static function getOperationParameters($reflectionPath)
    {
        $parameters = [];

        $method = static::getMethod($reflectionPath);

        if (!empty($reflectionPath->variables)) {
            $parameters = static::getPathParameters($reflectionPath);
        }

        if (!empty($reflectionParameters = $reflectionPath->getParameters())) {
            switch ($method) {
                case 'get':
                case 'delete':
                    foreach ($reflectionParameters as $reflectionParameter) {
                        $parameters[] = static::getParameter($reflectionParameter);
                    }
                    break;
            
                case 'post':
                case 'put':
                default:
                    $parameters[] = static::getBodyDefinition($reflectionParameters);
            }
        }

        return $parameters;
    }

    protected static function getPathParameters($reflectionPath)
    {
        $parameters = [];

        foreach ($reflectionPath->variables as $name => $def) {
            $parameter = new \StdClass();
            $parameter->name = $name;
            $parameter->in = 'path';
            $parameter->required = true;
            $parameter->type = 'string';

            $parameters[] = $parameter;
        }
        
        return $parameters;
    }

    protected static function getParameter($reflectionParameter)
    {
        $parameter = new \StdClass();
        
        $parameter->name = $reflectionParameter->name;
        
        $parameter->in = 'query';

        static::getDescription($reflectionParameter, $parameter);
        
        if (!$reflectionParameter->isDefaultValueAvailable() || !$reflectionParameter->allowsNull()) {
            $parameter->required = true;
        }

        static::getSimpleType($reflectionParameter->type, $parameter);

        return $parameter;
    }

    protected static function getBodyDefinition($reflectionParameters)
    {
        $parameter = new \StdClass();
        $parameter->name = 'entity';
        $parameter->in = 'body';
        $parameter->required = true;

        $parameter->schema = $schema = new \StdClass();
            
        $schema->type = 'object';    
        
        foreach ($reflectionParameters as $reflectionParameter) {
            if (!$reflectionParameter->isOptional() && !$reflectionParameter->isDefaultValueAvailable()) {
                $schema->required[] = $reflectionParameter->name;
            }
            $schema->properties[$reflectionParameter->getName()] = static::getBodyParameter($reflectionParameter);
        }
    
        return $parameter;
    }

    protected static function getBodyParameter($reflectionParameter)
    {
        $property = new \StdClass();
        static::getDescription($reflectionParameter, $property);
        
        if (!empty($reflectionParameter->type)) {
            static::getDataType($reflectionParameter->type, $property);
        }

        return $property;
    }


    protected static function getOperationResponse($reflectionPath)
    {
        try {
            if (isset($reflectionPath->action)) {
                $actionRouter = new \core\Route\ActionRouter($reflectionPath->action);
            } else {
                $actionRouter = new \core\Route\ActionRouter($reflectionPath->getName());
            }

            $reflectionAction = $actionRouter->action;

            if ($returnType = $reflectionAction->getReturnType()) {
                $response = new \StdClass();

                $response->description = $reflectionAction->description;
                
                $response->schema = new \StdClass();

                static::getDataType($returnType, $response->schema);

                if (count(get_object_vars($response->schema))) {
                    return $response;
                }
            }
        } catch (\Exception $e) {

        }
    }

    protected static function getDataType($typename, $schema)
    {
        if (substr($typename, -2) == '[]') {
            $schema->type = 'array';
            $schema->items = new \StdClass();
            static::getDataType(substr($typename, 0, -2), $schema->items);

            return;
        }

        if (strpos($typename, '/') !== false) {
            $schema->{'$ref'} = '#/definitions/'.$typename;
            static::getTypeRef($typename);

            return;
        } 

        static::getSimpleType($typename, $schema);
    }

    protected static function getSimpleType($typename, $schema)
    {
        switch ($typename) {
            case 'integer':
            case 'int':
                $schema->type = 'integer';
                break;

            case 'float':
                $schema->type = 'number';
                $schema->format = 'float';
                break;

            case 'double':
                $schema->type = 'number';
                $schema->format = 'double';
                break;

            case 'boolean':
            case 'bool':
                $schema->type = 'boolean';
                break;

            case 'date':
                $schema->type = 'string';
                $schema->format = 'date';
                break;

            case 'datetime':
            case 'timestamp':
                $schema->type = 'string';
                $schema->format = 'date-time';
                break;

            case 'id':
                $schema->type = 'string';
                $schema->pattern = '^[a-z_][a-z0-9\-_]*$';
                break;

            case 'qname':
                $schema->type = 'string';
                $schema->pattern = '^[a-z_][a-z0-9\-_]*\/[a-z_][a-z0-9\-_]*$';
                break;

            case 'duration':
                $schema->type = 'string';
                $schema->pattern = '^(\-\+)?P(\d+Y)?(\d+M)?(\d+D)?(T(\d+H)?(\d+M)?(\d+S)?)?$';
                break;

            case 'array':
            case 'string':
            case 'object':
                $schema->type = $typename;
                break;

            default:
                $schema->type = 'string';
        }
    }

    protected static function getTypeRef($typename)
    {
        list ($bundle, $localname) = explode('/', $typename);

        if (!isset(static::$api->definitions)) {
            static::$api->definitions = [];
        }

        if (!isset(static::$api->definitions[$bundle])) {
            static::$api->definitions[$bundle] = $dict = new \StdClass();

            $dict->type = 'object';
            $dict->properties = [];
        }
       
        if (!isset(static::$api->definitions[$bundle]->properties[$localname])) {
            try {
                $reflectionType = \laabs::getMessage($typename);
            } catch (\Exception $e) {
                $reflectionType = \laabs::getClass($typename);
            }

            static::$api->definitions[$bundle]->properties[$localname] = $type = new \StdClass();

            static::getDescription($reflectionType, $type);
        
            foreach ($reflectionType->getProperties() as $reflectionProperty) {
                if (!$reflectionProperty->isEmptyable() || !$reflectionProperty->isNullable()) {
                    $type->required[] = $reflectionProperty->name;
                }
                $type->properties[$reflectionProperty->getName()] = static::getTypeProperty($reflectionProperty);
            }
        }
    }

    protected static function getTypeProperty($reflectionProperty)
    {
        $property = new \StdClass();
        static::getDescription($reflectionProperty, $property);
        
        if (!empty($reflectionProperty->type)) {
            static::getDataType($reflectionProperty->type, $property);
        }

        if (!empty($reflectionProperty->enumeration)) {
            $property->enum = $reflectionProperty->enumeration;
        }

        return $property;
    }

    protected static function getDescription($reflection, $component)
    {
        $description ='';
        if (isset($reflection->summary)) {
            $description = trim($reflection->summary);
        }
        if (!isset($reflection->description)) {
            $description .= $reflection->description;
        }

        if (!empty(trim($description))) {
            $component->description = trim($description);
        }
    }
}
