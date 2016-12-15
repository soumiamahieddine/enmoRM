<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle documentManagement.
 *
 * Bundle documentManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle documentManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle documentManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\documentManagement\Model;
/**
 * Class model that represents a relationship between 2 archives
 *
 * @package DocumentManagement
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 * 
 * @pkey [docId, relatedDocId, typeCode]
 * @fkey [docId] documentManagement/document [docId]
 * @fkey [relatedDocId] documentManagement/document [docId]
 */
class documentRelationship
{
    /**
     * The document identifier
     *
     * @var string
     * @notempty
     */
    public $docId;

    /**
     * The related document identifier
     * 
     * @var string
     * @notempty
     */
    public $relatedDocId;

    /**
     * The relationship type code
     * 
     * @var string
     * @notempty
     */
    public $typeCode;

    /**
     * The relationship information
     * 
     * @var string
     */
    public $description;
    

} // END class archive 
