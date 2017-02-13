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
     * @return void
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
        $folders = $this->sdoFactory->find('filePlan/folder');

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
        
        return $this->buildTree($roots, $folderList);
    }

    /**
     * Build the file plan tree
     *
     */
    protected function buildTree($roots, $folderList)
    {
        foreach ($roots as $folder) {
            $folderId = (string) $folder->folderId;

            if (isset($folderList[$folderId])) {
                $folder->folder = $this->buildTree($folderList[$folderId], $folderList);
            }
        }

        return $roots;
    }

}
