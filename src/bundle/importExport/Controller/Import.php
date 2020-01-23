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
class Import extends ImportExport
{
    /**
     * Import chosen data with chosen format
     *
     * @param string  $dataType Type of data to visualize (organization, user, etc)
     * @param string  $csv      Data base64 encoded or not
     * @param boolean $isReset  Reset tables or not
     *
     * @return boolean          Data exported well or not
     */
    public function create($dataType, $csv, $isReset = false)
    {
        if (!is_string($csv)) {
            throw new \core\Exception\BadRequestException("Data your trying to import is in an invalid format");
        }

        if ($this->isBase64($csv)) {
            $csv = base64_decode($csv, true);
        }

        $header = $this->getDefaultHeader($dataType);

        $datas = explode("\n", $csv);

        //compare csv header with message template
        if (str_getcsv($datas[0], ',', '"') != $header) {
            throw new \core\Exception\BadRequestException("Error in csv header");
        }

        //remove header
        unset($datas[0]);

        $cleanDatas = $this->convertCsvToArray($datas, $dataType);

        $this->controller[$dataType]->import($cleanDatas, $isReset);
    }

    /**
     * Convert an array of csv string into a ful array
     *
     * @param array  $arrayCsv    Array of csv strings
     * @param string $dataType    Type of data to visualize (organization, user, etc)
     *
     * @return array $cleanDatas  Array of datas
     */
    private function convertCsvToArray($arrayCsv, $dataType)
    {
        $datas = [];
        $header = $this->getDefaultHeader($dataType);

        foreach ($arrayCsv as $key => $csv) {
            $csvLineValues = str_getcsv($csv, ',', '"');
            if (empty($csvLineValues) || (count($csvLineValues) != count($header))) {
                throw new \core\Exception\BadRequestException("Error in data");
            }

            foreach ($csvLineValues as $line => $value) {
                if ($value == '') {
                    $csvLineValues[$line] = null;
                }

                if ($value == '0' || $value == '1') {
                    $csvLineValues[$line] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }

                if (strpos($value, ';')) {
                    $csvLineValues[$line] = explode(';', $value);
                }
            }

            $datas[] = array_combine($header, $csvLineValues);
        }

        return $datas;
    }

    protected function updateUseraccount()
    {
        var_dump('toto');
        exit;
    }

    /**
     * Check if a string is base 64 encoded
     *
     * @param  string $string string to test
     *
     * @return boolean If string is base64 encoded or not
     */
    protected function isBase64($string)
    {
        if (base64_encode(base64_decode($string, true)) === $string) {
            return true;
        }

        return false;
    }
}
