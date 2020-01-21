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
 * Interface for export
 */
interface ExportInterface
{
    /**
     * Create a csv file with type of data chosen
     *
     * @param  string $dataType Type of data to export (organization, user, etc)
     *
     * @action importExport/Export/create
     *
     * @return binary $csv      Csv files with data exported
     */
    public function create($dataType);

    /**
     * Read an excerpt of data type user can export
     *
     * @param  string $dataType Type of data to visualize (organization, user, etc)
     *
     * @action importExport/Export/read
     *
     * @return array $data      Csv files with data exported
     */
    public function read($dataType);
}
