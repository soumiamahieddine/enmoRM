<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle contact.
 *
 * Bundle contact is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle contact is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle contact.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\contact\Controller;
/**
 * Trait for contact communication control
 *
 * @package Contact
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 */
trait contactCommunicationTrait
{
    /**
     * Record a new communication
     * @param id                    $contactId     The contact identifier
     * @param contact/communication $communication The communication to record
     *
     * @return contact/communication $communication The communication recorded
     */
    public function addCommunication($contactId, $communication)
    {
        $communication->contactId = $contactId;

        try {
            $checkcommunication = $this->sdoFactory->read('contact/communication', array('purpose' => $communication->purpose, 'comMeanCode' => $communication->comMeanCode, 'contactId' => $contactId));

        } catch (\Exception $e) {
            $this->sdoFactory->create($communication, "contact/communication");

            return $communication;
        }

        throw \laabs::newException('contact/contactException', 'A communication mean with this purpose already exist.');

    }

    /**
     * Get communications
     * @param id $contactId The Id of the contact
     *
     * @return contact/communication[] The communications
     */
    public function getCommunications($contactId)
    {
        $communications = $this->sdoFactory->readChildren("contact/communication", array('contactId' => $contactId), "contact/contact");

        return \laabs::castMessageCollection($communications, 'contact/communication');
    }

    /**
     * Get communications for a given mean
     * @param id     $contactId   The Id of the contact
     * @param string $comMeanCode The requested communication mean
     *
     * @return contact/communication[] The communications
     */
    public function getCommunicationsByMean($contactId, $comMeanCode)
    {
        $args = array(
            'contactId' => $contactId,
            'comMeanCode' => $comMeanCode,
        );

        return $this->sdoFactory->find("contact/communication", "contactId=:contactId and comMeanCode=:comMeanCode", $args);
    }

    /**
     * Get a communication
     * @param id $communicationId The Id of the communication
     *
     * @return contact/communication The communication
     */
    public function getCommunication($communicationId)
    {
        $key = array('communicationId' => $communicationId);

        return $this->sdoFactory->read("contact/communication", $key);
    }

    /**
     * Modify a communication
     * @param id                    $communicationId The Id of the communication
     * @param contact/communication $communication   The communication to modify
     *
     * @return contact/communication $communication   The communication modified
     */
    public function modifyCommunication($communicationId, $communication)
    {
        $communication->communicationId = $communicationId;

        $this->sdoFactory->update($communication, "contact/communication");

        return $communication;
    }

    /**
     * Delete a communication
     * @param id $communicationId The Id of the communication
     *
     * @return bool The result of the operation
     */
    public function deleteCommunication($communicationId)
    {
        $key = array('communicationId' => $communicationId);

        return $this->sdoFactory->delete($key, "contact/communication");
    }
}
