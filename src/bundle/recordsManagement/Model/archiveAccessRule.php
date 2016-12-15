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
 * Class model that represents access rule
 *
 * @package RecordsManagement
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 *
 * @pkey [code]
 * 
 * @substitution recordsManagement/accessRule
 * @xmlns rm maarch.org:laabs:recordsManagement
 */
class archiveAccessRule
{
    /**
     * The code of the access
     *
     * @var string
     * @xpath rm:code
     */
    public $code;

    /**
     * The duration of the access
     *
     * @var duration
     * @xpath rm:duration
     */
    public $duration;

    /**
     * The start date
     *
     * @var date
     * @xpath rm:startDate
     */
    public $startDate;

    /**
     * The start date
     *
     * @var dateTime
     * @xpath rm:originatingAgency/rm:Identifier
     */
    public $originatorOrgRegNumber;

    

}