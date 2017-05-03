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
namespace bundle\filePlan\Model;

/**
 * The basic information about an archive unit in file plan
 *
 * @package RecordsManagement
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 *
 * @pkey [archiveId, folderId]
 */
class archivePosition
{

    /**
     * The archive identifier
     *
     * @var string
     */
    public $archiveId;

    /**
     * Originator organisation Archive identifier
     *
     * @var string
     */
    public $originatorArchiveId;

    /**
     * The archive name/title
     *
     * @var string
     */
    public $archiveName;

    /**
     * The name of archival profile
     *
     * @var string
     */
    public $archivalProfileReference;

    /**
     * The deposit date of the archive
     *
     * @var timestamp
     */
    public $depositDate;

    /**
     * Originator organisation identifier
     *
     * @var string
     */
    public $ownerOrgRegNumber;

    /**
     * The folder identifier
     *
     * @var string
     */
    public $folderId;
}
