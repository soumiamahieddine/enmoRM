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
 * Interface for the conversion
 *
 * @package Dependency/fileSystem
 */
interface conversionInterface
{
    /**
     * Convert a file
     * @param string $srcfile The source file
     * @param mixte  $options The options
     * @return bool
     */
    public function convert($srcfile, $options);

    /**
     * Convert a file
     * @return bool
     */
    public function getSoftwareName();

    /**
     * Convert a file
     * @return bool
     */
    public function getSoftwareVersion();
}
