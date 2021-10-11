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
 * Abstract calss for requests : http, cli, batch
 *
 * @package Laabs
 */
abstract class AbstractRequest
    implements RequestInterface
{

    use \core\ReadonlyTrait;

    /* -------------------------------------------------------------------------
    - Constants
    ------------------------------------------------------------------------- */

    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */
    protected static $instance;

    /**
     * The protocol : http or cli
     * @var string
     */
    public $mode;

    /**
     * The authentication mode if found in the request
     * @var string
     */
    public $authentication;

    /**
     * The Laabs method : 
     *  - For action requests : CREATE READ UPDATE DELETE
     *  - For batch requests  : RUN RESTART LIST INFO
     * @var string
     */
    public $method;

    /**
     * The frontal script
     * @var string
     */
    public $script;

    /**
     * The request uri
     * @var string
     */
    public $uri;

    /**
     * The query type
     * @var string
     */
    public $queryType;

    /**
     * The query string
     * @var string
     */
    public $query;

    /**
     * The content type
     * @var string
     */
    public $contentType;

    /**
     * The request body content
     * @var string
     */
    public $body;

    /**
     * The accepted types
     * @var array
     */
    public $accept;

    /**
     * The accepted languages
     * @var array
     */
    public $acceptLanguage;

    /**
     * The accepted encodings
     * @var array
     */
    public $acceptEncoding;

    /**
     * The accepted charsets
     * @var array
     */
    public $acceptCharset;

    /**
     * The accepted number of entities returned
     * @var integer
     */
    public $maxCount;

    /**
     * The requested host
     * @var string
     */
    public $host;

    /**
     * The array of tokens received
     * @var array
     */
    public $tokens;

    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    /**
     * Get the authentication information 
     */
    protected function getAuthentication()
    {
        $this->authentication = $this->selectAuthentication(); 
    }

    protected function selectAuthentication()
    {
        $authModes = \laabs::getAuthModes();
        foreach ($authModes as $authMode) {
            switch ($authMode) {
                case LAABS_BASIC_AUTH:
                    if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {
                        return new basicAuthentication($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']);
                    }
                    break;

                case LAABS_DIGEST_AUTH:
                    if (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                        $neededParts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
                        $data = array();
                        $keys = implode('|', array_keys($neededParts));
                     
                        preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $_SERVER['PHP_AUTH_DIGEST'], $matches, PREG_SET_ORDER);

                        foreach ($matches as $match) {
                            $data[$match[1]] = $match[3] ? $match[3] : $match[4];
                            unset($neededParts[$match[1]]);
                        }

                        return new digestAuthentication($data['username'], $data['nonce'], $data['uri'], $data['response'], $data['qop'], $data['nc'], $data['cnonce']);
                    }
                    break;

                case LAABS_REMOTE_AUTH:
                    if (isset($_SERVER['REMOTE_USER']) && isset($_SERVER['AUTH_TYPE'])) {
                        return new remoteAuthentication($_SERVER['REMOTE_USER'], $_SERVER['AUTH_TYPE']);
                    }
                    break;
            }
        }
    }
}