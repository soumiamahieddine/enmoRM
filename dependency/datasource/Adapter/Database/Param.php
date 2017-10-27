<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency datasource.
 *
 * Dependency datasource is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency datasource is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency datasource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\datasource\Adapter\Database;

class Param
    implements \dependency\datasource\ParamInterface
{

    /* Properties */
    protected $name;
    protected $value;
    protected $type;
    protected $length;
    protected $ref;

    /* Methods */
    public function __construct($name, &$value, $type=\PDO::PARAM_STR, $length=null, $ref=false) {
        $this->name = $name;
        $this->value = &$value;
        $this->type = $type;
        $this->length = $length;
        $this->ref = $ref;
    }

    /**
     * Get name
     * 
     * @return strin
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Get value
     * 
     * @return string
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * Get type
     * 
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Get length
     * 
     * @return string
     */
    public function getLength() {
        return $this->length;
    }

}