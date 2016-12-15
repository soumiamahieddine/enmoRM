<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle recordsManagement.
 *
 * Bundle recordsManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle recordsManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\recordsManagement\Model;
/**
 * Class model that represents a document profile
 *
 * @package RecordsManagement
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 * @pkey [documentProfileId]
 * @key [reference]
 * @fkey [archivalProfileId] recordsManagement/archivalProfile [archivalProfileId]
 */
class documentProfile
{
    /**
     * The identifier of the archival profile
     *
     * @var id
     */
    public $archivalProfileId;

    /**
     * The identifier
     *
     * @var id
     */
    public $documentProfileId;

    /**
     * The reference
     *
     * @var string
     */
    public $reference;

    /**
     * The name
     *
     * @var string
     */
    public $name;

    /**
     * The document is required in archive 
     *
     * @var boolean
     */
    public $required;

    /**
     * The document accepts user custom indexes
     *
     * @var boolean
     */
    public $acceptUserIndex;

    /**
     *  The list of profile description
     *
     * @var recordsManagement/documentDescription[]
     */
    public $documentDescription = array();

    /**
     * Get the properties
     * @return array
     */
    public function getProperties()
    {
        return $this->documentDescription;
    }

    /**
     * Get the name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}