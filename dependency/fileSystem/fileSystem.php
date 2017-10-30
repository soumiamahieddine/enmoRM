<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency fileSystem.
 *
 * Dependency fileSystem is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency fileSystem is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency fileSystem.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\fileSystem;
/**
 * Class for a file in file system
 * 
 * @package Dependency/fileSystem
 */
class fileSystem
{

    /**
     * Check if file system is windows
     * @return bool
     */
    public function isWin() 
    {
        return (strtoupper(substr(php_uname('s'), 0, 3)) === 'WIN');
    }

    /**
     * Check if file system is *nix
     * @return bool
     */
    public function isNix() 
    {
        return (strtoupper(substr(php_uname('s'), 0, 3)) !== 'WIN');
    }

    /**
     * Add a path to include path
     * @param string $path
     * 
     * @return string The new directive value
     */
    public function addIncludePath($path)
    {
        return set_include_path(get_include_path() . PATH_SEPARATOR . $path);
    }

    /**
     * Magic access to plugin methods
     * @param string $name The name of the plugin
     * @param string $args The args
     * 
     * @return object The plugin instance
     */
    public function __call($name, $args)
    {
       
        if (method_exists($this, $name)) {
            return call_user_func_array(array($this, $name), $args);
        }

        array_unshift($args, "dependency/fileSystem/plugins/$name/$name");

        return call_user_func_array('\laabs::newService', $args);
    }


}