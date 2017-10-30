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
namespace bundle\filePlan\Controller;

/**
 * Controler of the file plan position
 *
 * @package FilePlan
 * @author  Cyril Vazquez (maarch) <cyril.vazquez@maarch.org> 
 */
class position
{

    protected $sdoFactory;

    protected $archiveController;

    /**
     * Constructor
     * @param object $sdoFactory The model for file plan
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;

        $this->archiveController = \laabs::newController('recordsManagement/archive');
    }

    /**
     * Add an archive on a position
     * @param string $archiveId
     * @param string $folderId
     * 
     * @return boolean
     */
    public function add($archiveId, $folderId)
    {
        if (!$this->sdoFactory->exists('filePlan/folder', $folderId)) {
            throw new \Exception('Folder does not exist.');
        }
        
        $position = \laabs::newInstance('filePlan/position');
        $position->archiveId = $archiveId;
        $position->folderId = $folderId;

        $this->sdoFactory->create($position);

        return true;
    }

    /**
     * Move an archive on a new position
     * @param string $archiveId
     * @param string $fromFolderId
     * @param string $toFolderId
     * 
     * @return boolean
     */
    public function move($archiveId, $fromFolderId=null, $toFolderId=null)
    {
        // If from position is given, delete
        // Else means archive was not positioned
        if ($fromFolderId) {
            $positionBefore = $this->sdoFactory->find('filePlan/position', 'folderId = :fromFolderId', ['fromFolderId'=>$fromFolderId]);
            if (count($positionBefore)) {
                $this->sdoFactory->delete($positionBefore[0]);
            }
        }

        // If to position is given, create
        // Else means archive is moved to root originator
        if ($toFolderId) {
            return $this->add($archiveId, $toFolderId);
        }

        return true;
    }

    /**
     * Get the list of archives in a position for an owner
     * @param string $ownerOrgRegNumber
     * @param string $folderId
     * 
     * @return boolean
     */
    public function index($ownerOrgRegNumber, $folderId=null)
    {
        if (empty($folderId)) {
            $positions = $this->sdoFactory->find('filePlan/archivePosition', 'ownerOrgRegNumber = :ownerOrgRegNumber and folderId = null', ['ownerOrgRegNumber'=>$ownerOrgRegNumber]);
        } else {
            $positions = $this->sdoFactory->find('filePlan/archivePosition', 'ownerOrgRegNumber = :ownerOrgRegNumber and folderId = :folderId', ['ownerOrgRegNumber'=>$ownerOrgRegNumber, 'folderId'=>$folderId]);
        }
        
        return $positions;
    }

    /**
     * Get the count of archives in a position for an owner
     * @param string $ownerOrgRegNumber
     * @param string $folderId
     * 
     * @return boolean
     */
    public function count($ownerOrgRegNumber, $folderId=null)
    {
        if (empty($folderId)) {
            $positions = $this->sdoFactory->count('filePlan/archivePosition', 'ownerOrgRegNumber = :ownerOrgRegNumber and folderId = null', ['ownerOrgRegNumber'=>$ownerOrgRegNumber]);
        } else {
            $positions = $this->sdoFactory->count('filePlan/archivePosition', 'ownerOrgRegNumber = :ownerOrgRegNumber and folderId = :folderId', ['ownerOrgRegNumber'=>$ownerOrgRegNumber, 'folderId'=>$folderId]);
        }
        
        return $positions;
    }

}
