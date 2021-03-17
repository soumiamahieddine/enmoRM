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
 * Controler of the file plan
 *
 * @package FilePlan
 * @author  Prosper DE LAURE (maarch) <prosper.delaure@maarch.org> 
 */
class filePlan
{

    protected $sdoFactory;

    /**
     * Constructor
     * @param object $sdoFactory The model for file plan
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get the file plan's tree
     *
     * @return object[] The list of file plan folder with their position
     */
    public function getTree()
    {
        // Get user org tree
        $userPositionController = \laabs::newController('organization/userPosition');
        $tree = $userPositionController->getCurrentOrgTree();
        if (!$tree) {
            return;
        }

        $orgRegNumbers = $this->getOrgIdsFromTree($tree);

        $folders = $this->sdoFactory->find(
            'filePlan/folder',
            "ownerOrgRegNumber=['".\laabs\implode("', '", $orgRegNumbers)."']",
            [],
            "name"
        );

        $folderTree = \laabs::buildTree($folders, 'filePlan/folder');
        \laabs::C14NPath($folderTree, 'name', 'path');
        $folderTreeByOwner = [];

        foreach ($folderTree as $folder) {
            if (!isset($folderTreeByOwner[$folder->ownerOrgRegNumber])) {
                $folderTreeByOwner[$folder->ownerOrgRegNumber] = [];
            }
            $folderTreeByOwner[$folder->ownerOrgRegNumber][] = $folder;
        }

        $this->mergeFoldersToTree($tree, $folderTreeByOwner);

        return $tree;
    }

    /**
     * Create a folder
     * @param filePlan/folder $folder The new folder
     * 
     * @return string the new folder identifier
     */
    public function create($folder)
    {
        if ($this->exists($folder)) {
            throw new \core\Exception\ConflictException("The folder already exists");
        }

        $folder->folderId = \laabs::newId();

        $this->sdoFactory->create($folder, "filePlan/folder");

        return $folder->folderId;
    }

    /**
     * Check if the folder exists
     * @param filePlan/folder$folder
     *
     * @throws \core\Exception\NotFoundException
     *
     * @return boolean
     */
    public function exists($folder)
    {
        $checkParams = [];

        if (!isset($folder->parentFolderId)) {
            $folder->parentFolderId = null;
            $checkClause = "name=:name AND parentFolderId=NULL AND ownerOrgRegNumber=:ownerOrgRegNumber";
        } elseif (!$this->sdoFactory->exists('filePlan/folder', array('folderId' => $folder->parentFolderId))) {
            throw new \core\Exception\NotFoundException("The parent folder does not exist");
        } else {
            $checkClause = "name=:name AND parentFolderId=:parentFolderId AND ownerOrgRegNumber=:ownerOrgRegNumber";
            $checkParams["parentFolderId"] = $folder->parentFolderId;
        }

        $checkParams["name"] = $folder->name;
        $checkParams["ownerOrgRegNumber"] = $folder->ownerOrgRegNumber;

        $result = $this->sdoFactory->count('filePlan/folder', $checkClause, $checkParams) ? true : false;

        return $result;
    }

    /**
     * Create folder from path
     * @param string  $path             The folder path
     * @param string  ownerOrgRegNumber The owner
     * @param boolean $recursive        Recursive creation
     * @param string  $delimiter        The folder path
     */
    public function createFromPath($path, $ownerOrgRegNumber, $recursive = false, $delimiter = "/")
    {
        $items = \laabs\explode($delimiter, $path);
        $parentFolderId = null;

        while (!empty($items)) {
            $folder = \laabs::newInstance("filePlan/folder");
            $folder->name = array_shift($items);
            $folder->parentFolderId = $parentFolderId;
            $folder->ownerOrgRegNumber = $ownerOrgRegNumber;

            try {
                if (!$recursive && !empty($items)) {
                    $parentFolderId = $this->readByName($folder->name, $ownerOrgRegNumber, $parentFolderId)->folderId;
                    continue;
                }

                $parentFolderId = $this->create($folder);
            } catch (\core\Exception\ConflictException $e) {
                $parentFolderId = $this->readByName($folder->name, $ownerOrgRegNumber, $parentFolderId)->folderId;
            }
        }

        return $parentFolderId;
    }

    /**
     * Read a folder
     * @param string $folderId The folder identifier
     * 
     * @return filePlan/folder The folder
     */
    public function read($folderId)
    {
        try {
            $folder = $this->sdoFactory->read("filePlan/folder", $folderId);
        
        } catch(\Exception $e) {
            throw new \core\Exception\NotFoundException("The folder can't be found.");
        }

        return $folder;
    }

