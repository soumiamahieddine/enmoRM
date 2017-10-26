<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency html.
 *
 * Dependency html is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency html is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency html.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\html;
/**
 * undocumented class
 *
 * @package Dependency_Html
 * 
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class DocumentFragment
    extends \dependency\xml\DocumentFragment
{
    /**
     * Appendh Html content to the fragment
     * @param string $source The html source content
     * 
     * @return bool
     */
    public function appendHtml($source)
    {
        libxml_use_internal_errors(true);

        // Add html contents to a wrapper
        $htmlWrapperId = \laabs\uniqid();
        $htmlWrapper = "<div id='$htmlWrapperId'>" . utf8_decode($source) . '</div>';

        // Load html contents with DOM (Html parser required)
        $hdoc = new \DOMDocument("1.0", "UTF-8");
        $hdoc->loadHTML($htmlWrapper);

        // Save all nodes append to wrapper into XML to re-append it (XML parser this time... because it is the only parser available on fragment)
        $children = $hdoc->getElementById($htmlWrapperId)->childNodes;
        foreach ($children as $child) {
            $childHtml = $hdoc->saveXml($child, LIBXML_NOEMPTYTAG + LIBXML_NOXMLDECL);
            $childHtml = str_replace("?>", ">", $childHtml);
            $result = parent::appendXml($childHtml);
        }
        libxml_use_internal_errors(false);

        $this->ownerDocument->addPlugins($this);
        $this->ownerDocument->hinclude($this);

        return $result;
    }
    
    /**
     * Appendh Html resource file contents to the fragment
     * @param string $resource The html source uri
     * 
     * @return bool
     */
    public function appendHtmlFile($resource)
    {
        $resource = LAABS_PRESENTATION . LAABS_URI_SEPARATOR
            . 'view' . LAABS_URI_SEPARATOR . $resource;

        $router = new \core\Route\ResourceRouter($resource);

        $path = $router->getResource()->getRealPath();
        $source = file_get_contents($path);

        return $this->appendHtml($source);
    }
    
    /**
     * Clone the Html node with its plugins
     * @param bool $deep clone with chil nodes
     * 
     * @return object The cloned node
     */
    public function cloneNode($deep=false)
    {
        $clonedNode = parent::cloneNode($deep);
        $this->ownerDocument->addPlugins($this);
        
        return $clonedNode;
    }
}