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
 * Class for exception when the file is not on the file system
 */
class Exception
    extends \core\Exception
{
    
    protected $output;

    public function __construct($message="", $code=0, \Exception $previous=null, array $output=null) 
    {
        parent::__construct($message, $code, $previous);
        
        $this->output = $output;
    }

    public function getOutput()
    {
        return $this->output;
    }
}