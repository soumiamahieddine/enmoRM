<?php

/*
 * Copyright (C) 2019 Maarch
 *
 * This file is part of bundle mades.
 *
 * Bundle mades is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle mades is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle mades.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\mades\Controller;

/**
 * Class abstractMessage
 *
 * @package mades
 *
 */
abstract class abstractMessage
{
    protected function send($message)
    {
        $madesMessage = new \stdClass();

        $message->object = $madesMessage;

        $madesMessage->id = (string) $message->messageId;

        $madesMessage->date = $message->date;

        $madesMessage->comment = $message->comment;

        return $madesMessage;
    }

    protected function sendOrganization($orgOrganization)
    {
        $organization = \laabs::newInstance('mades/organization');
        $organization->id = (string) $orgOrganization->orgId;
        $organization->identifier = $orgOrganization->registrationNumber;
        $organization->name = $orgOrganization->orgName;

        if (isset($orgOrganization->address)) {
            foreach ($orgOrganization->address as $address) {
                $organization->address[] = $this->sendAddress($address);
            }
        }

        if (isset($orgOrganization->communication)) {
            foreach ($orgOrganization->communication as $communication) {
                $organization->communication[] = $this->sendCommunication($communication);
            }
        }

        if (isset($orgOrganization->contact)) {
            foreach ($orgOrganization->contact as $contact) {
                $organization->contact[] = $this->sendContact($contact);
            }
        }

        return $organization;
    }

    protected function sendAddress($orgAddress)
    {
        $address = \laabs::newInstance('contact/address');
        $address->id = (string) $orgAddress->addressId;
        $address->blockName = $orgAddress->block;
        $address->buildingName = $orgAddress->building;
        $address->buildingNumber = $orgAddress->number;
        $address->cityName = $orgAddress->city;
        $address->citySubDivisionName = $orgAddress->citySubDivision;
        $address->country = $orgAddress->country;
        $address->floorIdentification = $orgAddress->floor;
        $address->postCode = $orgAddress->postCode;
        $address->postOfficeBox = $orgAddress->postBox;
        $address->roomIdentification = $orgAddress->room;
        $address->streetName = $orgAddress->street;

        return $address;
    }

    protected function sendCommunication($orgCommunication)
    {
        $communication = \laabs::newInstance('contact/communication');
        $communication->id = (string) $orgCommunication->communicationId;
        $communication->channel = $orgCommunication->comMeanCode;

        switch ($orgCommunication->comMeanCode) {
            case 'EM':
            case 'FTP':
                $communication->URIID = $orgCommunication->value;
                break;

            default:
                $communication->completeNumber = $orgCommunication->value;
        }

        return $communication;
    }

    protected function sendContact($orgContact)
    {
        $contact = \laabs::newInstance('contact/contact');
        $contact->id = (string) $orgContact->contactId;
        $contact->departmentName = $orgContact->service;
        $contact->identification = $orgContact->contactId;
        $contact->personName = $orgContact->displayName;
        $contact->responsibility = $orgContact->function;

        if (isset($orgContact->address)) {
            foreach ($orgContact->address as $address) {
                $contact->address[] = $this->sendAddress($address);
            }
        }

        if (isset($orgContact->communication)) {
            foreach ($orgContact->communication as $communication) {
                $contact->communication[] = $this->sendCommunication($communication);
            }
        }

        return $contact;
    }

    protected function sendUnitIdentifiers($message) {
        if (isset($message->unitIdentifier)) {
            foreach ($message->unitIdentifier as $unitIdentifier) {
                $message->object->unitIdentifier[] = $unitIdentifier->objectId;
            }
        }
    }
}