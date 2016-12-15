<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle financialRecords.
 *
 * Bundle financialRecords is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle financialRecords is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle financialRecords.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\recordsManagement\Serializer\xml;
/**
 * accounting record Xml serializer
 *
 * @package recordsManagement
 * @author Alexandre Morin (Maarch) <alexandre.morin@maarch.org>
 */
class archivalProfile
{

    protected $xml;

    /**
     * Constructor of archivalProfile class
     * @param \dependency\xml\Document $xml
     */
    public function __construct(\dependency\xml\Document $xml)
    {
        $this->xml = $xml;
        $this->xml->formatOutput = true;
    }

    /**
     * Serialize archival Profile as XML
     * @param recordsManagement/archivalProfile archivalProfile
     *
     * @return string
     */
    public function read($archivalProfile)
    {
        $fragment = $this->xml->createDocumentFragment();
        $fragment->appendFile('recordsManagement/xml/archivalProfile.xml');
        $this->xml->appendChild($fragment);

        $this->xml->setSource('archivalProfile', $archivalProfile);

        $this->xml->merge();

        return $this->xml->saveXml($this->xml->documentElement);
    }

}
