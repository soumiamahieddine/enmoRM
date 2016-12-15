<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle documentManagement.
 *
 * Bundle documentManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle documentManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle documentManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\documentManagement\Parser\xml;
/**
 * Parser for XML documents
 */
class document
{
    protected $xmlDocument;

    protected $xPath;

    /**
     * digital resource xml parser
     * @var object
     */
    protected $digitalResourceParser;

    /**
     * Constructor
     * @param \dependency\xml\Document $xmlDocument
     */
    public function __construct(\dependency\xml\Document $xmlDocument) 
    {
        $this->xmlDocument = $xmlDocument;

        $this->digitalResourceParser = \laabs::newParser('digitalResource/digitalResource', 'xml');
    }

    /**
     * Parse Xml element into archive object
     * @param object $xml
     * 
     * @return object
     */
    public function create($xml)
    {
        $this->xmlDocument->loadXml($xml);
        $this->xPath = new \DOMXPath($this->xmlDocument);

        $document = \laabs::newInstance('documentManagement/document');

        $type = \laabs::getClass('documentManagement/document');
        $properties = $type->getProperties();

        foreach ($properties as $property) {
            if ($property->isScalar()) {
                $node = $this->xPath->query("$property->name")->item(0);
                if ($node) {
                    $stringValue = $node->nodeValue;

                    if ($stringValue) {
                        $document->{$property->name} = \laabs::cast($stringValue, $property->getType());
                    }
                }
            } 
        }

        $resourceElement = $this->xPath->query("digitalResource")->item(0);
        if ($resourceElement) {
            $resource = $this->digitalResourceParser->create($this->xmlDocument->saveXml($resourceElement))['digitalResource'];
            $document->digitalResource = $resource;
        } 

        return $document;
    }

}