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

namespace bundle\documentManagement\Controller;

/**
 * Document object
 *
 * @package DocumentManagement
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
class document
{

    protected $sdoFactory;
    public $digitalResourceController;
    protected $droid;
    protected $finfo;
    protected $jhove;
    protected $descriptionControllers;
    protected $currentDescriptionController;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;

        $this->digitalResourceController = \laabs::newController("digitalResource/digitalResource");
    }

    /**
     * New empty document
     *
     * @return documentManagement/archivalProfile The archival profile object
     */
    public function newDocument()
    {
        return \laabs::newInstance("documentManagement/document");
    }

    /**
     * create document
     * @param documentManagement/document $document The document object
     *
     * @return documentManagement/document The document object
     */
    public function create($document)
    {
        return $this->sdoFactory->create($document, 'documentManagement/document');
    }

    /**
     * update a document
     * @param documentManagement/document $document The document object
     *
     * @return documentManagement/document The document object
     */
    public function update($document)
    {
        return $this->sdoFactory->update($document, "documentManagement/document");
    }

    /**
     * Delete digital resource contents
     * Used for transactions where persistance of data is rollbacked
     * @param object $document
     */
    public function rollbackStorage($document)
    {
        $this->digitalResourceController->rollbackStorage($document->digitalResource);
    }

    /**
     * Use a digital resource cluster for storage
     * @param string $digitalResourceClusterId
     * @param string $mode
     * @param bool   $limit
     *
     * @return digitalResource/cluster
     */
    public function useDigitalResourceCluster($digitalResourceClusterId, $mode, $limit)
    {
        return $this->digitalResourceController->useCluster($digitalResourceClusterId, $mode, $limit);
    }

    /**
     * Get resource of document
     * @param object $document The document object
     */
    public function getContent($document)
    {
        $resource = $this->digitalResourceController->getOriginalResource($document->docId);
        $document->digitalResource = $this->digitalResourceController->retrieve($resource->resId);
    }

    /**
     * Get document by resource identifier
     * @param string $docId The document identifier
     * @param string $resId The resource identifier
     *
     * @return documentManagement/document The document object
     */
    public function getByResId($docId, $resId)
    {
        $document = $this->getById($docId, false);

        $document->digitalResource = $this->digitalResourceController->retrieve($resId);

        return $document;
    }

    /**
     * Get document by identifier
     * @param string $docId        The doc identifier
     * @param bool   $withContents Retrieve contents or only description
     *
     * @return documentManagement/document The document object
     */
    public function getById($docId, $withContents = true)
    {
        $document = $this->sdoFactory->read('documentManagement/document', $docId);
        $resource = $this->digitalResourceController->getOriginalResource($document->docId);

        if ($withContents) {
            $document->digitalResource = $this->digitalResourceController->retrieve($resource->resId);
        } else {
            $document->digitalResource = $this->digitalResourceController->info($resource->resId);
        }

        return $document;
    }

    /**
     * Store document + resource
     * @param object $document
     * @param string $digitalResourceClusterId
     * @param string $bucket
     */
    public function store($document, $digitalResourceClusterId, $bucket = null)
    {
        // First create digitalResource
        if (!isset($document->docId)) {
            $document->docId = \laabs::newId();
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            if (empty($document->digitalResource->docId)) {
                $document->digitalResource->docId = $document->docId;
            }

            $this->digitalResourceController->store($document->digitalResource, $digitalResourceClusterId, $bucket);

            $this->create($document);
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            $this->digitalResourceController->rollbackStorage($document->digitalResource);

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }
    }

    /**
     * Convert and store document + resource
     * @param digitalResource/digitalResource $resource The resource to convert
     * @param string                          $bucket
     *
     * @return object
     */
    public function convertDocument($resource, $bucket = null)
    {
        $convertedResource = $this->digitalResourceController->convert($resource);

        if ($convertedResource) {
            $originalResource = $resource;

            if ($originalResource->relatedResId != "") {
                $originalResource = $this->digitalResourceController->getOriginalResource($originalResource->docId);
            }

            $convertedResource->relationshipType = "isConversionOf";
            $convertedResource->relatedResId = $originalResource->resId;
            $convertedResource->docId = $originalResource->docId;

            $convertedResource = $this->digitalResourceController->store($convertedResource, $resource->clusterId, $bucket);

            return $convertedResource;
        }

        throw new \Exception('Conversion failed');
    }

    /**
     * Get documents by archive identifier
     * @param string $archiveId    The archive identifier
     * @param bool   $withContents Retrieve contents or only description
     *
     * @return documentManagement/document[] Array of document object
     */
    public function getArchiveDocuments($archiveId, $withContents = true)
    {
        $documents = $this->sdoFactory->find('documentManagement/document', "archiveId='$archiveId'");
        $documentWithResource = [];

        foreach ($documents as $document) {
            $documentWithResource[] = $this->getById($document->docId, $withContents);
        }

        return $documentWithResource;
    }

    /**
     * Get document by archive identifier and order : original or last copy
     * @param string $archiveId    The archive identifier
     * @param bool   $original     Get the original or the last copy
     * @param bool   $withContents Retrieve contents or only description
     *
     * @return documentManagement/document The document
     */
