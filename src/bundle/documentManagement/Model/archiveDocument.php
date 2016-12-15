<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle documentManagement.
 *
 * Bundle documentManagement is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle documentManagement is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle documentManagement.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\documentManagement\Model;

/**
 * Class model that represents a archiveDocument
 *
 * @package DocumentManagement
 * @author  Prosper DE LAURE <prosper.delaure@maarch.org>
 *
 * @pkey [docId]
 */
class archiveDocument
{
    /**
     * The document identifier
     *
     * @var string
     * @notempty
     */
    public $docId;

    /**
     * The digital resource identifier
     *
     * @var string
     */
    public $resId;

    /**
     * The archive identifier
     *
     * @var string
     */
    public $archiveId;

    /**
     * The type of document
     *
     * @var string
     * @notempty
     */
    public $type;

    /**
     * Controls to make on the document
     *
     * @var string
     */
    public $control;

    /**
     * Define if the document is a copy
     *
     * @var bool
     */
    public $copy;

    /**
     * The title of document
     *
     * @var string
     */
    public $title;

    /**
     * The description of document
     *
     * @var string
     */
    public $description;

    /**
     * The creator of document
     *
     * @var string
     */
    public $creator;

    /**
     * The publisher of document
     *
     * @var string
     */
    public $publisher;

    /**
     * The contributor(s) of document
     *
     * @var string
     */
    public $contributor;

    /**
     * The category of document
     *
     * @var string
     */
    public $category;

    /**
     * Language of the document
     *
     * @var string
     */
    public $language;

    /**
     * Purpose of the document
     *
     * @var string
     */
    public $purpose;

    /**
     * Date of creation of the document
     *
     * @var timestamp
     */
    public $creation;

    /**
     * Date of issue of the document
     *
     * @var timestamp
     */
    public $issue;

    /**
     * Date of receipt of the document
     *
     * @var timestamp
     */
    public $receipt;

    /**
     * Date of response of the document
     *
     * @var timestamp
     */
    public $response;

    /**
     * Date of submission of the document
     *
     * @var timestamp
     */
    public $submission;

    /**
     * The depositor document identifier
     *
     * @var string
     */
    public $depositorDocId;

    /**
     * The originator document identifier
     *
     * @var string
     */
    public $originatorDocId;

    /**
     * Originator organisation Archive identifier
     *
     * @var string
     */
    public $originatorArchiveId;

    /**
     * Depositor organisation Archive identifier
     *
     * @var string
     */
    public $depositorArchiveId;

    /**
     * Registration number of originator organisation
     *
     * @var string
     * @notempty
     */
    public $originatorOrgRegNumber;

    /**
     * Identifier number of originator root organisation
     *
     * @var string
     * @notempty
     */
    public $originatorOwnerOrgId;

    /**
     * Registration number of depositor organisation
     *
     * @var string
     * @notempty
     */
    public $depositorOrgRegNumber;

    /**
     * Registration number of archiver organisation
     *
     * @var string
     * @notempty
     */
    public $archiverOrgRegNumber;

    /**
     * The status
     *
     * @var string
     * @enumeration [received, pending, preserved, frozen, disposable, disposed, restitued]
     */
    public $status;

    /**
     * The archive name/title
     *
     * @var string
     */
    public $archiveName;

    /**
     * The name of archival profile
     *
     * @var string
     */
    public $archivalProfileReference;

    /**
     * The archival agreement reference
     *
     * @var string
     */
    public $archivalAgreementReference;

    /**
     * The action to execute when the retention rule is over
     *
     * @var string
     */
    public $finalDisposition;

    /**
     * The disposal date of the archive
     *
     * @var date
     */
    public $disposalDate;

    /**
     * The parent archive identifier
     *
     * @var string
     */
    public $parentArchiveId;
}
