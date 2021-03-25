<?php
/*
 * Copyright (C) 2021 Maarch
 *
 * This file is part of dependency filesystem.
 *
 * Dependency filesystem is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency filesystem is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency filesystel.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\fileSystem;
/**
 *
 */
interface FullTextInterface
{
    /**
     * Get the extracted content from a file 
     * @param string $filename
     * 
     * @return string The extracted file content
     */
    public function getText($filename);
}