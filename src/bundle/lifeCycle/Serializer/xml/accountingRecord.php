<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle lifeCycle.
 *
 * Bundle lifeCycle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle lifeCycle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle lifeCycle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\lifeCycle\Serializer\xml;
/**
 * accounting record Xml serializer
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class event
{

    protected $xml;

    /**
     * Constructor of accountingRecord class
     * @param \dependency\xml\Document $xml
     */
    public function __construct(\dependency\xml\Document $xml)
    {
        $this->xml = $xml;
        $this->xml->formatOutput = true;
    }

    /**
     * Serialize accounting record as XML
     * @param recordsManagement/accountingRecord $accountingRecord
     *
     * @return string
     */
    public function read($event)
    {
        $fragment = $this->xml->createDocumentFragment();
        $fragment->appendFile('lifeCycle/xml/event.xml');
        $this->xml->appendChild($fragment);

        $this->xml->setSource('accountingRecord', $accountingRecord);

        $this->xml->merge();

        return $this->xml->saveXml($this->xml->documentElement);
    }

}
