<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency localisation.
 *
 * Dependency localisation is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency localisation is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency localisation.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\localisation;

class Message
    implements \dependency\localisation\MessageInterface
{
    /* Constants */

    /* properties */
    protected $msgtxt;
    protected $msgvars = array();
    protected $binds = array();

    /* Methods */
    public function __construct($msgtxt) {
        $this->msgtxt = $msgtxt;

        if (preg_match_all("#\:(\w+)#", ' ' . $msgtxt . ' ', $msgvars))
            $this->msgvars = $msgvars[1];
    }

    /**
     * bind value
     * @param string $name
     * @param string $value
     *
     */
    public function bindValue($name, $value) {
        $this->binds[$name] = $value;
    }

    /**
     * bind variable
     * @param string $name
     * @param string $variable
     *
     */
    public function bindVariable($name, &$variable){
        $this->binds[$name] = $variable;
    }

    /**
     * get text
     * @param array $msgvals
     *
     */
    public function getText(array $msgvals=null) {
        $msgtxt = $this->msgtxt;
        foreach ($this->msgvars as $msgvar) {
            if (isset($msgvals[$msgvar]))
                $msgval = $msgvals[$msgvar];
            elseif (isset($this->binds[$msgvar]))
                $msgval = $this->binds[$msgvar];

            $msgtxt = str_replace(":" . $msgvar, $msgval, $msgtxt);
        }
        return $msgtxt;
    }

}