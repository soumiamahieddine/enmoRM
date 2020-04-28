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
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\destruction;

/**
 * User story destruction processing
 * @author Alexandre Morin <prosper.delaure@maarch.org>
 */
interface destructionRequestInterface
{
    /**
     * Get destruction request form
     *
     * @return medona/message/destructionRequest
     */
    public function readDestructionRequest();

    /**
     * Flag archives for disposal
     * @param array  $archiveIds The archives ids
     * @param string $comment    The comment of destruction request
     * @param string $identifier Message identifier
     * @param string $format     Message format
     *
     * @uses recordsManagement/archives/updateDisposearchives
     * @return recordsManagement/archive/dispose
     */
    public function updateRecordsmanagementDispose($archiveIds, $comment = null, $identifier = null, $format = null);

    /**
     * Resource destruction
     * @param array $resIds Id List of resource
     *
     * @uses recordsManagement/archive/delete_archiveId_Digitalresource
     * @return recordsManagement/archive/deleteResource
     */
    public function delete_archiveId_Digitalresource($resIds);
}
