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
 * User story of migration
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface migrationInterface
{
    /**
     * Flag archives for disposal
     *
     * @uses recordsManagement/archive/updateInteractiveconversion_resId_
     *
     * @return recordsManagement/archive/convert
     */
    public function updateRecordsmanagementInteractiveconversion_resId_();

    /**
     * List all archive conversion request
     *
     * @uses medona/documentConversion/readList
     * @return medona/message/listConversionRequest
     */
    public function readConversionrequestList();

    /**
     * Flag archives for disposal
     * @param array $documentIds Array of document identifier
     *
     * @uses medona/documentConversion/updateDocumentsconversion
     * @return medona/archiveModification/conversion
     */
    public function updateConversion($documentIds);
}
