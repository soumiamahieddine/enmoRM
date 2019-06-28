<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency timestamp.
 *
 * Dependency timestamp is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency timestamp is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency timestamp.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\timestamp\plugins;
/**
 * Timestamp service
 */
class Timestamp implements \dependency\timestamp\TimestampInterface
{

    protected $timestamp;
    /**
     * Constructor
     */
    public function __construct()
    {

    }

    /**
     * Get a timestamp file for a journal
     * @param string $journalFile The journal file name
     *
     * @return string the timestamp file name
     */
    public function getTimestamp($journalFile)
    {
        $this->timestamp = new \DateTime();

        $tmpDir = pathinfo($journalFile)["dirname"];
        $timestampPath = $tmpDir."/timestamp.txt" ;
        $timestampFile = fopen($timestampPath, "w");

        fwrite($timestampFile, $this->timestamp->format('Y-m-d H:i:s'));

        fclose($timestampFile);
        
        return $timestampPath;
    }
}
