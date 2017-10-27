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
 * Contact controller
 *
 * @package Contact
 * @author  Cyril VAZQUEZ <cyril.vazquez@maarch.org>
 */
class contact
{
    use contactAddressTrait,
        contactCommunicationTrait;

    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The dependency Sdo Factory object
     *
     * @return void
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get the index of persons
     *
     * @return array The list of persons
     */
    public function index()
    {
        $contacts = $this->sdoFactory->find('contact/contact');

        return $contacts;
    }

    /**
     * Record a new contact
     * @param contact/contact $contact The contact to record
     *
     * @return id The id of the new contact
     */
    public function add($contact)
    {
        $contact->contactId = \laabs::newId();

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $this->sdoFactory->create($contact, "contact/contact");
            $contactId = (string) $contact->contactId;

            if (count($contact->address)) {
                foreach ($contact->address as $address) {
                    $this->addAddress($contactId, $address);
                }
            }

            if (count($contact->communication)) {
                foreach ($contact->communication as $communication) {
                    $communication->contactId = $contactId;
                }
                $this->sdoFactory->createCollection($contact->communication, "contact/communication");
            }

        } catch (\Exception $e) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $contact->contactId;
    }

    /**
     * Edit a party
     * @param id $contactId The Id of the contact
     *
     * @return contact/contact The contact
     */
    public function get($contactId)
    {
        $contact = $this->sdoFactory->read("contact/contact", $contactId);

        if ($contact != null) {
            $contact->address =  $this->sdoFactory->readChildren("contact/address", $contact);
            $contact->communication = $this->sdoFactory->readChildren("contact/communication", $contact);
            foreach ($contact->communication as $com){
                $com->comMeanName = $this->sdoFactory->index("contact/communicationMean", "name", "code = '$com->comMeanCode'")[$com->comMeanCode];
            }
        }
        return $contact;
    }

    /**
     * Modify a contact
     * @param id              $contactId The contact identifier
     * @param contact/contact $contact   The the contact to modify
     *
     * @return boolean
     */
    public function modify($contactId, $contact)
    {
        $contact->contactId = $contactId;

        $this->sdoFactory->update($contact, 'contact/contact');

        return $contact;
    }

    /**
     * Delete a contact
     * @param id $contactId The the contact id to delete
     *
     * @return bool
     */
    public function delete($contactId)
    {
        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        try {
            $this->sdoFactory->deleteChildren("contact/address", array('contactId' => $contactId), 'contact/contact');

            $this->sdoFactory->deleteChildren("contact/communication", array('contactId' => $contactId), 'contact/contact');

            $result = $this->sdoFactory->delete($contactId, "contact/contact");

        } catch (\Exception $e) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw \laabs::newException("contact/sdoException");
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $result;
    }
}
