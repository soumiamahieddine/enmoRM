<?php

/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of dependency pickLists.
 *
 * Dependency pickLists is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency pickLists is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with dependency pickLists. If not, see <http://www.gnu.org/licenses/>.
 */

namespace dependency\pickLists;

/**
 * Interface for pick lists datasources
 *
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
interface PickListInterface
{
    /**
     * Returns a set of values
     * @param string $query
     * @param string $order
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     */
    public function search(string $query = null, $limit = 100, $offset = 0): array;

    /**
     * Reads an entry or checks existence
     * @param string $key
     *
     * @return mixed The entry
     */
    public function get(string $key);
}
