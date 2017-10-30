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
namespace dependency\fileSystem\plugins;
/**
 * Main class for file format identification
 * 
 * @author Cyril Vazquez Maarch <cyril.vazquez@maarch.org>
 */
class fid
{
    public $droid;

    public function __construct($signatureFile=false, $containerSignatureFile=false, $zipExtractCmd=false)
    {
        require_once (__DIR__ . '/fid/Main.php');

        $this->droid = new \droid($signatureFile, $containerSignatureFile, $zipExtractCmd);
    }

    public function match($filename)
    {
        return $this->droid->match($filename);
    }

}