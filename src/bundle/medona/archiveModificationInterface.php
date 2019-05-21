<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona.
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\medona;

/**
 * Interface for management of archives
 *
 * @package medona
 */
interface archiveModificationInterface
{
    /*
        MODIFY ARCHIVES
    */
    /**
     * Suspend archives
     * @param array     $archiveIds     Array of archive identifier
     * @param string    $comment        The comment of modification
     * @param string    $identifier    Message identifier
     *
     * @action medona/ArchiveModification/freeze
     *
     */
    public function updateFreeze($archiveIds, $comment =null, $identifier =null);

    /**
     * Change the status of an archive
     * @param mixed     $archiveIds     Array of archive identifier
     * @param string    $comment        The comment of modification
     * @param string    $identifier    Message identifier
     *
     * @action medona/ArchiveModification/unfreeze
     */
    public function updateUnfreeze($archiveIds, $comment = null, $identifier =null);

    /**
     * Update a retention rule
     * @param recordsManagement/archiveRetentionRule $retentionRule The retention rule object
     * @param array                                  $archiveIds    The archives ids
     * @param string                                 $comment       The comment of modification
     * @param string                                 $identifier   Message identifier
     *
     * @action medona/ArchiveModification/modifyRetentionRule
     *
     */
    public function updateRetentionrule($retentionRule, $archiveIds, $comment = null, $identifier = null);

    /**
     * Update a access rule
     * @param recordsManagement/archiveAccessRule $accessRule    The retention rule object
     * @param array                               $archiveIds    The archives ids
     * @param string                              $comment       The comment of modification
     * @param string                              $identifier   Message identifier
     *
     * @action medona/ArchiveModification/modifyAccessRule
     *
     */
    public function updateAccessrule($accessRule, $archiveIds = null, $comment = null, $identifier = null);
}
