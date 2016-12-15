<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle organization.
 *
 * Bundle organization is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle organization is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle organization.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\Presenter\organization;

/**
 * organization contact serializer
 *
 * @package Organization
 * @author  Maarch Alexis Ragot <alexis.ragot@maarch.org>
 */
class orgContact
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    protected $json;

    /**
     * Constructor
     * @param \dependency\html\Document   $view A new ready-to-use empty view
     * @param \dependency\json\JsonObject $json The json base object
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json)
    {
        $this->view = $view;

        $this->json = $json;
        $this->json->status = true;

        $this->translator = $this->view->translator;
        $this->translator->setCatalog('organization/messages');
    }

    /**
     * Serializer JSON for create method
     * @param organization/contact $orgContact The organization contact
     *
     * @return object JSON object with a status and message parameters
     */
    public function create($orgContact)
    {
        $this->json->message = "The contact has been added to the organization";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for read method
     * @param organization/contact $orgContact The organization contact
     *
     * @return object JSON object with a status and message parameters
     */
    public function read($orgContact)
    {
        return json_encode($orgContact);
    }

    /**
     * Serializer JSON for read method
     *
     * @return object JSON object with a status and message parameters
     */
    public function delete()
    {
        $this->json->message = "the contact has been remove to the organization";

        return $this->json->save();
    }
}
