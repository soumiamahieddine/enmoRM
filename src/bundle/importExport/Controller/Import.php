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

        var_dump(explode(",", $datas[0]));
        var_dump($header);
        exit;
    }

    protected function isBase64($string)
    {
        if (base64_encode(base64_decode($string, true)) === $string) {
            return true;
        }

        return false;
    }

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
                $value = true;
                break;
            default:
                $value = false;
                break;
        }

        return $value;
    }
}
