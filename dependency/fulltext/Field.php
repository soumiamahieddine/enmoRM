<?php
/*
 * Copyright (C) 2016 Maarch
 *
 * This file is part of dependency Fulltext.
 *
 * Bundle documentManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle documentManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with dependency Fulltext. If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\fulltext;
/**
 * The document field
 * 
 * @author Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 */
class Field
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $value;
    
    /**
     * @var string
     */
    public $type;

    /**
     * Construct a new field
     * @param string $name
     * @param string $value
     * @param string $type
     */
    public function __construct($name, $value, $type=false)
    {
        $this->name = $name;

        $this->value = $value;

        $this->type = $type;
    }
}
