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
 * Class model that link an archive to a file plan folder 
 *
 * @package filePlan
 * @author  Prosper DE LAURE (Maarch) <prosper.delaure@maarch.org>
 *
 * @fkey [folderId] filePlan/folder [folderId]
 */
class position
{
    /**
     * The identifier of the archive linked to the folder
     * @var id
     */
    public $folderId;

    /**
     * The folder identifier 
     * @var string
     * @notempty
     */
    public $folderId;

}