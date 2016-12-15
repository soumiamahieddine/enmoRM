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
namespace bundle\recordsManagement\Parser\xml;

/**
 * Parser for XML archives
 */
class archive
{
    protected $xmlDocument;

    protected $xPath;

    /**
     * Document parser
     * @var object
     */
    protected $documentParser;

    /**
     * Previously loaded description parsers
     * @var array
     */
    protected $descriptionParsers;

    /**
     * Currently used archival agreement
     * @var object
     */
    protected $currentDescriptionParser;

    /**
     * Controller for archival profiles
     * @var recordsManagement/Controller/archivalProfile
     */
    protected $archivalProfileController;

    /**
     * Controller for service levels
     * @var recordsManagement/Controller/serviceLevel
     */
    protected $serviceLevelController;

    /**
     * Previously loaded archival profiles, indexed by reference
     * @var array
     */
    protected $archivalProfiles;

    /**
     * Currently used archival profile
     * @var recordsManagement/archivalProfile
     */
    protected $currentArchivalProfile;

    /**
     * Previously loaded archival agreements, indexed by reference
     * @var array
     */
    protected $archivalAgreements;

    /**
     * Currently used archival agreement
     * @var recordsManagement/archivalAgreement
     */
    protected $currentArchivalAgreement;

    /**
     * Previously loaded service levels, indexed by reference
     * @var array
     */
    protected $serviceLevels;

    /**
     * Currently used service level
     * @var recordsManagement/serviceLevel
     */
    protected $currentServiceLevel;

    /**
     * Constructor
     * @param \dependency\xml\Document $xmlDocument
     */
    public function __construct(\dependency\xml\Document $xmlDocument)
    {
        $this->xmlDocument = $xmlDocument;

        $this->documentParser = \laabs::newParser('documentManagement/document', 'xml');

        $this->archivalProfileController = \laabs::newController('recordsManagement/archivalProfile');

        $this->serviceLevelController = \laabs::newController("recordsManagement/serviceLevel");
    }

    /**
     * Select a description parser to use
     * @param string $descriptionClass
     *
     * @return object
     */
    public function useDescriptionParser($descriptionClass)
    {
        if (!isset($this->descriptionParsers[$descriptionClass])) {
            $this->currentDescriptionParser = \laabs::newParser($descriptionClass, 'xml');

            $this->descriptionParsers[$descriptionClass] = $this->currentDescriptionParser;
        } else {
            $this->currentDescriptionParser = $this->descriptionParsers[$descriptionClass];
        }

        return $this->currentDescriptionParser;
    }

    /**
     * Select an archival profile for use
     * @param string $archivalProfileReference
     *
     * @return recordsManagement/archivalProfile
     */
    public function useArchivalProfile($archivalProfileReference)
    {
        if (!isset($this->archivalProfiles[$archivalProfileReference])) {
            $this->currentArchivalProfile = $this->archivalProfileController->getByReference($archivalProfileReference);

            $this->archivalProfiles[$archivalProfileReference] = $this->currentArchivalProfile;
        } else {
            $this->currentArchivalProfile = $this->archivalProfiles[$archivalProfileReference];
        }

        return $this->currentArchivalProfile;
    }

    /**
     * Select an service level for use
     * @param string $serviceLevelReference
     *
     * @return recordsManagement/serviceLevel
     */
    public function useServiceLevel($serviceLevelReference=null)
    {
        if ($serviceLevelReference) {
            if (!isset($this->serviceLevels[$serviceLevelReference])) {
                $this->currentServiceLevel = $this->serviceLevelController->getByReference($serviceLevelReference);

                $this->serviceLevels[$serviceLevelReference] = $this->currentServiceLevel;
            } else {
                $this->currentServiceLevel = $this->serviceLevels[$serviceLevelReference];
            }
        } else {
            $this->currentServiceLevel = $this->serviceLevelController->getDefault();
            $this->serviceLevels[$this->currentServiceLevel->reference] = $this->currentServiceLevel;
        }

        return $this->currentServiceLevel;
    }

