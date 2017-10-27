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
namespace bundle\contact\Model;

/**
 * Communications of contacts
 *
 * @package Contact
 *
 * @pkey [communicationId]
 * @key  [contactId, purpose, comMeanCode]
 * @fkey [contactId] contact/contact[contactId]
 * @fkey [comMeanCode]  contact/communicationMean[code]
 * 
 * @xmlns contact maarch.org:laabs:contact
 */
class communication
{
    /**
     * The contact identifier
     *
     * @var id
     * @notempty
     */
    public $communicationId;

    /**
     * The contact identifier
     *
     * @var id
     * @notempty
     */
    public $contactId;

    /**
     * The communication purpose
     *
     * @var string
     * @notempty
     */
    public $purpose;

    /**
     * The communication mean code
     *
     * @var string
     * @notempty
     */
    public $comMeanCode;

    /**
     * The communication value
     *
     * @var string
     */
    public $value;

    /**
     * Some more info
     *
     * @var string
     */
    public $info;

    /**
     * Constructor
     * @param id     $contactId
     * @param string $purpose
     * @param string $comMeanCode
     * @param string $value
     */
    public function __construct($contactId, $purpose, $comMeanCode, $value)
    {
        $this->contactId = $contactId;
        $this->purpose = $purpose;
        $this->comMeanCode = $comMeanCode;
        $this->value = $value;
    }
}