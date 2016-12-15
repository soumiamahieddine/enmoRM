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
namespace bundle\recordsManagement;

/**
 * Interface for management of access rules
 *
 * @package RecordsMangement
 * @author  Maarch Prosper DE LAURE <prosper.delaure@maarch.org>
 */
interface accessRuleInterface
{

    /**
     *  List the access rule's code
     *
     * @action recordsManagement/accessRule/index The list of access code
     *
     */
    public function readIndex();

    /**
     * Edit an access code
     *
     * @action recordsManagement/accessRule/edit
     */
    public function read_code_();

    /**
     * create an access code
     * @param recordsManagement/accessRule $accessRule The access code
     *
     * @action recordsManagement/accessRule/create
     */
    public function create($accessRule);

    /**
     * update an access code
     * @param recordsManagement/accessRule $accessRule The access code
     *
     * @action recordsManagement/accessRule/update
     */
    public function update($accessRule);

    /**
     * delete an access code
     *
     * @action recordsManagement/accessRule/delete
     */
    public function delete_code_();
}
