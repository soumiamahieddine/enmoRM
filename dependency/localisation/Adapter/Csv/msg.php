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
namespace dependency\localisation\Adapter\Csv;
/**
 * Language message for CSV adapter
 *
 * @package Localisation
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */ 
class msg
{
    use \core\ReadonlyTrait;

    public $msgid;

    public $msgctxt;

    public $msgstr;

    /**
     * Constructor
     * @param array $data The csv line. The format depends on the number of columns in the data
     * 2 stands for  msgid [msgctxt], msgstr
     * 4 or more for msgid [msgctxt], nmsgid, msgstr, nmsgtxt [n..m], mmsgtxt [y..z]
     *
     * @return void
     */
    public function __construct(array $data)
    {
        // Get msg id. If context given , extract context and msgid
        $msgid = array_shift($data);
        if (preg_match("#(.+)\s\[([^\]]+)\]$#", $msgid, $matches)) {
            $this->msgid = trim($matches[1]);
            $this->msgctxt = trim($matches[2]);
        } else {
            $this->msgid = trim($msgid);
        }
        
        while ($msgstr = array_shift($data)) {
            $this->msgstr[] = trim($msgstr);
        } 
    }

}