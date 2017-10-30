<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\UserStory\adminFunc;

/**
 * Trait contact
 */
interface contact
{
    /**
     * Create contact
     * @param contact/contact $contact The contact object to create
     *
     * @return contact/contact/create
     * @uses contact/contact/create
     */
    public function createContact($contact);

    /**
     * Get form to add a new contact
     *
     * @return contact/contact/newContact
     */
    public function readContact();

    /**
     * Read contact
     *
     * @return contact/contact/newContact
     * @uses contact/contact/read_contactId_
     */
    public function readContact_contactId_();

    /**
     * Update a contact
     * @param contact/contact $contact The contact object to update
     *
     * @return contact/contact/update
     * @uses contact/contact/update_contactId_
     */
    public function updateContact_contactId_($contact);

    /**
     * Delete a contact
     *
     * @return contact/contact/delete
     * @uses contact/contact/delete_contactId_
     */
    public function deleteContact_contactId_();



    /**
     * Create a contact address
     * @param contact/address $address The address to add
     *
     * @return contact/contact/createAddress
     * @uses contact/contact/create_contactId_Address
     */
    public function createContact_contactId_Address($address);

    /**
     * Get an address
     *
     * @return contact/contact/readAddress
     * @uses contact/contact/readAddress_addressId_
     */
    public function readContactaddress_addressId_();

    /**
     * Modify an address
     * @param contact/address $address The address to update
     *
     * @return contact/contact/updateAddress
     * @uses contact/contact/updateAddress_addressId_
     */
    public function updateContactaddress_addressId_($address);

    /**
     * Delete an address
     *
     * @return contact/contact/deleteAddress
     * @uses contact/contact/deleteAddress_addressId_
     */
    public function deleteContactaddress_addressId_();
    
    /**
     * Record a new communication
     * @param contact/communication $communication The communication to record
     *
     * @return contact/contact/createCommunication
     * @uses contact/contact/create_contactId_Communication
     */
    public function createContact_contactId_communication($communication);

    /**
     * Get a communication
     *
     * @return contact/contact/readCommunication
     * @uses contact/contact/readCommunication_communicationId_
     */
    public function readContactcommunication_communicationId_();

    /**
     * Modify a communication
     * @param contact/communication $communication The communication to modify
     *
     * @return contact/contact/updateCommunication
     * @uses contact/contact/updateCommunication_communicationId_
     */
    public function updateContactcommunication_communicationId_($communication);

    /**
     * Delete a communication
     *
     * @return contact/contact/deleteCommunication
     * @uses contact/contact/deleteCommunication_communicationId_
     */
    public function deleteContactcommunication_communicationId_();
}