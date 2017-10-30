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
 * Class model that represents a relationship between 2 archives
 *
 * @package RecordsManagement
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 * 
 * @pkey [archiveId, relatedArchiveId, typeCode]
 * @fkey [archiveId] recordsManagement/archive [archiveId]
 * @fkey [relatedArchiveId] recordsManagement/archive [archiveId]
 */
class archiveRelationship
{
    /**
     * The archive identifier
     *
     * @var string
     * @notempty
     * @xpath @aid
     */
    public $archiveId;

    /**
     * The related archive identifier
     * 
     * @var string
     * @notempty
     * @xpath @rel
     */
    public $relatedArchiveId;

    /**
     * The relationship type code
     * 
     * @var string
     * @notempty
     * @xpath @type
     */
    public $typeCode;

    /**
     * The relationship information
     * 
     * @var string
     * @xpath .
     */
    public $description;
    

} // END class archive 
