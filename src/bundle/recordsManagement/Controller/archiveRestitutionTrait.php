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
namespace bundle\recordsManagement\Controller;

/**
 * Trait for archives restitution
 */
trait archiveRestitutionTrait
{
    /**
     * Flag for restitution
     * @param array $archiveIds     Array of archive identifier
     *
     * @return array The result of the operation
     */
    public function setForRestitution($archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        $canditates = $this->setStatus($archiveIds, 'restituable');

        return $canditates;
    }

    /**
     * Restitute an archive
     * @param mixed  $archives     The idetifier of the archive or an array of archive id
     * @param string $format       The format of output
     * @param bool   $resourceFile Generate the resource file
     *
     * @return int The number of restituted archives
     */
    public function restitute($archives, $format, $resourceFile)
    {
        $archiveSerializer = \laabs::newSerializer('recordsManagement/archive', 'xml');
        $succeeded = 0;

        if (!is_array($archives)) {
            $archives = array($archives);
        }

        $childrenArchive = array();

        foreach ($archives as $archiveId) {
            $this->verifyIntegrity($archiveId);

            $relationships = $this->sdoFactory->find("recordsManagement/archiveRelationship", "archiveId = '$archiveId'");
            foreach ($relationships as $relationship) {
                $childrenArchive[] = $relationship->relatedArchiveId;
            }
        }

        array_unique(array_merge($childrenArchive, $archives));

        foreach ($archives as $archiveId) {
            $archive = $this->retrieve($archiveId);

            $restitutionFile = $archiveSerializer->restitute($archive, !$resourceFile);
            $archiveFilename = $this->restitutionDirectory.DIRECTORY_SEPARATOR.$archive->archiveId.".".$format;

            file_put_contents($archiveFilename, $restitutionFile);

            if ($resourceFile) {
                $resourceFilename = $this->restitutionDirectory.DIRECTORY_SEPARATOR.$archive->archiveId;
                if ($archive->digitalResource->fileExtension) {
                    $resourceFilename .= ".".$archive->digitalResource->fileExtension;
                }
                file_put_contents($resourceFilename, $archive->digitalResource->getContents());
            }

            $this->setStatus($archive->archiveId, 'restitution');

            $succeeded++;

            // Life cycle journal
            $eventInfo = array(
                'archiverOrgRegNumber' => $archive->archiverOrgRegNumber,
                'originatorOrgRegNumber' => $archive->originatorOrgRegNumber,
            );

            foreach ($archive->document as $document) {
                if ($document->type == "CDO") {
                    $eventItems['resId'] = $document->digitalResource->resId;
                    $eventItems['hashAlgorithm'] = $document->digitalResource->hashAlgorithm;
                    $eventItems['hash'] = $document->digitalResource->hash;
                    $eventItems['address'] = $document->digitalResource->address[0]->path;

                    $this->lifeCycleJournalController->logEvent('recordsManagement/restitution', 'recordsManagement/archive', $archive->archiveId, $eventItems);
                }
            }
        }

        return $succeeded;
    }

    /**
     * Validate the restitution restitution
     * @param array $archiveIds Array of archive identifier
     *
     * @return bool The result of the operation
     */
    public function validateRestitution($archiveIds)
    {
        return $this->setStatus($archiveIds, 'restituted');
    }

    /**
     * Cancel restitution
     * @param array $archiveIds Array of archive identifier
     *
     * @return bool The result of the operation
     */
    public function cancelRestitution($archiveIds)
    {
        return $this->setStatus($archiveIds, 'preserved');
    }

    /**
     * Destruct restituted resource
     * @param id $archiveIds The archive identifier or identifier list
     *
     * @return bool The result of the operation
     */
    public function destructRestituted($archiveIds)
    {
        if (!is_array($archiveIds)) {
            $archiveIds = array($archiveIds);
        }

        foreach ($archiveIds as $archiveId) {
            $archive = $this->retrieve($archiveId);
            $destroyedArchives =  $this->destructArchive($archive);
            $archiveIds = array_diff($archiveIds, $destroyedArchives);
        }

        return true;
    }
}
