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
use core\Encoding\json;

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
 *
 * @xmlns rm maarch.org:laabs:recordsManagement
 * @xmlns medona org:afnor:medona:1.0
 */
class archive
{
    /* ************************************************************************
     * Identification
     *********************************************************************** */
    /**
     * The archive identifier
     *
     * @var id
     * @xvalue generate-id
     * @notempty
     */
    public $archiveId;

    /**
     * Archiver organisation Archive identifier
     *
     * @var string
     * @xpath rm:archiverArchiveId
     */
    public $archiverArchiveId;

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
     * The archive folder id
     *
     * @var string
     * @xpath rm:filePlanPosition
     */
    public $filePlanPosition;

    /**
     * The originating date of the archive
     *
     * @var date
     */
    public $originatingDate;

    /* ************************************************************************
     * Management Refs
     *********************************************************************** */
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
     * The archival agreement reference
     *
     * @var string
     */
    public $archivalAgreementReference;

    /* ************************************************************************
     * Management Data
     *********************************************************************** */
    /**
     * The retention rule code
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
     * The status of retention rule
     *
     * @var string
     * @xpath rm:retentionRuleStatus
     */
    public $retentionRuleStatus;

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
     * The classification rule code
     *
     * @var string
     */
    public $classificationRuleCode;

    /**
     * The classification duration, before archive is unclassified
     *
     * @var duration
     */
    public $classificationRuleDuration;

    /**
     * The classification start date
     *
     * @var date
     */
    public $classificationRuleStartDate;

    /**
     * The classification end date
     *
     * @var date
     */
    public $classificationEndDate;

    /**
     * The classification level
     *
     * @var string
     */
    public $classificationLevel;

    /**
     * The classification owner identification
     *
     * @var string
     */
    public $classificationOwner;

    /* ************************************************************************
     * Management Org
     *********************************************************************** */
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
     * Registration number of originator root organisation
     *
     * @var string
     */
    public $originatorOwnerOrgRegNumber;

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
     * Registration number of user organisation
     *
     * @var tokenlist
     */
    public $userOrgRegNumbers;

    /* ************************************************************************
     * Life Cycle
     *********************************************************************** */
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
     * The life cycle events
     *
     * @var lifeCycle/event[]
     */
    public $lifeCycleEvent;

    /**
     * The status
     *
     * @var string
     * @enumeration [received, pending, preserved, frozen, disposable, disposed, restitued]
     */
    public $status;

    /* ************************************************************************
     * Description
     *********************************************************************** */
    /**
     * The description of archive
     *
     * @var json
     */
    public $description;

    /**
     * The status of fulltext indaxation
     *
     * @var string
     * @enumeration [requested, indexed, failed, none]
     */
    public $fullTextIndexation;

    /**
     * The name of description class
     *
     * @var string
     */
    public $descriptionClass;

    /**
     * The descriptive metadata object
     * @xpath rm:descriptionObject/*
     */
    public $descriptionObject;

    /**
     * The archive classification level
     *
     * @var string
     * @enumeration [file, recordgrp, subgrp, item]
     */
    public $fileplanLevel;

    /**
     * The storage path
     *
     * @var string
     */
    public $storagePath;

    /**
     * Status code for workflow and current use process
     * @var string
     */
    public $processingStatus;

    /* ************************************************************************
     * Structure
     *********************************************************************** */
    /**
     * The parent archive identifier
     *
     * @var string
     */
    public $parentArchiveId;

    /**
     * The contained archives list
     *
     * @var recordsManagement/archive[]
     */
    public $contents;

    /**
     * The digital resources
     *
     * @var digitalResource/digitalResource[]
     */
    public $digitalResources;

    /**
     * The archive relationship
     *
     * @var recordsManagement/archiveRelationship[]
     * @xpath rm:archiveRelationship
     */
    public $archiveRelationship;
}
