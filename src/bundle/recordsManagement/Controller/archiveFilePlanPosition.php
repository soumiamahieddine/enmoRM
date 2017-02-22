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
            $archives = $this->sdoFactory->find('recordsManagement/archiveFilePlanPosition', 'originatorOrgRegNumber = :orgRegNumber and parentArchiveId = null and filePlanPosition = null', ['orgRegNumber'=>$orgRegNumber]);
        } else {
            $archives = $this->sdoFactory->find('recordsManagement/archiveFilePlanPosition', 'originatorOrgRegNumber = :orgRegNumber and parentArchiveId = null and filePlanPosition = :folderId', ['orgRegNumber'=>$orgRegNumber, 'folderId'=>$folderId]);
        }

        return $archives;
    }

}
