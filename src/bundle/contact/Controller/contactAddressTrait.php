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
 * Trait for contact address control
 *
 * @package Contact
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 */
trait contactAddressTrait
{
    /**
     * Record a new address
     * @param id              $contactId The contact to add address to
     * @param contact/address $address   The address to record
     *
     * @return contact/address $address  The address to record
     */
    public function addAddress($contactId, $address)
    {
        $address->contactId = $contactId;

        try {
            $checkAddress = $this->sdoFactory->read('contact/address', array('purpose' => $address->purpose, 'contactId' => $contactId));

        } catch (\Exception $e) {
            $address->addressId = \laabs::newId();
            $this->sdoFactory->create($address, "contact/address");
            
            return $address;
        }

        throw \laabs::newException('contact/contactException', 'An address with this purpose already exist.');

    }

    /**
     * Get addresses
     * @param id $contactId The Id of the contact
     *
     * @return contact/address[] The addresses
     */
    public function getAddresses($contactId)
    {
        $addresses = $this->sdoFactory->readChildren("contact/address", array('contactId' => $contactId), "contact/contact");

        return \laabs::castMessageCollection($addresses, "contact/address");
    }

    /**
     * Get an address
     * @param string $addressId The Id of the address
     *
     * @return contact/address The address
     */
    public function getAddress($addressId)
    {
        return $this->sdoFactory->read("contact/address", array('addressId' => $addressId));

        return \laabs::castMessage($address, "contact/address");
    }

    /**
     * Modify an address
     * @param id              $addressId The address id
     * @param contact/address $address   The address data
     *
     * @return bool
     */
    public function modifyAddress($addressId, $address)
    {
        $address->addressId = $addressId;

        $this->sdoFactory->update($address, 'contact/address');

        return $address;
    }

    /**
     * Delete an address
     * @param id $addressId The address id
     *
     * @return bool
     */
    public function deleteAddress($addressId)
    {
        $key = array('addressId' => $addressId);

        $result =  $this->sdoFactory->delete($key, "contact/address");

        return $result;
    }
}
