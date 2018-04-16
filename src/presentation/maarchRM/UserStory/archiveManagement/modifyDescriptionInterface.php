<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\archiveManagement;
/**
 * Interface for archive modification
 */
interface modifyDescriptionInterface
{
    /**
     * Change the information of archive
     * @param string $archiveId
     * @param string $originatorArchiveId
     * @param string $archiveName     
     * @param date   $originatingDate     
     * @param mixed $description
     * 
     * 
     * @return recordsManagement/archive/metadata
     * 
     * @uses recordsManagement/archives/updateMetadata
     */
    public function updateRecordsmanagementArchiveMetadata($archiveId,$originatorArchiveId,$archiveName,$originatingDate,$description);
}