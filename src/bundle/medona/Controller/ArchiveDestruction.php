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
class ArchiveDestruction
{
    protected $sdoFactory;

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
     * Flag for disposal
     * @param array  $archiveIds The archives ids
     * @param string $comment    The comment of modification
     * @param string $identifier Message identifier
     *
     * @return bool
     */
    public function dispose($archiveIds, $comment = null, $identifier = null)
    {
        $archiveList = $this->archiveController->dispose($archiveIds);

        $archiveDestructionRequestController = \laabs::newController("medona/ArchiveDestructionRequest");

        $archivesByOriginator = array();
        foreach ($archiveList['success'] as $archiveId) {
            $archive = $this->archiveController->retrieve($archiveId);
            if (!isset($archivesByOriginator[$archive->originatorOrgRegNumber])) {
                $archivesByOriginator[$archive->originatorOrgRegNumber] = array();
            }

            $archivesByOriginator[$archive->originatorOrgRegNumber][] = $archive;
        }
        $requesterOrg = null;
        if (!$requesterOrg) {
            $requesterOrg = \laabs::getToken('ORGANIZATION');
            if (!$requesterOrg) {
                throw \laabs::newException('medona/invalidMessageException', "No current organization choosen");
            }
        }
        $requesterOrgRegNumber = $requesterOrg->registrationNumber;

        if (!$identifier) {
            $identifier = "archiveDestructionRequest_".date("Y-m-d-H-i-s");
        }

        $reference = $identifier;
        foreach ($archivesByOriginator as $originatorOrgRegNumber => $archives) {
            $i = 1;
            $recipientOrgRegNumber = $archives[0]->archiverOrgRegNumber;

            $unique = array(
                'type' => 'ArchiveDestructionRequest',
                'senderOrgRegNumber' => $requesterOrgRegNumber,
                'reference' => $reference,
            );

            while ($this->sdoFactory->exists("medona/message", $unique)) {
                $i++;
                $unique['reference'] = $reference = $identifier.'_'.$i;
            }

            $archiveDestructionRequestController->send($reference, $archives, $comment, $requesterOrgRegNumber, $recipientOrgRegNumber, $originatorOrgRegNumber);
        }

        return $archiveList;
    }
}
