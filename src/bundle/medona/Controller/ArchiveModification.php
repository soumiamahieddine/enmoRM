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

namespace bundle\medona\Controller;

/**
 * Archives modification
 */
class ArchiveModification
{
    protected $sdoFactory;
    protected $archiveController;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->archiveController = \laabs::newController("recordsManagement/archive");
    }

    /**
     * Modify the archive retention
     * @param recordsManagement/archiveRetentionRule $retentionRule The retention rule object
     * @param mixed                                  $archiveIds    The archives ids
     * @param string                                 $comment       The comment of modification
     * @param string                                 $identifier    Message identifier
     *
     * @return bool
     */
    public function modifyRetentionRule($retentionRule, $archiveIds, $comment = null, $identifier = null)
    {
        $res = $this->archiveController->modifyRetentionRule($retentionRule, $archiveIds);

        $this->sendModificationNotification($archiveIds, $comment, $identifier);

        return $res;
    }

    /**
     * Modify the archive access
     * @param recordsManagement/archiveAccessCode $accessRule The access rule object
     * @param array                               $archiveIds The archives ids
     * @param string                              $comment    The comment of modification
     * @param string                              $identifier Message identifier
     *
     * @return bool
     */
    public function modifyAccessRule($accessRule, $archiveIds, $comment = null, $identifier = null)
    {
        $res = $this->archiveController->modifyAccessRule($accessRule, $archiveIds);

        $this->sendModificationNotification($archiveIds, $comment, $identifier);

        return $res;
    }

    /**
     * Suspend archives
     * @param mixed  $archiveIds Array of archive identifier
     * @param string $comment    The comment of modification
     * @param string $identifier Message identifier
     *
     * @return array
     */
    public function freeze($archiveIds, $comment = null, $identifier = null)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = $this->archiveController->freeze($archiveIds);

        $this->sendModificationNotification($archiveIds, $comment, $identifier);

        return $res;
    }

    /**
     * Liberate archives
     * @param mixed  $archiveIds Array of archive identifier
     * @param string $comment    The comment of modification
     * @param string $identifier Message identifier
     *
     * @return array
     */
    public function unfreeze($archiveIds, $comment = null, $identifier = null)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $res = $this->archiveController->unfreeze($archiveIds);

        $this->sendModificationNotification($archiveIds, $comment, $identifier);

        return $res;
    }

    /**
     * Add a relationship to the archive
     * @param recordsManagement/archiveRelationship $archiveRelationship The relationship of the archive
     *
     * @return bool The result of the operation
     */
    public function addRelationship($archiveRelationship)
    {
        $this->archiveController->addRelationship($archiveRelationship);

        $archive = $this->getDescription($archiveRelationship->archiveId);

        $this->sendModificationNotification($archive, "Add relationship");

        return true;
    }

    /**
     * delete a relationship
     * @param recordsManagement/archiveRelationship $archiveRelationship The archive relationship object
     *
     * @return recordsManagement/archiveRelationship
     */
    public function deleteRelationship($archiveRelationship)
    {
        $this->archiveController->deleteRelationship($archiveRelationship);

        $archive = $this->getDescription($archiveRelationship->archiveId);

        $this->sendModificationNotification($archive, "Delete relationship");

        return true;
    }

    protected function sendModificationNotification($archiveIds, $comment, $identifier = null)
    {
        $archives = array();
        foreach ($archiveIds as $archiveId) {
            $archive = $this->archiveController->retrieve($archiveId);
            $archives[] = $archive;
        }

        $archivesByOriginator = array();
        foreach ($archives as $archive) {
            if (!isset($archivesByOriginator[$archive->originatorOrgRegNumber])) {
                $archivesByOriginator[$archive->originatorOrgRegNumber] = array();
            }

            $archivesByOriginator[$archive->originatorOrgRegNumber][] = $archive;
        }

        $archiveModificationNotificationController = \laabs::newController("medona/ArchiveModificationNotification");
        
        if (!$identifier) {
            $identifier = "archiveModificationNotification_".date("Y-m-d-H-i-s");
        }

        $reference = $identifier;
        foreach ($archivesByOriginator as $originatorOrgRegNumber => $archives) {
            $i = 1;
            $senderOrg = $archives[0]->archiverOrgRegNumber;
            $recipientOrg = $originatorOrgRegNumber;

            $unique = array(
                'type' => 'ArchiveModificationNotification',
                'senderOrgRegNumber' => $senderOrg,
                'reference' => $reference,
            );

            while ($this->sdoFactory->exists("medona/message", $unique)) {
                $i++;
                $unique['reference'] = $reference = $identifier.'_'.$i;
            }
            
            $message = $archiveModificationNotificationController->send($reference, $archives, $senderOrg, $recipientOrg, $comment);
        }
    }
}
