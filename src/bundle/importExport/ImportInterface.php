<?php
/*
 * Copyright (C) 2020 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\importExport;

/**
 * Interface for import
 */
interface ImportInterface
{
    /**
     * @param string  $data      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @action importExport/Import/create
     *
     * @return boolean        Import with reset of table data or not
     */
    public function create_dataType_($data, $isReset = false);
}
