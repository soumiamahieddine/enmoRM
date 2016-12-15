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
namespace bundle\recordsManagement\Serializer\xml;

/**
 * Archive Xml serializer
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class archive
{

    protected $xml;

    protected $descriptionSerializers;

    protected $lifeCycleEventSerializer;

    /**
     * Constructor of message class
     * @param \dependency\xml\Document $xml
     */
    /*public function __construct(\dependency\xml\Document $xml)
    {
        $this->xml = $xml;
        $this->xml->formatOutput = true;
    }*/

    /**
     * Return new digital resource for an archive
     * @param recordsManagement/archive $archive
     * @param bool                      $includeContents
     *
     * @return string
     */
    public function restitute($archive, $includeContents = true)
    {
        $fragment = $this->xml->createDocumentFragment();
        $fragment->appendFile('recordsManagement/view/archive/restitution.xml');
        $this->xml->appendChild($fragment);

        $this->xml->setSource('archive', $archive);
        $this->xml->setSource('includeContents', $includeContents);

        $this->xml->merge();

        $descriptionXml = \laabs::callOutputRoute(
            'READ ' . $archive->descriptionClass . LAABS_URI_SEPARATOR . $archive->descriptionId,
            "xml",
            $archive->descriptionObject
        );

        $fragment = $this->xml->createDocumentFragment();
        $fragment->appendXml($descriptionXml);
        $this->xml->documentElement->appendChild($fragment);

        if ($includeContents) {
            foreach ($archive->document as $document) {
                $base64File = base64_encode($document->digitalResource->getContents());
                $document = $this->xml->createElement('document', $base64File);
                $this->xml->documentElement->appendChild($document);
            }
        }

        return $this->xml->saveXml();
    }
    
    /**
     * TODO
     * @param type $archive The archive object
     * 
     * @return string The XML contents
     */
    public function getDescription($archive, $parentElement)
    {
        $this->xml = $parentElement->ownerDocument;

        $description = $this->getDescriptionElement($archive, $parentElement);
        
        return $description;
    }
    
    protected function getDescriptionElement($archive, $parentElement)
    {
        $archiveElement = $this->xml->createElementNS('maarch.org:laabs:recordsManagement', 'archive');
        $archiveElement->setAttribute('xml:id', 'archiveId_' . $archive->archiveId);
        $parentElement->appendChild($archiveElement);

        // Description object
        if ($archive->descriptionClass && isset($archive->descriptionObject)) {
            if (!isset($this->descriptionSerializers[$archive->descriptionClass])) {
                $this->descriptionSerializers[$archive->descriptionClass] = \laabs::newSerializer($archive->descriptionClass, "xml");
            }

            $descriptionElement = $this->xml->createElement('descriptionObject');
            $archiveElement->appendChild($descriptionElement);

            $this->descriptionSerializers[$archive->descriptionClass]->read($archive->descriptionObject, $descriptionElement);
        }

        // Life cycle event
        if (isset($archive->lifeCycleEvent) && count($archive->lifeCycleEvent)) {
            if (!isset($this->lifeCycleEventSerializer)) {
                $this->lifeCycleEventSerializer = \laabs::newSerializer('lifeCycle/event', "xml");
            }

            $lifeCycleElement = $this->xml->createElement('lifeCycle');
            $archiveElement->appendChild($lifeCycleElement);

            foreach ($archive->lifeCycleEvent as $event) {
                $eventXML = $this->lifeCycleEventSerializer->read($event, $lifeCycleElement);
            }
        }

        // Documents
        if ($archive->document) {
            foreach ($archive->document as $document) {
                //if ($document->type == 'CDO') {
                    $documentElement = $this->xml->createElement('document');
                    $documentElement->setAttribute('xml:id', 'docId_'.$document->docId);
                    $documentElement->setAttribute('oid', $document->digitalResource->resId);
                    $archiveElement->appendChild($documentElement);
                //}
            }
        }

        // Contents
        if (isset($archive->contents) && count($archive->contents)) {
            foreach ($archive->contents as $subArchive) {
                $this->getDescriptionElement($subArchive, $archiveElement);
            }
        }
    }
}
