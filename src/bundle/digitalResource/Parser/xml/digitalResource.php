<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle digitalResource.
 *
 * Bundle digitalResource is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle digitalResource is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\digitalResource\Parser\xml;

/**
 * digitalResource parser
 *
 * @package DigitalResource
 * @author  Maarch Prosper De Laure <prosper.delaure@maarch.org>
 */
class digitalResource
{
    protected $xmlDocument;

    protected $xPath;

    /**
     * Constructor
     * @param \dependency\xml\Document $xmlDocument
     */
    public function __construct(\dependency\xml\Document $xmlDocument) 
    {
        $this->xmlDocument = $xmlDocument;
    }

    /**
     * Parse xml data
     * @param string $resourceXml
     *
     * @return array
     */
    public function create($resourceXml)
    {
        $this->xmlDocument->loadXml($resourceXml);
        $this->xPath = new \DOMXPath($this->xmlDocument);

        $digitalResource = \laabs::newInstance('digitalResource/digitalResource');

        $type = \laabs::getClass('digitalResource/digitalResource');
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

        $resourceElement = $this->xmlDocument->documentElement;
        if ($resourceElement->hasAttribute('filename')) {
            $filename = $resourceElement->getAttribute('filename');

            $pathinfo = pathinfo($filename);
            $digitalResource->fileName = $pathinfo['filename'];
            if (isset($pathinfo['extension'])) {
                $digitalResource->fileExtension = $pathinfo['extension'];
            }

            $contents = file_get_contents($filename);
        } elseif ($resourceElement->hasAttribute('uri')) {
            $contents = file_get_contents($resourceElement->getAttribute('uri'));
        } elseif ($contentsElement = $this->xPath->query("contents")->item(0)) {
            $contents = base64_decode($contentsElement->nodeValue);
        }

        if (isset($contents)) {
            $digitalResource->size = strlen($contents);

            $finfo = new \finfo();
            $digitalResource->mimetype = $finfo->buffer($contents, \FILEINFO_MIME_TYPE);

            $digitalResource->setContents($contents);
        }

        return array('digitalResource' => $digitalResource);
    }
}
