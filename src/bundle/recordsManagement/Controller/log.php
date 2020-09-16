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
class log implements archiveDescriptionInterface
{
    /* Properties */

    public $sdoFactory;
    public $logFilePlan;
    public $translationLogType;
    public $filePlanController;

    /**
     * Constructor of access control class
     * @param \dependency\sdo\Factory $sdoFactory         The factory
     * @param string                  $logFilePlan        The path of log in the file plan
     * @param array                   $translationLogType The translation of log types
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $logFilePlan = null, $translationLogType = [])
    {
        $this->sdoFactory = $sdoFactory;
        $this->logFilePlan = $logFilePlan;
        $this->translationLogType = $translationLogType;

        $this->filePlanController = \laabs::newController("filePlan/filePlan");
    }

    /**
     * Get a  search result
     *
     * @param string  $archiveId   The archive identifier
     * @param string  $type        The type
     * @param date    $fromDate    The date
     * @param date    $toDate      The date
     * @param string  $processName The process name
     * @param string  $processId   The process identifier
     * @param string  $sortBy      The process identifier
     * @param integer $maxResults  The process identifier
     *
     * @return array Array of logs
     */
    public function find(
        $archiveId = null,
        $type = null,
        $fromDate = null,
        $toDate = null,
        $processName = null,
        $processId = null,
        $sortBy = ">fromDate",
        $maxResults = null
    ) {
        list($queryParams, $queryString) = $this->queryBuilder($archiveId, $type, $fromDate, $toDate, $processName, $processId);

        $logs = $this->sdoFactory->find(
            "recordsManagement/log",
            $queryString,
            $queryParams,
            $sortBy,
            0,
            $maxResults
        );

        return $logs;
    }

