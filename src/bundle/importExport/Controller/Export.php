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
namespace bundle\importExport\Controller;

use core\Exception;

/**
 * Control of the organization
 *
 * @package importExport
 */
class Export extends ImportExport
{
    /**
     * Create a csv file with type of data chosen
     *
     * @param  string $dataType Type of data to export (organization, user, etc)
     *
     * @return binary $csv      Csv files with data exported
     */
    public function create($dataType)
    {
        $this->limit = null;
        $data = $this->read($dataType);

        $csvFile = fopen('php://output', 'w');

        $header = [];
        $object = \laabs::newInstance($this->model[$dataType]);
        foreach ($object as $key => $value) {
            $header[] = $key;
        }
        fputcsv($csvFile, $header);

        foreach ($data as $list) {
            fputcsv($csvFile, (array) $list);
        }

        return $csvFile;
    }

    /**
     * Read an excerpt of data type user can export
     *
     * @param  string $dataType Type of data to visualize (organization, user, etc)
     *
     * @action importExport/Export/read
     *
     * @return array $data      Csv files with data exported
     */
    public function read($dataType)
    {
        if (!array_key_exists($dataType, $this->controller)) {
            throw new \core\Exception\BadRequestException("Data your trying to export does not exists");
        }

        $data = $this->controller[$dataType]->index($this->limit);

        return $data;
    }
}
