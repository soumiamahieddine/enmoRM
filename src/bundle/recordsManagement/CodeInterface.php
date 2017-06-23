<?php

/*
 * Copyright (C) 2017 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\recordsManagement;

/**
 * Interface for code
 *
 * @package importFromScan
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface CodeInterface
{
    /**
     * Generate file with code
     *
     * @param string $data  The data of codes
     * @param string $label The label
     *
     * @action recordsManagement/Code/generateCodes
     */
    public function createGenerate($data, $label);
}