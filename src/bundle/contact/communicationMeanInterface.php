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
 * API for communication means
 * 
 * @package Contact
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
interface communicationMeanInterface
{

    /**
     * Get the index of commMeans
     * 
     * @action contact/communicationMean/index
     * 
     * @return contact/communicationMean[]
     */
    public function readIndex();

    /**
     * Add an orgUnit type
     * @param contact/communicationMean $communicationMean the communication mean object
     * 
     * @return bool
     * 
     * @action contact/communicationMean/add
     */
    public function create($communicationMean);
    
    /**
     * Edit a communication mean
     *
     * @return object The commMean
     * 
     * @action contact/communicationMean/get
     */
    public function read_code_();

    /**
     * Modify a communication mean
     * @param string $name    The commMean to name
     * @param bool   $enabled The status
     *
     * @return bool
     * 
     * @action contact/communicationMean/modify
     */
    public function update_code_($name, $enabled);

    /**
     * Delete a communication mean
     * @return bool
     * 
     * @action contact/communicationMean/delete
     */
    public function delete_code_();
}
