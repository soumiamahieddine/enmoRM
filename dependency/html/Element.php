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
class Element
    extends \dependency\xml\Element
{
    /* -------------------------------------------------------------------------
    - Properties
    ------------------------------------------------------------------------- */
    protected $plugin;
    /* -------------------------------------------------------------------------
    - Methods
    ------------------------------------------------------------------------- */
    
    /**
     * Clone node
     * @param boolean $deep
     * 
     * @return \DOMNode
     */
    public function cloneNode($deep=false) {
        $clonedNode = parent::cloneNode($deep);
        $this->ownerDocument->addPlugins($clonedNode);
        return $clonedNode;
    }
    
    /**
     * Add html Class
     * @param string $newHtmlClasses
     */
    public function addHtmlClass($newHtmlClasses) {
        $htmlClasses = $this->tokenize($this->getAttribute("class"));
        foreach ($this->tokenize($newHtmlClasses) as $newHtmlClass)
            if (!in_array($newHtmlClass, $htmlClasses))
                $htmlClasses[] = $newHtmlClass;
        $result = parent::setAttribute('class', $this->stringify($htmlClasses));
    }
    
    /**
     * Remove html classes
     * @param string $oldHtmlClasses
     * 
     * @return string
     */
    public function removeHtmlClass($oldHtmlClasses)
    {
        $htmlClasses = $this->tokenize($this->getAttribute("class"));
        foreach ($this->tokenize($oldHtmlClasses) as $oldHtmlClass)
            if ($i = array_search($oldHtmlClass, $htmlClasses))
                unset($htmlClasses[$i]);
        return parent::setAttribute('class', $this->stringify($htmlClasses));
    }
    
    /**
     * Has html class
     * @param string $htmlClass
     * @return array
     */
    public function hasHtmlClass($htmlClass)
    {
        return (array_search($htmlClass, $this->tokenize($this->getAttribute("class"))) !== false);
    }
    
    /**
     * Token size
     * @param string $string
     * 
     * @return integer
     */
    public function tokenize($string)
    {
        return explode(" ", $string);
    }
    
    /**
     * stringify
     * @param array $array
     * 
     * @return string
     */
    public function stringify(array $array)
    {
        return implode(" ", $array);
    }

    /**
     * Returns the requested property
     *
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if ($name == 'plugin' && empty($this->plugin)) {
            $this->plugin = new PluginContainer($this);
        }

        return $this->{$name};
    }

    /**
     * Add plugins
     */
    public function addPlugins()
    {
        if (!isset($this->plugin)) {
            $this->plugin = new PluginContainer($this);
        }

        foreach (explode(' ', $this->getAttribute('class')) as $htmlClass) {
            if (isset($this->ownerDocument->plugins[$htmlClass])) {
                $this->plugin->add($htmlClass);
            }
        }
    }

    /**
     * Save plugins
     */
    public function savePlugins()
    {
        foreach ($this->plugin as $name => $plugin) {
            if (method_exists($plugin, 'saveHtml')) {
                $plugin->saveHtml();
            }

            if (method_exists($plugin, 'saveParameters') && !isset($this->ownerDocument->pluginsParameters[$name])) {
                $plugin->saveParameters();

                $this->ownerDocument->pluginsParameters[$name] = $plugin;
            }
        }
    }
}
