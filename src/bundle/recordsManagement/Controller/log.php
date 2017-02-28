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

namespace bundle\recordsManagement\Controller;

/**
 * Class log
 *
 * @package RecordsManagement
 * @author  Cyril Vazquez <cyril.vazquez@maarch.org>
 */
class log
    implements archiveDescriptionInterface
{
    /* Properties */

    public $sdoFactory;

    /**
     * Constructor of access control class
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
    }

    /**
     * Get a  search result
     * @param string $archiveId      The archive identifier
     * @param string $type           The type
     * @param date   $fromDate       The date
     * @param date   $toDate         The date
     * @param string $processName    The process name
     * @param string $processId      The process identifier
     * @param string $sortBy         The process identifier
     * @param int    $numberOfResult The process identifier
     *
     * @return array Array of logs
     */
    public function find($archiveId = null, $type = null, $fromDate = null, $toDate = null, $processName = null, $processId = null, $sortBy = ">fromDate", $numberOfResult = 300)
    {
        $queryParts = array();
        $queryParams = array();

        //$queryParts[] = $this->auth->getUserAccessRule('recordsManagement/log');

        if ($archiveId) {
            $queryParams['archiveId'] = $archiveId;
            $queryParts['archiveId'] = "archiveId = :archiveId";
        }

        if ($type) {
            $queryParams['type'] = $type;
            $queryParts['type'] = "type = :type";
        }

        if ($fromDate && $toDate) {
            $queryParams['fromDate'] = $fromDate->format('Y-m-d').'T23:59:59';
            $queryParams['toDate'] = $toDate->format('Y-m-d').'T00:00:00';
            $queryContentDescription['date'] = "fromDate >= :toDate AND toDate <= :fromDate";

        } elseif ($toDate) {
            $queryParams['toDate'] = $toDate->format('Y-m-d').'T00:00:00';
            $queryContentDescription['date'] = "toDate >= :toDate";

        } elseif ($fromDate) {
            $queryParams['fromDate'] = $fromDate->format('Y-m-d').'T23:59:59';
            $queryContentDescription['date'] = "fromDate <= :fromDate";
        }

        if ($processName) {
            $queryParams['processName'] = $processName;
            $queryParts['processName'] = "processName = :processName";
        }

        if ($processId) {
            $queryParams['processId'] = $processId;
            $queryParts['processId'] = "processId = :processId";
        }

        $queryString = implode(' AND ', $queryParts);

        $logs = $this->sdoFactory->find("recordsManagement/log", $queryString, $queryParams, $sortBy, 0, $numberOfResult);

        return $logs;
    }

    /**
     * Search the description objects
     * @param string $description The search args on description object
     * @param string $text        The search args on text
     * @param array  $args        The search args on archive std properties
     */
    public function search($description=null, $text=null, array $args=[])
    {
        
    }

    /**
     * Retrieve a journal by evenement date
     * @param string $type The journal type
     * @param string $date The date of the event
     *
     * @return recordsManagement/log The journal object
     */
    public function getByDate($type, $date)
    {
        $journal = $this->sdoFactory->find('recordsManagement/log', "type='$type' AND fromDate >= '$date' AND toDate <= '$date'", null, ">fromDate", 1);

        if (!count($journal)) {
            return null;
        }

        return end($journal);
    }

    /**
     * Create the requested log
     * @param object $archive The archived log object
     *
     * @return boolean status of the query
     */
    public function create($archive)
    {
        if (!\laabs::validate($archive->descriptionObject)) {
            $e = new \core\Exception('Invalid log data');

            $e->errors = \laabs::getValidationErrors();
            throw $e;
        }
        
        $archive->descriptionObject->archiveId = $archive->archiveId;
        
        $this->sdoFactory->create($archive->descriptionObject, 'recordsManagement/log');

        return true;
    }

    /**
     * Read an log with its archive identifier
     * @param id $archiveId
     *
     * @return recordsManagement/log
     */
    public function read($archiveId)
    {
        return $this->sdoFactory->read("recordsManagement/log", $archiveId);
    }

    /**
     * Update the description object
     * @param object $description
     * @param id     $archiveId
     */
    public function update($description, $archiveId)
    {
        // Not implemented yet...
    }

    /**
     * Delete the description object
     * @param id   $archiveId
     * @param bool $deleteDescription
     */
    public function delete($archiveId, $deleteDescription=true)
    {
        // Not possible
    }

    /**
     * Read the first journal
     * @param string $type The type of journal
     *
     * @return recordsManagement/log The next journal
     */
    public function getFirstJournal($type)
    {
        $firstJournal = $this->sdoFactory->find('recordsManagement/log', "type='$type'", null, "<fromDate", 0, 1);
        if (!count($firstJournal)) {
            return null;
        }

        return end($firstJournal);
    }

    /**
     * Read the next journal
     * @param recordsManagement/log $journal THe current journal object
     *
     * @return recordsManagement/log The next journal
     */
    public function getNextJournal($journal)
    {
        if (is_scalar($journal) || get_class($journal) == 'core\Type\Id') {
            $journal = $this->sdoFactory->read('recordsManagement/log', $journal);
        }

        $nextJournal =  $this->sdoFactory->find('recordsManagement/log', "type = '$journal->type' and fromDate >= '$journal->toDate'", null, '<fromDate', 0, 1);
        if (!count($nextJournal)) {
            return null;
        }

        return end($nextJournal);
    }

    /**
     * Get the last usable journal
     * @param string $type The type of journal
     *
     * @return recordsManagement/log The journal object
     */
    public function getLastJournal($type)
    {
        $journals = $this->sdoFactory->find('recordsManagement/log', "type='$type'", null, ">fromDate", 0, 1);

        if (empty($journals)) {
            return null;
        }

        $journal = end($journals);

        return $journal;
    }

    /**
     * Deposit a log file
     * @param string    $journalFileName   The name of the journal to deposit
     * @param timestamp $fromDate          The journal start date
     * @param timestamp $toDate            The journal end date
     * @param string    $type              The tye of the journal (system, lifeCycle, application)
     * @param string    $processName       The journal process name
     * @param string    $timestampFileName The name of the timestamp file
     *
     * @return string The archive id of the journal archive
     */
    public function depositJournal($journalFileName, $fromDate, $toDate, $type, $processName = null, $timestampFileName = null)
    {
        $newJournal = \laabs::newInstance('recordsManagement/log');
        $newJournal->archiveId = \laabs::newId();
        $newJournal->toDate = $toDate;
        $newJournal->fromDate = $fromDate;
        $newJournal->processName = $processName;
        $newJournal->type = $type;

        return $this->archiveJournal($journalFileName, $newJournal, $timestampFileName);
    }

    /**
     * Deposit a log file
     * @param string $journalFileName   The name of the journal to deposit
     * @param object $log               The log object
     * @param string $timestampFileName The name of the timestamp file
     *
     * @return string The archive id of the journal archive
     */
    public function archiveJournal($journalFileName, $log, $timestampFileName = null)
    {
        if (!in_array($log->type, array('system', 'lifeCycle', 'application'))) {
            throw \laabs::newException("recordsManagement/logException", "The journal type is not allowed.");
        }

        $currentOrganization = \laabs::getToken("ORGANIZATION");

        if ($currentOrganization->orgRoleCodes && !in_array("owner", (array) $currentOrganization->orgRoleCodes)) {
            throw \laabs::newException("recordsManagement/logException", "The journal must be archived by an owner organization.");
        }

        $archiveController = \laabs::newController('recordsManagement/archive');
        $digitalResourceController = \laabs::newController('digitalResource/digitalResource');

        // Create archive
        $archive = $archiveController->newArchive();

        $archive->archiveId = $log->archiveId;
        $archive->accessRuleDuration = 'P0D';
        $archive->retentionDuration = 'P0D';
        $archive->finalDisposition = 'preservation';

         // Create resource
        $journalResource = $digitalResourceController->createFromFile($journalFileName);
        $journalResource->puid = "x-fmt/18";
        $journalResource->mimetype = "text/csv";
        $journalResource->archiveId = $archive->archiveId;
        $digitalResourceController->getHash($journalResource, "SHA256");

        if ($timestampFileName) {
            // Create timestamp resource
            $timestampResource = $digitalResourceController->createFromFile($timestampFileName);
            $digitalResourceController->getHash($timestampResource, "SHA256");

            $timestampResource->archiveId = $journalResource->archiveId;
            $timestampResource->relatedResId = $journalResource->resId;
            $timestampResource->relationshipType = "isTimestampOf";

            $archive->digitalResources[] = $timestampResource;
        }

        $archive->digitalResources[] = $journalResource;

        $archive->descriptionObject = $log;
        $archive->descriptionClass = 'recordsManagement/log';

        $archive->originatorOrgRegNumber = $archive->archiverOrgRegNumber = $archive->depositorOrgRegNumber = (string) $currentOrganization->registrationNumber;
        $archive->originatorOwnerOrgId = (string) $currentOrganization->orgId;

        $archive->serviceLevelReference = $archiveController->useServiceLevel("deposit")->reference;

        return $archiveController->deposit($archive);
    }
}