//    public function getArchiveDocument($archiveId, $original = false, $withContents = true)
//    {
//        // TODO toute la methode (je pense supprimer la méthode)
//        $requestedDocument = false;
//        if ($original) {
//            $documents = $this->sdoFactory->find('documentManagement/document', "archiveId='$archiveId' and copy=false");
//            if (count($documents)) {
//                $requestedDocument = $documents[0];
//            }
//        } else {
//            $documents = $this->sdoFactory->find('documentManagement/document', "archiveId='$archiveId' and type='CDO'");
//            if (count($documents)) {
//                foreach ($documents as $document) {
//                    $document->digitalResource = $this->digitalResourceController->info($document->resId);
//                    if (empty($requestedDocument)
//                        || ($document->digitalResource->created > $requestedDocument->digitalResource->created)
//                    ) {
//                        $requestedDocument = $document;
//                    }
//                }
//            }
//        }
//
//        if ($requestedDocument) {
//            if ($withContents) {
//                $requestedDocument->digitalResource = $this->digitalResourceController->retrieve($requestedDocument->resId);
//            } else {
//                $requestedDocument->digitalResource = $this->digitalResourceController->info($requestedDocument->resId);
//            }
//
//            $requestedDocument->documentRelationship = $this->sdoFactory->find('documentManagement/documentRelationship', "docId='$requestedDocument->docId'");
//
//            return $requestedDocument;
//        }
//    }

    /**
     * delete all archive documents
     * @param string $archiveId The archive identifier
     *
     * @return boolean The result of the request
     */
    public function deleteArchiveDocuments($archiveId)
    {
        $documents = $this->sdoFactory->find('documentManagement/document', "archiveId='$archiveId'");

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            foreach ($documents as $document) {
                $this->delete($document->docId);
            }
        } catch (\Exception $exception) {

            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return true;
    }

    /**
     * delete a document
     * @param string $docId The doc identifier
     *
     * @return boolean The result of the request
     */
    public function delete($docId)
    {
        $document = $this->sdoFactory->read('documentManagement/document', $docId);

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $resource = $this->digitalResourceController->getOriginalResource($document->docId);

            if ($resource) {
                $this->digitalResourceController->delete($resource->resId);
            }

            /*if (!empty($document->descriptionClass)) {
                $descriptionController = $this->useDescriptionController($document->descriptionClass);
                $descriptionController->delete($document->descriptionId);
            }*/

            $this->sdoFactory->deleteChildren("documentManagement/documentRelationship", $document);
            $deleted = $this->sdoFactory->delete($document);
        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $deleted;
    }
}
