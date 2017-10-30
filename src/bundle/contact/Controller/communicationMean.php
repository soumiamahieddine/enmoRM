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
 * Communication mean controller
 *
 * @package Maarch
 */
class communicationMean
{
    protected $sdoFactory;    

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory
     *
     * @return void
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get the index of communicationMeans
     * @return array The list of com means
     */
    public function index()
    {
        return $this->sdoFactory->find("contact/communicationMean");
    }

    /**
     * Record a new communication mean
     * @param object $communicationMean The comm mean to record
     *
     * @return bool
     */
    public function add($communicationMean)
    {
        $result = $this->sdoFactory->create($communicationMean, "contact/communicationMean");
        
        return $result;
    }

    /**
     * Edit a communication mean
     * @param string $code The code of the communicationMean to edit
     *
     * @return object The communicationMean
     */
    public function get($code)
    {
        return $this->sdoFactory->read("contact/communicationMean", $code);
    }

    /**
     * Modify a communication mean
     * @param string $code    The identifier
     * @param string $name    The commMean to name
     * @param bool   $enabled The status
     *
     * @return bool
     */
    public function modify($code, $name, $enabled)
    {
        $comMean = \laabs::newInstance("contact/communicationMean");
        $comMean->code = $code;
        $comMean->name = $name;
        $comMean->enabled = $enabled;

        $result = $this->sdoFactory->update($comMean);
        
        return $result;
    }

    /**
     * Delete a communication mean
     * @param string $code The Id of the communicationMean to delete
     *
     * @return bool
     */
    public function delete($code)
    {
        $communicationMean = $this->sdoFactory->read("contact/communicationMean", $code);

        $childrenCommunications = $this->sdoFactory->readChildren("contact/communication", $communicationMean);
        
        foreach ($childrenCommunications as $childCommunication) {
            $this->sdoFactory->delete($childCommunication);
        }
        
        $result = $this->sdoFactory->delete($code, "contact/communicationMean");
        
        return $result;
    }

}