    /**
     * Read a folder
     * @param string $folderName        The folder name
     * @param string $ownerOrgRegNumber The owner of the folder
     * @param string $parentFolderId    The parent folder identifier
     *
     * @return filePlan/folder The folder
     */
    public function readByName($folderName, $ownerOrgRegNumber, $parentFolderId = null)
    {
        $folder = \laabs::newInstance("filePlan/folder");
        $folder->name = $folderName;
        $folder->ownerOrgRegNumber = $ownerOrgRegNumber;
        $folder->parentFolderId = $parentFolderId;

        $checkParams = [];

        if (!$this->exists($folder)) {
            throw new \core\Exception\ConflictException("The folder can't be found.");
        }

        if (!isset($folder->parentFolderId)) {
            $checkClause = "name=:name AND parentFolderId=NULL AND ownerOrgRegNumber=:ownerOrgRegNumber";
        } else {
            $checkClause = "name=:name AND parentFolderId=:parentFolderId AND ownerOrgRegNumber=:ownerOrgRegNumber";
            $checkParams["parentFolderId"] = $folder->parentFolderId;
        }

        $checkParams["name"] = $folder->name;
        $checkParams["ownerOrgRegNumber"] = $folder->ownerOrgRegNumber;

        $folders = $this->sdoFactory->find('filePlan/folder', $checkClause, $checkParams);

        $folder = $folders[0];

        return $folder;
    }

    /**
     * Move a folder on a new position
     * @param string $folderId
     * @param string $parentFolderId
     * 
     * @return boolean The result of the operation
     */
    public function move($folderId, $parentFolderId=null)
    {
        $folder = $this->sdoFactory->read('filePlan/folder', $folderId);

        if ($this->sdoFactory->exists('filePlan/folder', array('name' => $folder->name, 'parentFolderId' =>$parentFolderId))) {
            throw new \core\Exception\ConflictException("The folder already exists");
        }

        // Check 
        if ($parentFolderId) {
            $parentFolder = $this->sdoFactory->read('filePlan/folder', $parentFolderId);
            if ($parentFolder->ownerOrgRegNumber != $folder->ownerOrgRegNumber) {
                throw \Exception("Can't move to another service !");
            }
            if ($parentFolder->closed) {
                throw new \core\Exception\ForbiddenException("The folder is closed.");
            }
        }

        $folder->parentFolderId = $parentFolderId;

        $this->sdoFactory->update($folder);

        return true;
    }

    /**
     * Update a folder
     * @param filePlan/folder $folder The new folder
     * 
     * @return boolean The result of the operation
     */
    public function update($folder)
    {
        // Validate :
        // Couple parentFolderId + new name is unique

        $this->sdoFactory->update($folder, 'filePlan/folder');

        return true;
    }

    /**
     * Delete a folder
     * @param mixed $folder The folder identifier or the folder itself 
     * 
     * @return boolean The result of the operation
     */
    public function delete($folder)
    {
        if (is_string($folder)) {
            $folder = $this->sdoFactory->read('filePlan/folder', $folder);
        }

        $archiveFilePlanPositionController = \laabs::newController("recordsManagement/archiveFilePlanPosition");
        $archiveFilePlanPositionController->removeFilePlanPosition($folder->folderId);

        $children = $this->sdoFactory->find('filePlan/folder', "parentFolderId = '$folder->folderId'");

        foreach ($children as $child) {
            $this->delete($child);
        }

        /*
        $positionController = \laabs::newController('filePlan/position');
        $archivesCount = $positionController->count($folder->ownerOrgRegNumber, $folderId);

        if ($archivesCount > 0) {
            return false;
        }
        */

        $this->sdoFactory->delete($folder, 'filePlan/folder');

        return true;
    }

    /**
     * Get organization identifier from tree
     * @param object $tree   The organization tree
     * 
     * @return object[] the organization identifier list
     */
    protected function getOrgIdsFromTree($tree)
    {
        $orgRegNumbers = [];
        $orgRegNumbers[] = $tree->registrationNumber;

        if (isset($tree->organization)) {
            foreach ($tree->organization as $organization) {
                $orgRegNumbers = array_merge($orgRegNumbers, $this->getOrgIdsFromTree($organization));
            }
        }

        return $orgRegNumbers;

    }

    /**
     * Merge folder tree into organization tree
     * @param object $tree       The organization tree
     * @param object $folderTree The folder tree sorted by parentId
     * 
     * @return object the tree with folders
     */
    protected function mergeFoldersToTree($tree, $folderTree)
    {
        if (isset($folderTree[$tree->registrationNumber])) {
            $tree->folder = $folderTree[$tree->registrationNumber];
        }

        if (isset($tree->organization)) {
            foreach ($tree->organization as $organization) {
                $this->mergeFoldersToTree($organization, $folderTree);
            }
        }
    }   

}
