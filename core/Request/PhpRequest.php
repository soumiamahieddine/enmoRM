<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace core\Request;
/**
 * Class for http requests
 */
class PhpRequest
    extends AbstractRequest
{

    /**
     * Constructor
     */
    public function __construct($method, $path='', $params=array(), $body=array())
    {
        $this->mode = 'php';

        $this->queryType = 'arg';

        $this->method = $method;

        $this->uri = $path;

        $this->query = $params;

        $this->body = $body;

        $this->contentType = 'php';

        $this->accept = array(1 => 'php');
    }

    public function setToken($name, $value)
    {
        $key = \laabs::getCryptKey();

        $binToken = base64_decode($value);
        $jsonToken = \laabs::decrypt($binToken, $key);
        $token = \json_decode(trim($jsonToken));

        $GLOBALS['TOKEN'][$name] = $token;
    }

}
