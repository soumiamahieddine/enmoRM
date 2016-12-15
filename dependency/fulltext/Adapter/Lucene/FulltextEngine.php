<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency zend.
 *
 * Dependency zend is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency zend is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency zend.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace dependency\fulltext\Adapter\Lucene;

/**
 * Apache Lucene fulltext engine
 */
class FulltextEngine implements \dependency\fulltext\FulltextEngineInterface
{
    protected $repository;

    /**
     * Constructor of lucene dependency
     * @param string $repository The indexes repository
     * @param array  $options    Additionnal options
     */
    public function __construct($repository, $options = null)
    {
        $this->repository = str_replace("/", DIRECTORY_SEPARATOR, $repository);

        if (!is_dir($this->repository)) {
            mkdir($this->repository, 0777, true);
        }

        set_include_path(__dir__.DIRECTORY_SEPARATOR.PATH_SEPARATOR.get_include_path());
        require_once('Zend/Search/Lucene.php');

        \Zend_Search_Lucene_Search_QueryParser::setDefaultEncoding('utf-8');
        \Zend_Search_Lucene_Analysis_Analyzer::setDefault(new \Zend_Search_Lucene_Analysis_Analyzer_Common_Utf8Num_CaseInsensitive());
        \Zend_Search_Lucene_Search_QueryParser::setDefaultOperator(\Zend_Search_Lucene_Search_QueryParser::B_OR);
    }

    /**
     * Check if an index exists
     * @param string $index The index name
     *
     * @return boolean The result of the operation
     */
    public function indexExists($index)
    {
        try {
            $index = \Zend_Search_Lucene::open($this->repository.DIRECTORY_SEPARATOR.$index);

        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Create an index
     * @param string $index The index name
     *
     * @return The index object
     */
    public function createIndex($index)
    {
        if ($this->indexExists($index)) {
            return false;
        }

        $index = \Zend_Search_Lucene::create($this->repository.DIRECTORY_SEPARATOR.$index);

        return $index;
    }

    /**
     * Get the indexes
     *
     * @return Array The index names
     */
    public function getIndexes()
    {
        $indexes = scandir($this->repository);

        foreach ($indexes as $key => $index) {
            if ($index === "." || $index === ".." || !is_dir("$this->repository/$index")) {
                unset($indexes[$key]);
            }
        }

        return $indexes;
    }

    /**
     * Open an index
     * @param string $index The index name
     *
     * @return The index object
     */
    public function openIndex($index)
    {
        return \Zend_Search_Lucene::open($this->repository.DIRECTORY_SEPARATOR.$index);
    }

    /**
     * Add a document to an index
     * @param string   $index    The index name
     * @param Document $document The array of fields
     *
     * @return the result of the operation
     */
    public function addDocument($index, $document)
    {
        if (!$this->indexExists($index)) {
            $this->createIndex($index);
        }

        $index = $this->openIndex($index);

        $doc = new \Zend_Search_Lucene_Document();
        foreach ($document->fields as $field) {
            switch ($field->type) {
                // Stored & Indexed & ~Tokenized
                case 'Keyword':
                    $doc->addField(\Zend_Search_Lucene_Field::Keyword($field->name, $field->value, 'UTF-8'));
                    break;

                // unIndexed
                case 'UnIndexed':
                    $doc->addField(\Zend_Search_Lucene_Field::UnIndexed($field->name, $field->value, 'UTF-8'));
                    break;

                // UnStored
                case 'Binary':
                    $doc->addField(\Zend_Search_Lucene_Field::Binary($field->name, $field->value, 'UTF-8'));
                    break;

                case 'Text':
                    $doc->addField(\Zend_Search_Lucene_Field::Text($field->name, $field->value, 'UTF-8'));
                    break;

                case 'UnStored':
                    $doc->addField(\Zend_Search_Lucene_Field::UnStored($field->name, $field->value, 'UTF-8'));
                    break;

            }
        }

        // Add info about date, number, boolean stored fields
        if (isset($document->keywordTypes)) {
            $doc->addField(\Zend_Search_Lucene_Field::UnIndexed('__', json_encode($document->keywordTypes), 'UTF-8'));
        }

        $index->addDocument($doc);

        return true;
    }

    /**
     * Search in an index
     * @param string $query The query
     * @param mixed  $index The name of the index
     * @param int    $limit The max length of results
     *
     * @return array The list of document
     */
    public function find($query, $index = false, $limit = false)
    {
        $documents = [];

        $indexes = [];
        if (!$index) {
            $indexes = $this->getIndexes();
        } else if (is_array($index)) {
            $indexes = $index;
        } else {
            $indexes[] = $index;
        }

        if ($limit) {
            \Zend_Search_Lucene::setResultSetLimit($limit);
        }

        foreach ($indexes as $index) {
            try {
                $indexObject = $this->openIndex($index);

                $resultSet = $indexObject->find($query);

                foreach ($resultSet as $luceneHit) {
                    $document = new \dependency\fulltext\Hit();
                    $document->id = $luceneHit->id;
                    $document->score = $luceneHit->score;
                    $document->index = $index;

                    $luceneDocument = $luceneHit->getDocument();

                    $fieldNames = $luceneDocument->getFieldNames();

                    if ($keywordTypes = $luceneHit->{'__'}) {
                        $keywordTypes = (array) json_decode($keywordTypes, \JSON_OBJECT_AS_ARRAY);
                    }

                    foreach ($fieldNames as $name) {
                        if ($name == '__') {
                            continue;
                        }

                        $value = $luceneDocument->getFieldValue($name);

                        $luceneField = $luceneDocument->getField($name);
                        switch (true) {
                            case !$luceneField->isStored:
                                $type = 'null';
                                break;

                            case $luceneField->isBinary:
                                $type = 'binary';
                                break;


                            case $luceneField->isTokenized:
                                $type = 'text';
                                break;

                            case $luceneField->isIndexed:
                                $type = 'name';
                                if (isset($keywordTypes[$name])) {
                                    $type = $keywordTypes[$name];
                                    switch ($keywordTypes[$name]) {
                                        case 'number':
                                            $value = floatval(substr($value, 0, 16).'.'.substr($value, 16, 14));
                                            break;

                                        case 'date':
                                            $value = substr($value, 0, 4).'-'.substr($value, 4, 2).'-'.substr($value, 6, 2);
                                            break;
                                    }
                                }
                                break;

                            default:
                                $type = 'id';
                        }

                        $field = new \dependency\fulltext\Field($name, $value, $type);

                        $document->fields[] = $field;
                    }

                    $documents[] = $document;
                }
            } catch (\Exception $e) {

            }
        }

        // Sort by scores if multiple indexes
        if (count($indexes) > 1) {
            $scores = [];
            foreach ($documents as $pos => $luceneHit) {
                $scores[$pos] = $luceneHit->score;
            }

            array_multisort($scores, SORT_DESC, $documents);

            if ($limit) {
                $documents = array_slice($documents, 0, $limit);
            }
        }

        return $documents;
    }

    /**
     * Delete documents in an index
     * @param string              $index    The index name
     * @param fulltext/FtDocument $document The document to delete
     *
     * @return boolean The result of the operation
     */
    public function delete($index, $document)
    {
        $queryString = [];

        foreach ($document->fields as $field) {
            $queryString[] = $field->name.':"'.$field->value.'"';
        }

        $queryString = implode(' AND ', $queryString);
        $documents = $this->find($queryString, $index);

        $index = $this->openIndex($index);

        foreach ($documents as $doc) {
            $index->delete($doc->id);
        }

        return true;
    }
}
