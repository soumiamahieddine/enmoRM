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
 * @package filePlan
 * @author  Prosper DE LAURE (maarch) <prosper.delaure@maarch.org> 
 */
class filePlan {

    protected $sdoFactory;

    /**
     * Constructor
     * @param object $sdoFactory The model for file plan
     *
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory) {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get the file plan's tree
     *
     * @return array The list of file plan folder with their position
     */
    public function getTree() {
        // Find the user orgUnit
        $organizations = \laabs::callService("organization/userPosition/read");
        $orgIds = [];

        foreach ($organizations as $key => $userPosition) {
            $orgIds[] = (string) $userPosition->orgId;
            $organizations[$key] = $userPosition->organization;
        }
        
        $folders = $this->sdoFactory->find('filePlan/folder', "ownerOrgId=['".\laabs\implode("', '", $orgIds)."']");

        // sort by parent
        $roots = [];
        $folderList = [];

        foreach ($folders as $folder) {
            $parentFolderId = (string) $folder->parentFolderId;

            if ($parentFolderId == null) {
                $roots[] = $folder;
            } else {
                if (!isset($folderList[$parentFolderId])) {
                    $folderList[$parentFolderId] = [];
                }
                $folderList[$parentFolderId][] = $folder;
            }
        }
        
        $roots = $this->buildForlderTree($roots, $folderList);
        $orgById = [];
        $orgByParent = [];
        $orgRoots = [];

        foreach ($organizations as $organization) {
            $orgById[(string) $organization->orgId] = $organization;
        }

        foreach ($roots as $root) {
            if (!isset($orgById[(string) $root->ownerOrgId]->folder)) {
                $orgById[(string) $root->ownerOrgId]->folder = [];
            }

            $orgById[(string) $root->ownerOrgId]->folder[] = $root;   
        }

        // Org tree structure
        foreach ($organizations as $organization) {
            if (!in_array((string) $organization->parentOrgId, $orgIds)) {
                $orgRoots[] = $organization;
            } else {
                if (!isset($orgByParent[(string) $organizations->parentOrgId])) {
                    $orgByParent[(string) $organizations->parentOrgId] = [];
                }

                $orgByParent[(string) $organizations->parentOrgId][] = $organizations;
            }
        }

        return $this->buildOrgTree($orgRoots, $orgByParent);
    }

    /**
     * Build the file plan tree
     * @param array $roots      The list of parent folders
     * @param array $folderList The list of folders sorted by parentId
     *
     * @return array The folder tree
     */
    protected function buildForlderTree($roots, $folderList)
    {
        foreach ($roots as $folder) {
            $folderId = (string) $folder->folderId;

            if (isset($folderList[$folderId])) {
                $folder->folder = $this->buildForlderTree($folderList[$folderId], $folderList);
            }
        }

        return $roots;
    }

    /**
     * Build the organization tree
     * @param array $roots   The list of parent organization
     * @param array $orgList The list of organization sorted by parentId
     *
     * @return array The organization tree
     */
    protected function buildOrgTree($roots, $orgList)
    {
        foreach ($roots as $organization) {
            $organizationId = (string) $organization->orgId;

            if (isset($orgList[$organizationId])) {
                $organization->organization = $this->buildOrgTree($orgList[$organizationId], $orgList);
            }
        }

        return $roots;
    }

}
