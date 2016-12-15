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
namespace bundle\contact;

/**
 * Interface for contact management
 */
interface contactInterface
{
    /*
        CONTACT
    */
    /**
     * Get the index of persons
     *
     * @return array The list of persons
     *
     * @action contact/contact/index
     */
    public function readIndex();

    /**
     * Add a new contact
     * @param contact/contact $contact The contact to record
     *
     * @return string The id of the new contact
     *
     * @action contact/contact/add
     */
    public function create($contact);

    /**
     * Get a contact by its id
     * @return contact/contact The contact
     *
     * @action contact/contact/get
     */
    public function read_contactId_();

    /**
     * Modify a contact
     * @param contact/contact $contact The the contact to modify
     *
     * @return contact/contact
     *
     * @action contact/contact/modify
     */
    public function update_contactId_($contact);

    /**
     * Delete a contact
     *
     * @return bool
     *
     * @action contact/contact/delete
     */
    public function delete_contactId_();

    /*
        CONTACT ADDRESS
    */
    /**
     * Get a contact addresses
     *
     * @return contact/address[] The address
     *
     * @action contact/contact/getAddresses
     */
    public function read_contactId_Address();

    /**
     * Add a new address
     * @param contact/address $address The address to record
     *
     * @return contact/address $address The address recorded
     *
     * @action contact/contact/addAddress
     */
    public function create_contactId_Address($address);

    /**
     * get an address
     *
     * @return contact/address The address
     *
     * @action contact/contact/getAddress
     */
    public function readAddress_addressId_();

    /**
     * Modify an address
     * @param contact/address $address The address to modify
     *
     * @return bool
     *
     * @action contact/contact/modifyAddress
     */
    public function updateAddress_addressId_($address);

    /**
     * Delete an address
     *
     * @return bool
     *
     * @action contact/contact/deleteAddress
     */
    public function deleteAddress_addressId_();

    /*
        CONTACT COMMUNICATION
    */
    /**
     * Record a new communication
     * @param contact/communication $communication The communication to record
     *
     * @return contact/communication $communication The communication recorded
     *
     * @action contact/contact/addCommunication
     */
    public function create_contactId_Communication($communication);

    /**
     * Get all communications
     *
     * @return contact/communication[] The communications
     *
     * @action contact/contact/getCommunications
     */
    public function read_contactId_Communication();

    /**
     * Get communications for a mean
     *
     * @return contact/communication[] The communications
     *
     * @action contact/contact/getCommunicationsByMean
     */
    public function read_contactId_Communication_comMeanCode_();


    /**
     * Get a communication
     *
     * @return contact/communication The communication
     *
     * @action contact/contact/getCommunication
     */
    public function readCommunication_communicationId_();

    /**
     * Modify a communication
     * @param contact/communication $communication The communication value
     *
     * @return contact/communication The communication
     *
     * @action contact/contact/modifyCommunication
     */
    public function updateCommunication_communicationId_($communication);

    /**
     * Delete a communication
     *
     * @return bool
     *
     * @action contact/contact/deleteCommunication
     */
    public function deleteCommunication_communicationId_();
}
