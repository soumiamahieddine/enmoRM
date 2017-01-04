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
 * Class model that represents an archive
 *
 * @package RecordsManagement
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 *
 * @pkey [archiveId]
 * @id [archiveId]
 * 
 * @fkey [parentArchiveId] recordsManagement/archive [archiveId]
 * @fkey [archivalProfileReference] recordsManagement/archivalProfile [reference]
 * @fkey [serviceLevelReference] recordsManagement/serviceLevel [reference]
 * @key  [descriptionId]
 * @key  [descriptionClass]
 * 
 * @xmlns dm maarch.org:laabs:documentManagement
 * @xmlns rm maarch.org:laabs:recordsManagement
 * @xmlns medona org:afnor:medona:1.0
 */
class archive
{

    /**
     * The archive identifier
     *
     * @var id
     * @xvalue generate-id
     * @notempty
     */
    public $archiveId;

    /**
     * Originator organisation Archive identifier
     *
     * @var string
     * @xpath rm:originatorArchiveId
     */
    public $originatorArchiveId;

    /**
     * Depositor organisation Archive identifier
     *
     * @var string
     * @xpath rm:depositorArchiveId
     */
    public $depositorArchiveId;

    /**
     * The archive name/title
     *
     * @var string
     * @xpath rm:archiveName
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
     * The name of service level (unique key for serviceLevels)
     *
     * @var string
     */
    public $serviceLevelReference;

    /**
     * The restriction rule code
     *
     * @var string
     */
    public $retentionRuleCode;

    /**
     * The starting date of the retention rule calculation
     *
     * @var date
     * @xpath rm:retentionStartDate
     */
    public $retentionStartDate;

    /**
     * The duration of retention
     *
     * @var duration
     * @xpath rm:retentionDuration
     */
    public $retentionDuration;

    /**
     * The action to execute when the retention rule is over
     *
     * @var string
     * @xpath rm:finalDisposition
     */
    public $finalDisposition;

    /**
     * The disposal date of the archive
     *
     * @var date
     */
    public $disposalDate;

    /**
     * The access restriction rule code
     *
     * @var string
     */
    public $accessRuleCode;

    /**
     * The access rule duration, before archive is public
     *
     * @var duration
     */
    public $accessRuleDuration;

    /**
     * The access rule validity date
     *
     * @var date
     */
    public $accessRuleStartDate;

    /**
     * The access rule communication  date
     *
     * @var date
     */
    public $accessRuleComDate;

    /**
     * The deposit date of the archive
     *
     * @var timestamp
     */
    public $depositDate;

    /**
     * @var timestamp
     */
    public $lastCheckDate;

    /**
     * @var timestamp
     */
    public $lastDeliveryDate;
    
    /**
     * @var timestamp
     */
    public $lastModificationDate;

    /**
     * The status
     *
     * @var string
     * @enumeration [received, pending, preserved, frozen, disposable, disposed, restitued]
     */
    public $status;

    /**
     * The parent archive identifier
     *
     * @var string
     */
    public $parentArchiveId;

    /* Aggregates */
    /**
     * The descriptive metadata object
     * @xpath rm:descriptionObject/*
     */
    public $descriptionObject;

    /**
     * The life cycle events
     *
     * @var recordsManagement/lifeCycleEvent[]
     */
    public $lifeCycleEvent;

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
     * @xpath rm:originatorOrgRegNumber
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
     * The contained archives list
     *
     * @var recordManagement/archive[]
     */
    public $contents;

    /**
     * The digital resources
     *
     * @var digitalresource/digitalResource[]
     */
    public $digitalResources;

    /**
     * The archive relationship
     *
     * @var recordsManagement/archiveRelationship[]
     * @xpath rm:archiveRelationship
     */
    public $archiveRelationship;

    /**
     * The storage path
     *
     * @var string
     */
    public $storagePath;
}