    /**
     * Parse Xml string into archive object
     * @param string $xml
     *
     * @return object
     */
    public function create($xml)
    {
        $this->xmlDocument->loadXml($xml);

        $this->xPath = new \DOMXPath($this->xmlDocument);
        $this->xPath->registerNamespace('recordsManagement', 'maarch.org:laabs:recordsManagement');
        $this->xPath->registerNamespace('documentManagement', 'maarch.org:laabs:documentManagement');

        $archive = \laabs::newInstance('recordsManagement/archive');
        $archive->archiveId = \laabs::newId();
        $archive->status = 'pending';

        $type = \laabs::getClass('recordsManagement/archive');
        $properties = $type->getProperties();

        foreach ($properties as $property) {
            if ($property->isScalar()) {
                $node = $this->xPath->query('recordsManagement:'.$property->name)->item(0);
                if ($node) {
                    $stringValue = $node->nodeValue;

                    if ($stringValue) {
                        $archive->{$property->name} = \laabs::cast($stringValue, $property->getType());
                    }
                }
            }
        }

        // Parse business specific description
        $descriptionObjectElement = $this->xPath->query("recordsManagement:descriptionObject/*")->item(0);
        $descriptionNamespace = $descriptionObjectElement->namespaceURI;

        $schema = \laabs::resolveXmlNamespace($descriptionNamespace);
        if (!$schema) {
            throw new \Exception('Unknown description schema '.$descriptionNamespace);
        }

        $archive->descriptionClass = $schema.LAABS_URI_SEPARATOR.$descriptionObjectElement->localName;
        $descriptionParser = \laabs::newParser($archive->descriptionClass, 'xml');

        $archive->descriptionObject = $descriptionParser->create($this->xmlDocument->saveXml($descriptionObjectElement));

        // Parse documents
        $documentElements = $this->xPath->query("documentManagement:document");
        if ($documentElements->length) {
            foreach ($documentElements as $documentElement) {

                $document = $this->documentParser->create($this->xmlDocument->saveXml($documentElement));
                $document->docId = \laabs::newId();
                $document->archiveId = $archive->archiveId;

                if ($documentElement->hasAttribute('oid')) {
                    $oid = $documentElement->getAttribute('oid');
                    $archive->document[$oid] = $document;

                } else {
                    $archive->document[] = $document;
                }

                if (!isset($document->type)) {
                    $document->type = "CDO";
                }
            }
        }

        // Parse relationships
        $relationshipElements = $this->xPath->query("recordsManagement:archiveRelationship");
        if ($relationshipElements->length) {
            foreach ($relationshipElements as $relationshipElement) {
                $archiveRelationship = \laabs::newInstance('recordsManagement/archiveRelationship');
                $archiveRelationship->archiveId = $archive->archiveId;

                $relatedArchiveIdElement = $this->xPath->query("recordsManagement:relatedArchiveId", $relationshipElement)->item(0);
                $archiveRelationship->relatedArchiveId = $relatedArchiveIdElement->nodeValue;

                $relationshipTypeElement = $this->xPath->query("recordsManagement:relationshipTypeCode", $relationshipElement)->item(0);
                $archiveRelationship->relationshipTypeCode = $relationshipTypeElement->nodeValue;

                $archive->archiveRelationship[] = $archiveRelationship;
            }
        }

        // parse sub-levels
        $subArchiveElements = $this->xPath->query("recordsManagement:archive");
        if ($subArchiveElements->length) {
            foreach ($subArchiveElements as $subArchiveElement) {
                $archive->contents[] = $this->create($this->xmlDocument->saveXml($subArchiveElement));
            }
        }

        return $archive;
    }

    /**
     * Parse Xml string into archive object
     * @param string $xml
     *
     * @return object
     */
    public function newArchive($xml)
    {
        $this->xmlDocument->loadXml($xml);
        $this->xPath = new \DOMXPath($this->xmlDocument);

        $archive = \laabs::newInstance('recordsManagement/archive');
        $archive->archiveId = \laabs::newId();
        $archive->timestamp = \laabs::newTimestamp();
        $archive->status = 'preserved';

        $type = \laabs::getClass('recordsManagement/archive');
        $properties = $type->getProperties();

        foreach ($properties as $property) {
            if ($property->isScalar()) {
                $node = $this->xPath->query("$property->name")->item(0);
                if ($node) {
                    $stringValue = $node->nodeValue;

                    if ($stringValue) {
                        $archive->{$property->name} = \laabs::cast($stringValue, $property->getType());
                    }
                }
            }
        }

        // Load references
        if (isset($archive->archivalProfileReference)) {
            $archivalProfile = $this->useArchivalProfile($archive->archivalProfileReference);

            if (!isset($archive->descriptionClass)) {
                $archive->descriptionClass = $archivalProfile->descriptionClass;
            }
            if (!isset($archive->descriptionSchema)) {
                $archive->descriptionSchema = $archivalProfile->descriptionSchema;
            }
        }

        if (isset($archive->archivalAgreementReference)) {
            $archivalAgreement = $this->useArchivalAgreement($archive->archivalAgreementReference);
        }

        if (isset($archive->serviceLevelReference)) {
            $serviceLevel = $this->useServiceLevel($archive->serviceLevelReference);
        }

        // Parse description
        $descriptionElement = $this->xPath->query("description")->item(0);
        $descriptionContents = $this->xmlDocument->saveXml($descriptionElement);
        if (isset($archive->descriptionClass)) {
            $descriptionParser = $this->useDescriptionParser($archive->descriptionClass);

            $archive->descriptionObject = $descriptionParser->create($descriptionContents);

            $archive->descriptionObject->archiveId = $archive->archiveId;
        }

        if (isset($archive->descriptionSchema)) {
            $archive->descriptionXml = \laabs::newXml($descriptionContents);
        }

        // Parse documents
        $documentElements = $this->xPath->query("document");
        if ($documentElements->length) {
            foreach ($documentElements as $documentElement) {
                $document = $this->documentParser->create($this->xmlDocument->saveXml($documentElement));
                $document->archiveId = $archive->archiveId;

                $archive->document[] = $document;
            }
        }

        // Certificates
        $certificates = array();
        $lifeCycleEvents = $this->xPath->query("lifeCycleEvents/*");
        if ($lifeCycleEvents->length) {
            $certificates["preprocess"] = array();

            foreach ($lifeCycleEvents as $certificate) {
                $xml = \laabs::newXml();
                $certificate = $xml->importNode($certificate);

                //$archiveId = $xml->createElement('archiveId', (string) $archive->archiveId);
                //$archiveNode = $xml->getElementsByTagName('archive')->item(0);
                //$archiveNode->appendChild($archiveId);

                if ($certificate->nodeName == "CertificateOfValidation") {
                    $certificates["CertificateOfValidation"] = $xml;
                } elseif ($certificate->nodeName == "CertificateOfConversion") {
                    $certificates["CertificateOfConversion"] = $xml;
                } else {
                    $certificates["preprocess"][] = $xml;
                }
            }
        }

        return array('archive' => $archive, 'lifeCycleEvents' => $certificates);
    }
}
