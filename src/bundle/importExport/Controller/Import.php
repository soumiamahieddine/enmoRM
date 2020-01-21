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

        $isReset = $this->cleanBooleanValue($isReset);

        $object = \laabs::newMessage($this->message[strtolower($dataType)]);
        foreach ($object as $key => $value) {
            $header[] = $key;
        }

        $datas = explode("\n", $csv);

        //compare csv header with message template
        if (str_getcsv($datas[0], ',', '"') != $header) {
            throw new \core\Exception\BadRequestException("Error in csv header");
        }

        //remove header
        unset($datas[0]);

        foreach ($datas as $line => $data) {
            $csvLineValues = str_getcsv($data, ',', '"');
            if ($isReset) {
                $functionName = 'create' . ucfirst(strtolower($dataType));
                $this->$functionName();
            } else {
                $functionName = 'update' . ucfirst(strtolower($dataType));
                $this->$functionName();
            }
        }
    }

    protected function createUseraccount()
    {
        var_dump('titi');
        return true;
    }

    protected function updateUseraccount()
    {
        var_dump('toto');
        exit;
        return true;
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


    /**
     * Clean boolean values received from get parameter. Return true if standard boolean values, false otherwise
     *
     * @param  string $value string to clean
     *
     * @return boolean        value cleaned
     */
    protected function cleanBooleanValue($value)
    {
        switch ($value) {
            case 'true':
            case '1':
            case 'y':
            case 'Y':
            case 'o':
            case 'O':
            case 'oui':
            case 'Oui':
            case 'OUI':
            case 'yes':
            case 'Yes':
            case 'YES':
            case true:
                $value = true;
                break;
            default:
                $value = false;
                break;
        }

        return $value;
    }
}
