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

namespace bundle\recordsManagement\Message;

/**
 * Class model that represents an archive
 *
 * @package RecordsManagement
 * @author  Cyril VAZQUEZ (Maarch) <cyril.vazquez@maarch.org>
 */
class archive
{
    /**
     * The archive identifier
     *
     * @var id
     */
    public $archiveId;

    /**
     * The archive name
     *
     * @var string
     */
    public $archiveName;

    /**
     * Originator organisation Archive identifier
     *
     * @var string
     */
    public $originatorArchiveId;

    /**
     * The archive folder id
     *
     * @var string
     */
    public $filePlanPosition;

    /**
     * The name of description class
     *
     * @var string
     */
    public $descriptionClass;

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
     * The retention rule code
     *
     * @var string
     */
    public $retentionRuleCode;

    /**
     * The starting date of the retention rule calculation
     *
     * @var date
     */
    public $retentionStartDate;

    /**
     * The duration of retention
     *
     * @var duration
     */
    public $retentionDuration;

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
     */
    public $descriptionObject;

    /**
     * The life cycle events
     *
     * @var recordsManagement/lifeCycleEvent[]
     */
    public $lifeCycleEvent;

    /**
     * Digital resources of archive
     * Digital resources of archive
     *
     * @var digitalResource/digitalResource[]
     */
    public $digitalResources;

    /**
     * The archival agreement reference
     *
     * @var string
     */
    public $archivalAgreementReference;

    /**
     * Register number of originator organisation
     *
     * @var string
     */
    public $originatorOrgRegNumber;

    /**
     * Register number of depositor organisation
     *
     * @var string
     */
    public $depositorOrgRegNumber;

    /**
     * Register number of archiver organisation
     *
     * @var string
     */
    public $archiverOrgRegNumber;

    /**
     * The contained archives list
     *
     * @var recordManagement/archive[]
     */
    public $contents;
}
