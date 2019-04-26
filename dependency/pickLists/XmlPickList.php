<?php

/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of dependency pickLists.
 *
 * Dependency pickLists is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency pickLists is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with dependency pickLists. If not, see <http://www.gnu.org/licenses/>.
 */

namespace dependency\pickLists;

/**
 * Pick lists from xml
 *
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class XmlPickList implements PickListInterface
{
    /**
     * @var DOMDocument The DOMDocument object
     */
    protected $document;

    /**
     * @var DOMXPath The DOMXPath object
     */
    protected $xPath;

    /**
     * @var string The item xpath template
     */
    protected $itemXPath;

    /**
     * @var string The key xpath template
     */
    protected $keyXPath;

    /**
     * @var array The value xpath templates
     */
    protected $valuesXPath;

    /**
     * Constructor
     * @param string $filename The XML uri
     *
     */
    public function __construct(string $filename, string $itemXPath, string $keyXPath, array $valuesXPath)
    {
        $this->document = new \DOMDocument();
        $this->document->load($filename);

        $this->xPath = new \DOMXPath($this->document);

        $this->itemXPath = $itemXPath;
        $this->keyXPath = $keyXPath;
        $this->valuesXPath = $valuesXPath;
    }

    /**
     * Returns a set of values
     * @param string string $query
     *
     * @return array
     */
    public function search(string $query = null, $limit = 100, $offset = 0): array
    {
        $array = [];

        if (!is_null($query)) {
            $xPathExprs = [];
            foreach ($this->valuesXPath as $name => $valueXPath) {
                $xPathExprs[] = sprintf('%1$s[contains(%2$s, "%3$s")]', $this->itemXPath, $valueXPath, $query);
            }
            $xPathExpr = implode('|', $xPathExprs);
        } else {
            $xPathExpr = $this->itemXPath;
        }

        $domNodeList = $this->xPath->query($xPathExpr);

        for ($i=$offset; $i<=$limit; $i++) {
            $itemNode = $domNodeList->item($i);
            if (!$itemNode) {
                break;
            }

            $array[] = $this->getValue($itemNode);
        }
       
        return $array;
    }

    /**
     * Reads an entry or checks existence
     * @param string $key
     *
     * @return object The value or null
     */
    public function get(string $key)
    {
        $xPathExpr = sprintf('%s[%s="%s"]', $this->itemXPath, $this->keyXPath, $key);

        $domNodeList = $this->xPath->query($xPathExpr);

        if ($domNodeList->length == 0) {
            return null;
        }

        $itemNode = $domNodeList->item(0);

        return $this->getValue($itemNode);
    }

    protected function getKey($itemNode)
    {
        $domNodeList = $this->xPath->query($this->keyXPath, $itemNode);

        if ($domNodeList->length > 0) {
            return $domNodeList->item(0)->nodeValue;
        }
    }

    protected function getValue($itemNode)
    {
        $item = new \stdClass();
        foreach ($this->valuesXPath as $name => $valueXPath) {
            $valueNode = $this->xPath->query($valueXPath, $itemNode)->item(0);
            if ($valueNode) {
                $item->{$name} = $valueNode->nodeValue;
            }
        }

        return $item;
    }
}
