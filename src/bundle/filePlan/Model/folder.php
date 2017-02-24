<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle filePlan.
 *
 * Bundle filePlan is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle filePlan is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle filePlan.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\filePlan\Model;
/**
 * Class model that represents a file plan folder
 *
 * @package FilePlan
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 *
 * @pkey [folderId]
 * @fkey [parentFolderId] filePlan/folder [folderId]
 */
class folder
{
    /**
     * The folder identifier
     * @var string
     */
    public $folderId;

    /**
     * The name of the folder
     * @var string
     * 
     */
    public $name;
    
    /**
     * The parent folder identifer
     * @var string
     * 
     */
    public $parentFolderId;
    
    /**
     * The description 
     * @var string
     * 
     */
    public $description;

    /**
     * The folder owner organization identifier
     * @var string
     * @notempty
     * 
     */
    public $ownerOrgRegNumber;

    /**
     * The folder availability status for modification
     * @var boolean
     * 
     */
    public $disabled = false;

    /**
     * The sub folders
     * @var filePlan/folder[]
     * 
     */
    public $subFolders;
    
}