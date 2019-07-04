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
namespace dependency\timestamp;
/**
 *
 */
interface TimestampInterface
{
    /**
     * Get a timestamp file for a journal
     * @param string $journalFile The journal file name
     *
     * @return string the timestamp file name
     */
    public function getTimestamp($journalFile);
}