    /**
     * Create query for search
     *
     * @param string  $archiveId      The archive identifier
     * @param string  $type           The type
     * @param date    $fromDate       The date
     * @param date    $toDate         The date
     * @param string  $processName    The process name
     * @param string  $processId      The process identifier
     *
     * @return
     */
    protected function queryBuilder(
        $archiveId = null,
        $type = null,
        $fromDate = null,
        $toDate = null,
        $processName = null,
        $processId = null
    ) {
        $queryParts = [];
        $queryParams = [];

        if ($archiveId) {
            $queryParams['archiveId'] = $archiveId;
            $queryParts['archiveId'] = "archiveId = :archiveId";
        }

        if ($type) {
            $queryParams['type'] = $type;
            $queryParts['type'] = "type = :type";
        }

        if ($fromDate && $toDate) {
            $queryParams['fromDate'] = $fromDate->format('Y-m-d').'T00:00:00';
            $queryParams['toDate'] = $toDate->format('Y-m-d').'T23:59:59';
            $queryParts['date'] = "fromDate <= :toDate AND toDate >= :fromDate";
        } elseif ($fromDate) {
            $queryParams['fromDate'] = $fromDate->format('Y-m-d').'T00:00:00';
            $queryParts['date'] = "fromDate >= :fromDate";
        } elseif ($toDate) {
            $queryParams['toDate'] = $toDate->format('Y-m-d').'T23:59:59';
            $queryParts['date'] = "toDate <= :toDate";
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

        return [$queryParams, $queryString];
    }

    /**
     * Count search results
     *
     * @param string $archiveId      The archive identifier
     * @param string $type           The type
     * @param date   $fromDate       The date
     * @param date   $toDate         The date
     * @param string $processName    The process name
     * @param string $processId      The process identifier
     *
     * @return integer
     */
    public function countFind(
        $archiveId = null,
        $type = null,
        $fromDate = null,
        $toDate = null,
        $processName = null,
        $processId = null
    ) {
        list($queryParams, $queryString) = $this->queryBuilder($archiveId, $type, $fromDate, $toDate, $processName, $processId);

        $count = $this->sdoFactory->count(
            "recordsManagement/log",
            $queryString,
            $queryParams
        );

        return $count;
    }

    /**
     * Search the description objects
     * @param string $description The search args on description object
     * @param string $text        The search args on text
     * @param array  $args        The search args on archive std properties
     *
     * @return object Array of description objects
     */
    public function search($description = null, $text = null, array $args = [], $checkAccess = null, $maxResults = null)
    {
        $archiveController = \laabs::newController('recordsManagement/archive');
        $archives = [];

        $sortBy = ">fromDate";

        $logs = $this->sdoFactory->find("recordsManagement/log", $description, [], $sortBy, 0, $maxResults);
        foreach ($logs as $log) {
            try {
                $archive = $archiveController->read($log->archiveId);
                $archive->descriptionObject = $log;
                $archives[] = $archive;
            } catch (\Exception $e) {
            }
        }

        return $archives;
    }

    /**
     * Count log objects
     * @param string $description The search args on description object
     * @param string $text        The search args on text
     * @param array  $args        The search args on archive std properties
     *
     * @return object Array of description objects
     */
    public function count($description = null, $text = null, array $args = [], $checkAccess = null, $maxResults = null)
    {
        return count($this->search($description, $text, $args, $checkAccess, $maxResults = null));
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
        $journal = $this->sdoFactory->find(
            'recordsManagement/log',
            "type='$type' AND fromDate <= '$date' AND toDate >= '$date'",
            [],
            ">fromDate",
            0,
            1
        );

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
     * @return recordsManagement/log Log with its archive identifier
     */
    public function read($archiveId)
    {
        return $this->sdoFactory->read("recordsManagement/log", $archiveId);
    }

    /**
     * Update the description object
     * @param object $archive
     */
    public function update($archive)
    {
        // Not implemented yet...
    }

    /**
     * Delete the description object
     * @param id   $archiveId
     * @param bool $deleteDescription
     */
    public function delete($archiveId, $deleteDescription = true)
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
        $firstJournal = $this->sdoFactory->find('recordsManagement/log', "type='$type'", [], "<fromDate", 0, 1);
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

        $nextJournal =  $this->sdoFactory->find(
            'recordsManagement/log',
            "type = '$journal->type' and fromDate >= '$journal->toDate'",
            [],
            '<fromDate',
            0,
            1
        );

        if (!count($nextJournal)) {
            return null;
        }

        return end($nextJournal);
    }

    /**
     * Get the last usable journal
     * @param string $type              The type of journal
     * @param string $ownerOrgRegNumber The journal owner organization registration number 
     *
     * @return recordsManagement/log The journal object
     */
    public function getLastJournal($type, $ownerOrgRegNumber = null)
    {
        if ($ownerOrgRegNumber) {
            $query = "type='$type' AND ownerOrgRegNumber = '$ownerOrgRegNumber'";
        } else {
            $query = "type='$type' AND ownerOrgRegNumber = null";
        }
        
        $journals = $this->sdoFactory->find('recordsManagement/log', $query, [], ">toDate", 0, 1);

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
    public function depositJournal(
        $journalFileName,
        $fromDate,
        $toDate,
        $type,
        $processName = null,
        $timestampFileName = null
    ) {
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

        if (!($currentOrganization->orgRoleCodes && in_array("owner", (array) $currentOrganization->orgRoleCodes))) {
            throw \laabs::newException(
                "recordsManagement/logException",
                "The journal must be archived by an owner organization."
            );
        }

        $archiveController = \laabs::newController('recordsManagement/archive');
        $digitalResourceController = \laabs::newController('digitalResource/digitalResource');

        // Create archive
        $archive = $archiveController->newArchive();

        $archive->archiveId = $log->archiveId;
        $archive->accessRuleDuration = 'P0D';
        $archive->retentionDuration = 'P0D';
        $archive->finalDisposition = 'preservation';
        $archive->fileplanLevel = 'item';
        $archive->archiveName =
            'journal/'.
            $log->type.
            ' '.
            date_format($log->fromDate, 'Y/m/d').
            ' - '.
            date_format($log->toDate, 'Y/m/d');

        if (!empty($this->logFilePlan)) {
            $path = $this->resolveLogFilePlan($this->logFilePlan, ["type" => $log->type]);
            $position = $this->filePlanController->createFromPath(
                $path,
                $currentOrganization->registrationNumber,
                true
            );
            $archive->filePlanPosition = $position;
        }

         // Create resource
        $journalResource = $digitalResourceController->createFromFile($journalFileName);
        $journalResource->puid = "x-fmt/18";
        $journalResource->mimetype = "text/csv";
        $journalResource->archiveId = $archive->archiveId;
        $digitalResourceController->getHash($journalResource, "SHA256");

        $archive->digitalResources[] = $journalResource;

        if ($timestampFileName) {
            // Create timestamp resource
            $timestampResource = $digitalResourceController->createFromFile($timestampFileName);
            $digitalResourceController->getHash($timestampResource, "SHA256");
            
            $logMessage = ["message" => "Timestamp file generated"];
            \laabs::notify(\bundle\audit\AUDIT_ENTRY_OUTPUT, $logMessage);

            $timestampResource->archiveId = $journalResource->archiveId;
            $timestampResource->relatedResId = $journalResource->resId;
            $timestampResource->relationshipType = "isTimestampOf";

            $archive->digitalResources[] = $timestampResource;
        }

        $archive->descriptionObject = $log;
        $archive->descriptionClass = 'recordsManagement/log';

        $archive->originatorOrgRegNumber =
        $archive->archiverOrgRegNumber =
        $archive->depositorOrgRegNumber =
            (string) $currentOrganization->registrationNumber;
        $archive->originatorOwnerOrgId = (string) $currentOrganization->orgId;

        $archive->serviceLevelReference = $archiveController->useServiceLevel("deposit")->reference;

        if (strpos($archiveController->useServiceLevel("deposit")->control, "fullTextIndexation")) {
            $archive->fullTextIndexation = "requested";
        } else {
            $archive->fullTextIndexation = "none";
        }

        try {
            $archive = $archiveController->deposit($archive, 'journal/'.$log->type.'/<date("Y")>/<date("m")>');
            $logMessage = ["message" => "New journal identifier : %s", "variables" => $archive->archiveId];
            \laabs::notify(\bundle\audit\AUDIT_ENTRY_OUTPUT, $logMessage);

        } catch (\Exception $e) {
            $logMessage = ["message" => "Error on journal creation"];
            \laabs::notify(\bundle\audit\AUDIT_ENTRY_OUTPUT, $logMessage);
            throw $e;
        }

        return $archive->archiveId;
    }

    private function resolveLogFilePlan($path, $values)
    {
        $values = is_array($values) ? $values : get_object_vars($values);

        if (preg_match_all("/\<[^\>]+\>/", $path, $variables)) {
            foreach ($variables[0] as $variable) {
                $token = substr($variable, 1, -1);
                switch (true) {
                    case substr($token, 0, 5) == 'date(':
                        $format = substr($token, 5, -1);
                        $path = str_replace($variable, date($format), $path);
                        break;
                    case isset($values[$token]):
                        if (array_key_exists($values[$token], $this->translationLogType)) {
                            $values[$token] = $this->translationLogType[$values[$token]];
                        }

                        $path = str_replace($variable, (string) $values[$token], $path);
                        break;
                }
            }
        }

        return $path;
    }

    public function contents($type, $archiveId, $resourceId)
    {
        $archiveController = \laabs::newController('recordsManagement/archive');

        $res = $archiveController->consultation($archiveId, $resourceId);

        $stream = (stream_get_contents($res->attachment->data));

        $journal = $type . PHP_EOL;
        $journal .= $archiveId . ',' . $resourceId . PHP_EOL;
        $journal .= $stream;

        return $journal;
    }
}
