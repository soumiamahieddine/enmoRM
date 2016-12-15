<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle lifeCycle.
 *
 * Bundle lifeCycle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle lifeCycle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle lifeCycle.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\lifeCycle\Serializer\xml;
/**
 * accounting record Xml serializer
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class event
{

    protected $xml;

    protected $sdoFactory;

    protected $eventFormats;

    /**
     * Constructor of accountingRecord class
     * @param \dependency\sdo\Factory $sdoFactory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;

        $this->eventFormats = $this->sdoFactory->index('lifeCycle/eventFormat');
        foreach ($this->eventFormats as $eventFormat) {
            $eventFormat->format = explode(' ', $eventFormat->format);
        }
    }

    /**
     * Serialize accounting record as XML
     * @param lifeCycle/event $event
     * @param DOMElement      $parentNode
     *
     * @return string
     */
    public function read($event, $parentNode)
    {
        $this->xml = $parentNode->ownerDocument;

        $eventType = \laabs\basename((string) $event->eventType);
        $objectClass = \laabs\basename((string) $event->objectClass);
        $eventNode = $this->xml->createElement($eventType);

        $eventNode->setAttribute('xml:id', 'eventId_' . (string) $event->eventId);
        
        $eventNode->setAttribute('date', (string) $event->timestamp);
        $eventNode->setAttribute('accountId', (string) $event->accountId);
        $eventNode->setAttribute('objectClass', (string) $objectClass);
        $eventNode->setAttribute('objectId', (string) $event->objectId);
        $eventNode->setAttribute('operationResult', (string) $event->operationResult);
        $eventNode->setAttribute('description', (string) $event->description);
        if (isset($event->hashAlgorithm)) {
            $eventNode->setAttribute('hashAlgorithm', (string) $event->hashAlgorithm);
        }
        if (isset($event->hash)) {
            $eventNode->setAttribute('hash', (string) $event->hash);
        }
        if (isset($event->address)) {
            $eventNode->setAttribute('address', (string) $event->address);
        }
        if (isset($event->originatorOrgRegNumber)) {
            $eventNode->setAttribute('originatorOrgRegNumber', (string) $event->originatorOrgRegNumber);
        }
        if (isset($event->depositorOrgRegNumber)) {
            $eventNode->setAttribute('depositorOrgRegNumber', (string) $event->depositorOrgRegNumber);
        }
        if (isset($event->archiverOrgRegNumber)) {
            $eventNode->setAttribute('archiverOrgRegNumber', (string) $event->archiverOrgRegNumber);
        }

        if (isset($this->eventFormats[$event->eventType])) {
            $eventFormat = $this->eventFormats[$event->eventType];
            foreach ($eventFormat->format as $i => $infoName) {
                if (isset($event->{$infoName})) {
                    $infoValue = $event->{$infoName};
                    $infoElement = $this->xml->createElement($infoName, (string) $infoValue); 
                    $eventNode->appendChild($infoElement); 
                }
            }
        }

        $parentNode->appendChild($eventNode);
    }

}
