<?php
/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle audit.
 *
 * Bundle audit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle audit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle audit.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace bundle\audit\Controller;

/**
 * Controller for the audit trail journal
 *
 * @package Audit
 */
class journal
{
    protected $sdoFactory;

    protected $eventController;

    protected $separateInstance;


    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory
     * @param string                  $separateInstance Read only instance events
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory, $separateInstance = false)
    {
        $this->sdoFactory = $sdoFactory;

        $this->separateInstance = $separateInstance;
    }

    /**
     * Chain the last journal
     *
     * @return string The chained journal file name
     */
    public function chainJournal()
    {
        $logController = \laabs::newController('recordsManagement/log');
        $tmpdir = \laabs::getTmpDir();

        $newJournal = \laabs::newInstance('recordsManagement/log');
        $newJournal->archiveId = \laabs::newId();
        $newJournal->type = "application";
        $newJournal->toDate = \laabs::newTimestamp();


        // Get events to write in the new journal
        $previousJournal = $logController->getLastJournal('application');
        if ($previousJournal) {
            $newJournal->fromDate = $previousJournal->toDate;
            $newJournal->previousJournalId = $previousJournal->archiveId;

            $queryString = "eventDate > '$newJournal->fromDate' AND eventDate <= '$newJournal->toDate'";

            if ($this->separateInstance) {
                $queryString .= "AND instanceName = '".\laabs::getInstanceName()."'";
            }

            $events = $this->sdoFactory->find('audit/event', $queryString, [], "<eventDate");

        } else {
            // No previous journal, select all events
            $events = $this->sdoFactory->find('audit/event', "eventDate <= '$newJournal->toDate'", null, "<eventDate");
            if (count($events) > 0) {
                $newJournal->fromDate = reset($events)->eventDate;
            } else {
                $newJournal->fromDate = \laabs::newTimestamp('1970-01-01');
            }
        }

        $journalFilename = $tmpdir.DIRECTORY_SEPARATOR.(string) $newJournal->archiveId.".csv";
        $journalFile = fopen($journalFilename, "w");

        // First event : chain with previous journal
        $eventLine = array();
        $eventLine[0] = (string) $newJournal->archiveId;
        $eventLine[1] = (string) $newJournal->fromDate;
        $eventLine[2] = (string) $newJournal->toDate;

        // Write previous journal informations
        if ($previousJournal) {
            $eventLine[3] = (string) $previousJournal->archiveId;

            $archiveController = \laabs::newController('recordsManagement/archive');
            $resources = $archiveController->getDigitalResources($previousJournal->archiveId);
            $journalResource = $resources[0];

            $eventLine[4] = (string) $journalResource->hashAlgorithm;
            $eventLine[5] = (string) $journalResource->hash;
        }

        fputcsv($journalFile, $eventLine);

        // Write events
        foreach ($events as $event) {
            $eventLine = array();

            $eventLine[] = (string) $event->eventDate;
            $eventLine[] = (string) $event->accountId;
            $eventLine[] = (string) $event->path;
            $eventLine[] = (string) $event->status;
            
            if (isset($event->output)) {
                $output = json_decode($event->output);
                if ($output) {
                    $messages = [];
                    foreach ($output as $value) {
                        $messages[] = $value->fullMessage;
                    }
                    $eventLine[] = implode('  ', $messages);
                }
            }

            fputcsv($journalFile, $eventLine);
        }

        fclose($journalFile);

        // create timestamp file
        $timestampFileName = null;
        if (isset(\laabs::configuration('audit')['chainWithTimestamp']) && \laabs::configuration('audit')['chainWithTimestamp']==true) {
            try {
                $timestampServiceUri = \laabs::configuration('audit')['timestampService'];
                $timestampService = \laabs::newService($timestampServiceUri);
                $timestampFileName = $timestampService->getTimestamp($journalFilename);
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return $logController->archiveJournal($journalFilename, $newJournal, $timestampFileName);
    }
}
