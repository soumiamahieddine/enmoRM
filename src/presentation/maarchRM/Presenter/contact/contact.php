<?php

/*
 * This file is part of the contact package.
 *
 * (c) Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace presentation\maarchRM\Presenter\contact;

/**
 * Bundle contact html serializer
 *
 * @package Contact
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class contact
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;
    public $view;
    protected $publicArchives;

    /**
     * __construct
     *
     * @param \dependency\html\Document $view the view
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator, $publicArchives=false)
    {
        $this->view = $view;
        $this->json = $json;
        $this->translator = $translator;
        $this->translator->setCatalog('contact/contact');
        $this->json->status = true;
        $this->publicArchives = $publicArchives;
    }

    /**
     * Print the index contact
     * @param array $contacts Array of contacts
     *
     * @return string View with the contact list
     */
    public function index($contacts)
    {
        $view = $this->view;

        $view->addContentFile("contact/manageContact/index.html");

        $table = $view->getElementById("listContacts");
        $dataTable = $table->plugin['dataTable'];
        $dataTable->setPaginationType("full_numbers");
        $dataTable->setUnsortableColumns(2);
        $dataTable->setUnsearchableColumns(2);

        $view->setSource("contacts", $contacts);
        $view->merge();
        $this->view->translate();

        return $view->saveHtml();
    }

    /**
     * Add a new contact
     * @param object $contact The new contact object of parties
     *
     * @return view View with the contact list
     */
    public function newContact($contact = null)
    {
        $view = $this->view;

        $view->addContentFile("contact/manageContact/contactForm.html");

        if ($contact) {
            if ($contact->address) {
                foreach ($contact->address as $address) {
                    $address->json = json_encode($address);
                }
            }

            if ($contact->communication) {
                foreach ($contact->communication as $communication) {
                    $communication->json = json_encode($communication);
                }
            }
        }

        $communicationMeans = \laabs::callService("contact/communicationMean/readIndex");

        $view->setSource("publicArchives", $this->publicArchives);
        $view->setSource("contact", $contact);
        $view->setSource("communicationMeans", $communicationMeans);
        //$view->setSource("countries", $countries);
        $view->merge();
        $view->translate();

        return $this->view->saveHtml();
    }

    /**
     * Serializer json for create method
     *
     * @return result object
     */
    public function create()
    {
        $this->json->message = $this->translator->getText("The contact has been created.");

        return $this->json->save();
    }

    /**
     * Serializer json for update method
     *
     * @return result object
     */
    public function update()
    {
        $this->json->message = $this->translator->getText("The contact has been updated.");

        return $this->json->save();
    }

    /**
     * Serializer json for delete method
     *
     * @return result object
     */
    public function delete()
    {
        $this->json->message = $this->translator->getText("The contact has been deleted.");

        return $this->json->save();
    }

    /**
     * Result message from adding an address
     * @param contact/address $address The created address
     *
     * @return result object
     */
    public function createAddress($address)
    {
        $this->json->message = $this->translator->getText("The address has been added.");
        $this->json->addressId = $address->addressId;

        return $this->json->save();
    }

    /**
     * Serializer JSON for read an address
     * @param contact/address $address The address
     *
     * @return string Address in JSON
     */
    public function readAddress($address)
    {
        return json_encode($address);
    }

    /**
     * Serializer JSON for update an address
     * @param contact/address $address The address
     *
     * @return result object
     */
    public function updateAddress($address)
    {
        $this->json->message = $this->translator->getText("The address has been modified.");
        $this->json->addressId = $address->addressId;

        return $this->json->save();
    }

    /**
     * Result message from deleting an address
     * @return result object
     */
    public function deleteAddress()
    {
        $this->json->message = $this->translator->getText("The address has been deleted.");

        return $this->json->save();
    }

    /**
     * Serializer JSON for communication create method
     * @param contact/communication $communication The communication created
     *
     * @return result object
     */
    public function createCommunication($communication)
    {
        $this->json->message = $this->translator->getText("The communication has been added.");
        $this->json->communicationId = $communication->communicationId;

        return $this->json->save();
    }

    /**
     * Serializer JSON for communication read method
     * @param contact/communication $communication The communication
     *
     * @return object JSON object with a status and message parameters
     */
    public function readCommunication($communication)
    {
        return json_encode($communication);
    }

    /**
     * Serializer JSON for communication update method
     * @param contact/communication $communication The communication updated
     *
     * @return result object
     */
    public function updateCommunication($communication)
    {
        $this->json->message = $this->translator->getText("The communication has been modified.");
        $this->json->communicationId = $communication->communicationId;

        return $this->json->save();
    }

    /**
     * Result message from deleting a communication
     *
     * @return result object
     */
    public function deleteCommunication()
    {
        $this->json->message = $this->translator->getText("The communication has been deleted.");

        return $this->json->save();
    }

    /**
     * Exception
     * @param contact/Exception/contactException $contactException
     * 
     * @return string
     */
    public function contactException($contactException)
    {
        //$this->json->load($contactException);
        $this->json->message = $this->translator->getText($contactException->getMessage());
        $this->json->status = false;

        return $this->json->save();
    }
}
