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

class Factory
    extends \DOMImplementation
{
    /* Constants */

    /* Properties */


    protected $lang;

    protected $extensions;

    protected $classes;

    protected $plugins;

    protected $headers;

    protected $layout;

    protected $view;

    protected $XPath;

    protected $Sources;

    public function __construct($layout, $lang=false, $extensions=null, $plugins=null, $headers=null, $classes=null) {
        $this->layout = $layout;

        $this->lang = $lang;

        $this->plugins    = $plugins;
        $this->extensions = $extensions;

        $this->headers = $headers;
        $this->classes = $classes;
    }

    /**
     * Set layout
     * 
     * @param boolean $layout
     */
    public function setLayout($layout=false) {
        $this->layout = $layout;
    }

    /**
     * Set lang
     * 
     * @param string $lang
     */
    public function setLang($lang) {
        $this->lang = $lang;
    }

    /**
     * New view
     * 
     * @param type $layout
     * 
     * @return view
     */
    public function newView($layout=null)
    {
        $view = new \dependency\html\Document($this->extensions, $this->plugins, $this->headers, $this->classes);

        if ($layout === false) {
            // Layout is false make empty document
        } else {
            // Layout is null (not passed) use default layout
            if (is_null($layout)) $layout = $this->layout;
            $LayoutRoute = new \core\Route\ResourceRouter($layout);
            $layoutPath = $LayoutRoute->Resource->getRealPath();
            $layoutFragment = $view->createDocumentFragment();
            $layoutFragment->appendFile($layoutPath);
            $view->getElementsByTagName('body')->item(0)->appendChild($layoutFragment);
        }

        $this->View = $view;
        $this->XPath = new \dependency\html\XPath($this->View);

        return $this->View;
    }

}