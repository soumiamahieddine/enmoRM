<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of maarchRM.
 *
 * maarchRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * maarchRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle digitalResource.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace presentation\maarchRM\UserStory\archiveManagement;

/**
 * User story admin access rule
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface checkIntegrityInterface
{
    /**
     * Verify archives integrity
     * @param array $archiveIds Array of archive identifier
     * 
     * @return recordsManagement/archive/verifyIntegrity
     * 
     * @uses recordsManagement/archives/readIntegritycheck
     */
    public function readRecordsmanagementVerifyintegrity($archiveIds);
}
