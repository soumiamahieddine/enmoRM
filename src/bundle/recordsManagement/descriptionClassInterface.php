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
namespace bundle\recordsManagement;
/**
 *
 * @author Cyril Vazquez <cyril.vazqure@maarch.org> 
 */
interface descriptionClassInterface
{
    /**
     * Get the description classes
     *
     * @action recordsManagement/descriptionClass/index
     */
    public function readIndex();

    /**
     * Get the description class
     *
     * @action recordsManagement/descriptionClass/read
     */
    public function read_name_();

    /**
     * Get the description class properties
     *
     * @action recordsManagement/descriptionClass/getDescriptionFields
     */
    public function read_name_Descriptionfields();
}
