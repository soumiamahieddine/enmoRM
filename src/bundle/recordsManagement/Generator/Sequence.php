<?php

/*
 * Copyright (C) 2020 Maarch
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
namespace bundle\recordsManagement\Generator;

class Sequence implements \bundle\recordsManagement\Controller\archiverArchiveIdGeneratorInterface
{
    /**
     * Sdo Factory for management of archive persistance
     * @var dependency/sdo/Factory
     */
    protected $sdoFactory;

    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }
    /**
     * Generate archiverArchiveId
     *
     * @param  recordsManagement/archive $archive
     *
     * @return recordsManagement/archive $archive
     */
    public function generate($archive)
    {
        $sequenceConfiguration = \laabs::configuration('recordsManagement')['archiveIdGenerator']['archiverArchiveIdRules'];
        if (is_null($sequenceConfiguration) || empty($sequenceConfiguration)) {
            return;
        }

        if (!empty($archive->archiverArchiveId) && !$sequenceConfiguration['isAutomaticallyForced']) {
            return;
        }

        if (isset($archive->parentArchiveId) && !empty($archive->parentArchiveId)) {
            $parentArchive = $this->sdoFactory->read('recordsManagement/archive', $archive->parentArchiveId);
            $parentArchiveId = (string) $parentArchive->archiveId;
            $directChildrenCount = $this->sdoFactory->count('recordsManagement/archive', "parentArchiveId='$parentArchiveId'");

            $archive->archiverArchiveId = $parentArchive->archiverArchiveId . $sequenceConfiguration['sequenceSeparator'] . str_pad($directChildrenCount + 1, 7, 0, STR_PAD_LEFT);
        } else {
            $sequenceId = $this->getNewSequenceId();
            $year = date("Y");
            $archive->archiverArchiveId = sprintf($sequenceConfiguration['format'], $sequenceConfiguration['sequenceSeparator'], $year, str_pad($sequenceId, 7, 0, STR_PAD_LEFT));
        }
    }

    protected function getNewSequenceId()
    {
        $sequenceId = null;

        $query = <<<EOT
            SELECT NEXTVAL('"recordsManagement"."archiverArchiveIdSequence"');
EOT;
        $stmt = $this->sdoFactory->das->pdo->prepare($query);
        $stmt->execute();
        while ($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $sequenceId = $result['nextval'];
        }

        return $sequenceId;
    }
}
