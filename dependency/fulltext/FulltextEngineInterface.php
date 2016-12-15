<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of dependency fulltext.
 *
 * Dependency fulltext is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency fulltext is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency fulltext.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\fulltext;
/**
 * The full text engines interface
 */
interface FulltextEngineInterface
{
    /**
     * Constructor of lucene dependency
     * @param string $repository The indexes repository
     * @param array  $options    Additionnal options
     */
    public function __construct($repository, $options = null);

    /**
     * Create an index
     * @param string $index The index name
     *
     * @return The index object
     */
    public function createIndex($index);

    /**
     * Check if an index exists
     * @param string $index The index name
     *
     * @return boolean The result of the operation
     */
    public function indexExists($index);

    /**
     * Get the list of indexes
     *
     * @return array
     */
    public function getIndexes();

    /**
     * Add a document to an index
     * @param string       $index  The index name
     * @param object|array $fields The array of fields
     *
     * @return the result of the operation
     */
    public function addDocument($index, $fields);

    /**
     * Search in an index
     * @param string $query The query
     * @param mixed  $index The name of the index
     * @param int    $limit The length
     *
     * @return array The list of document
     */
    public function find($query, $index=false, $limit=false);

    /**
     * Delete a document in an index
     * @param string              $index    The index name
     * @param fulltext/FtDocument $document The document to delete
     *
     * @return boolean The result of the operation
     */
    public function delete($index, $document);
}
