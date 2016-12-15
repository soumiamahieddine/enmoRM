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
namespace bundle\recordsManagement\Model;

/**
 * Class model that represents a view
 *
 * @package RecordsManagement
 * @author Alexis RAGOT <alexis.ragot@maarch.org>
 *
 * @pkey[docId]
 */
class archiveDocumentDigitalResource
{
    /**
     * The archive identifier
     *
     * @var id
     * @notempty
     */
    public $archiveId;

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
     * The archive name/title
     *
     * @var string
     */
    public $archiveName;

    /**
     * The name of description class
     *
     * @var string
     */
    public $descriptionClass;

    /**
     * The name of description identifier
     *
     * @var id
     */
    public $descriptionId;

    /**
     * The name of archival profile
     *
     * @var string
     */
    public $archivalProfileReference;

    /**
     * The disposal date of the archive
     *
     * @var date
     */
    public $disposalDate;

    /**
     * The status
     *
     * @var string
     * @enumeration [received, pending, preserved, frozen, disposable, disposed, restitued]
     */
    public $archiveStatus;

    /**
     * The parent archive identifier
     *
     * @var string
     */
    public $parentArchiveId;

    /**
     * The archival agreement reference
     *
     * @var string
     */
    public $archivalAgreementReference;

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


    /***********************************************/

    /**
     * The document identifier
     *
     * @var string
     * @notempty
     */
    public $docId;

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
     * The document identifier of depositor
     *
     * @var string
     */
    public $depositorDocId;

    /**
     * The document identifier of originator
     *
     * @var string
     */
    public $originatorDocId;

    /**
     * Status of the document
     *
     * @var string
     */
    public $documentStatus;


    /***********************************************/


    /**
     * The universal identifier
     *
     * @var id
     */
    public $resId;

    /**
     * The storing profile identifier
     *
     * @var id
     */
    public $clusterId;

    /**
     * The size of the resource
     *
     * @var integer
     */
    public $size;

    /**
     * The UK National Archives PRONOM registry format identifier
     *
     * @var string
     */
    public $puid;

    /**
     * The mime type
     *
     * @var string
     */
    public $mimetype;

    /**
     * The integrity hash value
     *
     * @var string
     */
    public $hash;

    /**
     * The integrity hash algorithm
     *
     * @var string
     */
    public $hashAlgorithm;

    /**
     * The file extension
     *
     * @var string
     */
    public $fileExtension;

    /**
     * The file name
     *
     * @var string
     */
    public $fileName;

    /**
     * The xml for media information : audio, video, image
     *
     * @var string
     */
    public $mediaInfo;

    /**
     * The date when the resource was recorded
     *
     * @var timestamp
     */
    public $created;

    /**
     * The date when the resource was last mofified
     *
     * @var timestamp
     */
    public $updated;
}
