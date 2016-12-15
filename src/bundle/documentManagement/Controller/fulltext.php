<?php
/*
 * Copyright (C) 2016 Maarch
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
namespace bundle\documentManagement\Controller;

/**
 * Document object
 *
 * @package DocumentManagement
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class fulltext
{
    protected $sdoFactory;
    protected $fulltextEngine;

    /**
     * Constructor
     * @param \dependency\sdo\Factory                      $sdoFactory     The sdo factory
     * @param \dependency\fulltext\FulltextEngineInterface $fulltextEngine The fulltext search engine
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, \dependency\fulltext\FulltextEngineInterface $fulltextEngine)
    {
        $this->sdoFactory = $sdoFactory;
        $this->fulltextEngine = $fulltextEngine;
    }

    /**
     * Store fulltext in an index
     * @param string $index    The index
     * @param string $docId    The document id
     * @param string $fulltext The text
     *
     * @return boolean The result of the operation
     */
    public function store($index, $docId, $fulltext)
    {
        if (!$this->fulltextEngine->indexExists($index)) {
            $this->fulltextEngine->createIndex($index);
        }

        $document = \laabs::newService("dependency/fulltext/Document");
        $document->addField('docId', $docId, 'UnIndexed');
        $document->text['content'] = $fulltext;

        return $this->fulltextEngine->addDocument($index, $document);
    }

    /**
     * Store fulltext in an index
     * @param string $index The index
     * @param string $docId The document id
     *
     * @return boolean The result of the operation
     */
    public function delete($index, $docId)
    {
        $document = \laabs::newService('dependency/fulltext/FtDocument');
        $document->key['docId'] = $docId;

        try {
            $this->fulltextEngine->removeDocument($index, $document);

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Search fulltext in an index
     * @param string $index The index
     * @param string $query The query
     *
     * @return boolean The result of the operation
     */
    public function find($index, $query)
    {
        $result = $this->fulltextEngine->find($index, $query, ['content']);
        $documents = [];

        foreach ($result as $fulltextObject) {
            try {
                $document = $this->sdoFactory->read('documentManagement/archiveDocument', $fulltextObject->fields['docId']);
                $document->fulltext = $fulltextObject->fields['content'];

                $documents[] = $document;
            } catch (\Exception $e) {
            }
        }

        return $documents;
    }
}
