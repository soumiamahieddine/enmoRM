<?php
/*
 *  Copyright (C) 2017 Maarch
 * 
 *  This file is part of bundle XXXX.
 *  Bundle recordsManagement is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 * 
 *  Bundle recordsManagement is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 * 
 *  You should have received a copy of the GNU General Public License
 *  along with bundle recordsManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\recordsManagement\Controller;

/**
 * Archive file plan controller
 *
 * @author Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class archiveFilePlanPosition
{
    /**
     * Sdo Factory for management of archive persistance
     * @var dependency/sdo/Factory
     */
    protected $sdoFactory;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The dependency sdo factory service
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get the archives on the given folder
     * @param string $orgRegNumber
     * @param string $folderId
     * 
     * @return array
     */
    public function getFolderContents($orgRegNumber, $folderId=null)
    {
        if (empty($folderId)) {
            $queryString = 'originatorOrgRegNumber = :orgRegNumber and filePlanPosition = null and (parentArchiveId = null)';
            $queryArgs = ['orgRegNumber'=>$orgRegNumber];
        } else {
            $queryString = 'originatorOrgRegNumber = :orgRegNumber and filePlanPosition = :folderId and (parentArchiveId = null)';
            $queryArgs = ['orgRegNumber'=>$orgRegNumber, 'folderId'=>$folderId];
        }

        $queryString .= "and status != 'disposed'";
        
        $archives = $this->sdoFactory->find('recordsManagement/archiveFilePlanPosition', $queryString, $queryArgs);
            
        // CVA 08-03-17 : Si nécessaire, ne sélectionner que mes racines
        /* $archivesById = [];
        foreach ($archives as $archive) {
            $archivesById[$archive->archiveId] = $archive;
        }

        $rootArchives = [];
        foreach ($archivesById as $archiveId => $archive) {
            if (empty($archive->parentArchiveId) || !isset($archivesById[$archive->parentArchiveId])) {
                $rootArchives[] = $archive;
            }
        }

        return $rootArchives;
        */

        return $archives;
    }

    /**
     * Move an archive into a folder
     * @param array  $archiveIds   The archive identifier list
     * @param string $fromFolderId The originating folder identifier
     * @param string $toFolderId   The destination folder identifier
     * 
     * @return int The number of moved archives
     */
    public function moveArchivesToFolder($archiveIds, $fromFolderId=null, $toFolderId=null)
    {
        $filePlanController = \laabs::newController("filePlan/filePlan");
        $fromFolder = null;
        $toFolder = null;
        $count = 0;

        if ($fromFolderId) {
            $fromFolder = $filePlanController->read($fromFolderId);
        }

        if ($toFolderId) {
            $toFolder = $filePlanController->read($toFolderId);
        }

        if (($toFolder && $toFolder->closed) || $fromFolder && $fromFolder->closed) {
            throw new \core\Exception\ForbiddenException("The folder is closed.");
        }
        
        if ($fromFolder && $toFolder && $fromFolder->ownerOrgRegNumber != $toFolder->ownerOrgRegNumber) {
            throw new \core\Exception\ForbiddenException("The archive can not be moved in a different organization unit.");
        }

        $transactionControl = !$this->sdoFactory->inTransaction();

        if ($transactionControl) {
            $this->sdoFactory->beginTransaction();
        }

        // move archives
        try {
            foreach ($archiveIds as $archiveId) {
                $archive = $this->sdoFactory->read('recordsManagement/archiveFilePlanPosition', $archiveId);

                // Validation
                if (!$archive) {
                    throw new \core\Exception\NotFoundException("The archive identifier %s does not exist.", 404, null, [$archiveId]);
                }

                if ($fromFolderId != $archive->filePlanPosition) {
                    throw new \core\Exception\ForbiddenException("The archive %s is not from the folder %s2.", 404, null, [$archiveId, $fromFolderId]);
                }

                if ($toFolder && $toFolder->ownerOrgRegNumber != $archive->originatorOrgRegNumber) {
                    throw new \core\Exception\ForbiddenException("The folder only accepts archives originated from %s.", 404, null, [$folder->ownerOrgRegNumber]);
                }

                // Update
                $archive->filePlanPosition = $toFolderId;
                $this->sdoFactory->update($archive, "recordsManagement/archiveFilePlanPosition");

                $count ++;
            }

        } catch (\Exception $exception) {
            if ($transactionControl) {
                $this->sdoFactory->rollback();
            }

            throw $exception;
        }

        if ($transactionControl) {
            $this->sdoFactory->commit();
        }

        return $count;
    }

    /**
     * List an archive resources and children archives
     * @param mixed $archive the archive identifier or the archive
     * 
     * @return object The archiveContent
     */
    public function listArchiveContents($archive) 
    {

        if (is_string($archive)) {
            $archiveId = $archive;

            $archive = new \stdClass();
            $archive->archiveId = $archiveId;
        }

        // Resources
        $digitalResourceController = \laabs::newController("digitalResource/digitalResource");
        $archive->digitalResources = $digitalResourceController->getResourcesByArchiveId($archive->archiveId);

        // ChildrenArchives
        $childrenArchives = $this->sdoFactory->find("recordsManagement/archiveFilePlanPosition", "parentArchiveId='".(string) $archive->archiveId."'", null, '< archiveName');
        foreach ($childrenArchives as $childArchive) {
            $archive->childrenArchives[] = $this->listArchiveContents($childArchive);
        }

        return $archive;
    }

    /**
     * Remove all archive of a folder 
     * @param string $filePlanPosition The file plan position to remove
     * 
     * @return int The number of archive updated
     */
    public function removeFilePlanPosition($filePlanPosition)
    {
        $count = 0;
        $archives = $this->sdoFactory->find("recordsManagement/archiveFilePlanPosition", "filePlanPosition = '$filePlanPosition'");

        foreach ($archives as $archive) {
            $archive->filePlanPosition = null;

            $this->sdoFactory->update($archive, "recordsManagement/archiveFilePlanPosition");
            $count++;
        }

        return $count;
    }

}
