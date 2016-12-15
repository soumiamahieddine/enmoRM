<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\recordsManagement\Controller;

/**
 * Fulltext controller
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class fulltext
{

    protected $fulltextEngine;

    /**
     * Constructor of fulltext controller
     */
    public function __construct()
    {
        if (!\laabs::hasDependency('fulltext')) {
            // todo exception
        }

        $this->fulltextEngine = \laabs::newService('dependency/fulltext/FulltextEngineInterface');
        $this->archivalProfileController = \laabs::newController('recordsManagement/archivalProfile');
    }

    /**
     * Archive indexing
     * @param recordsManagement/archive $archive The archive object
     *
     * @return \dependency\fulltext\Document The document index with the indexing archive
     */
    public function getArchiveIndex($archive)
    {
        $archiveIndex = \laabs::newService('dependency/fulltext/Document');

        $descriptionFields = $this->archivalProfileController->getArchiveDescriptionFields();
        $this->addFields($archive, $descriptionFields, $archiveIndex);

        return $archiveIndex;
    }

    /**
     * Update archive index
     * @param object $index Indexes of archive
     *
     * @return boolean The result of the archive indexing
     */
    public function updateArchiveIndex($index)
    {
        $this->validateDescriptionFields($index);

        $category = "";
        $documentToUpdate = \laabs::newService('dependency/fulltext/Document');
        $documentToRemove = clone($documentToUpdate);

        foreach ($index as $key => $field) {
            $documentToUpdate->addField($field->name, $field->value, $field->type);

            if ($field->name == "archiveId") {
                $documentToRemove->addField($field->name, $field->value, $field->type);
            }

            $category = $field->name == "category" ? $field->value : "";
        }

        if ($category == "" || count($documentToRemove->fields) < 1) {
            return false;
        }

        $this->delete($category, $documentToRemove);

        $this->addDocument($category, $documentToUpdate);

        return true;
    }

    /**
     * Fulltext indexing
     * @param array                         $descriptionObject The document management object to index
     * @param \dependency\fulltext\Document $baseIndex         The document index
     *
     * @return bool
     */
    public function mergeIndex($descriptionObject, $baseIndex)
    {
        foreach ($descriptionObject as $field) {
            // todo validate before
            $baseIndex->addField($field->name, $field->value, $field->type);
        }
    }

    /**
     * Fulltext archive indexing
     * @param documentManagement/document   $document  The document management object to index
     * @param \dependency\fulltext\Document $baseIndex The document index
     *
     * @return \dependency\fulltext\Document The document index with the indexing fulltext archive
     */
    public function getDocumentIndex($document, $baseIndex = null)
    {
        if (empty($baseIndex)) {
            $documentIndex = \laabs::newService('dependency/fulltext/Document');
        } else {
            $documentIndex = clone($baseIndex);
        }

        $descriptionFields = $this->archivalProfileController->getDocumentDescriptionFields();
        $this->addFields($document, $descriptionFields, $documentIndex);

        $this->mergeIndex($document->descriptionObject, $documentIndex);

        return $documentIndex;
    }

    /**
     * Fulltext indexing
     * @param string                        $index    The fulltext index
     * @param \dependency\fulltext\Document $document The fulltext document
     *
     * @return bool
     */
    public function addDocument($index, $document)
    {
        return $this->fulltextEngine->addDocument($index, $document);
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
        return $this->fulltextEngine->delete($index, $document);
    }

    /**
     * Add fields
     * @param object                        $object            The source
     * @param array                         $descriptionFields The names and types
     * @param \dependency\fulltext\Document $document          The names and types
     */
    public function addFields($object, $descriptionFields, $document)
    {
        foreach ($descriptionFields as $descriptionField) {
            $name = $descriptionField->name;
            $type = $descriptionField->type;
            if (!empty($object->{$name})) {
                $document->addField($name, $object->{$name}, $type);
            }
        }
    }

    /**
     * Check required fields
     * @param string $documentProfileReference The document profile
     * @param array  $fulltext                 The fulltext array
     */
    public function checkRequiredFields($documentProfileReference, $fulltext)
    {
        $archivalProfileController = \laabs::newController("recordsManagement/archivalProfile");
        $documentProfile = $archivalProfileController->getDocumentProfileByReference($documentProfileReference);

        $indexedFulltext = [];
        foreach ($fulltext as $field) {
            $indexedFulltext[$field->name] = $field;
        }

        $errors = [];
        foreach ($documentProfile->documentDescription as $description) {
            if (!$description->required) {
                continue;
            }

            if (!isset($indexedFulltext[$description->fieldName]) || strlen($indexedFulltext[$description->fieldName]->value) == 0) {
                $errors[] = new \core\Error('The field \'%1$s\' is required', array($description->fieldName));
            }
        }

        if (count($errors) > 0) {
            $exception = new \core\Exception("Validation error");
            $exception->errors = $errors;
            throw $exception;
        }
    }

    /**
     * Validate a fulltext description
     * @param array $fulltext The fulltext array
     *
     * @throws Exception
     */
    public function validateDescriptionFields($fulltext)
    {
        $errors = [];

        foreach ($fulltext as $field) {
            switch ($field->type) {
                case "name":
                    $result = $this->validateName($field);
                    break;

                case "boolean":
                    $result = $this->validateBoolean($field);
                    break;

                case "number":
                    $result = $this->validateNumber($field);
                    break;

                case "date":
                    $result = $this->validateDate($field);
                    break;

                case "text":
                    $result = true;
                    break;

                default:
                    break;
            }

            if ($result != true) {
                $errors[] = new \core\Error('The value of \'%1$s\' not respect the type \'%2$s\'', array($field->name, $field->type));
            }
        }

        if (count($errors) > 0) {
            $exception = new \core\Exception("Validation error");
            $exception->errors = $errors;
            throw $exception;
        }
    }

    /**
     * Validate a fulltext description of type name
     * @param string $field The value to validate
     *
     * @return boolean
     */
    private function validateName($field)
    {
        return true;
        /*$result = preg_match('#^[A-Za-z_][A-Za-z0-9_]*$#', $field->value) > 0 ? true : false;

        return $result;*/
    }

    /**
     * Validate a fulltext description of type boolean
     * @param string $field The value to validate
     *
     * @return boolean
     */
    private function validateBoolean($field)
    {
        switch ($field->value) {
            case true:
            case false:
            case "0":
            case "1":
            case "true":
            case "false":
                $result = true;
                break;

            default:
                $result = false;
                break;
        }

        return $result;
    }

    /**
     * Validate a fulltext description of type number
     * @param string $field The value to validate
     *
     * @return boolean
     */
    private function validateNumber($field)
    {
        $result = preg_match('#^[0-9]*([.]?[0-9]*)?$#', $field->value) > 0 ? true : false;

        return $result;
    }

    /**
     * Validate a fulltext description of type date
     * @param string $field The value to validate
     *
     * @return boolean
     */
    private function validateDate($field)
    {
        $result = false;

        if (\laabs::newDate($field->value) instanceof \core\Type\Date) {
            $result = true;
        }

        return $result;
    }
}
