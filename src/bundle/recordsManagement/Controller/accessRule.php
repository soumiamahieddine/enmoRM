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
 * Managemet of the access rule of an archive
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class accessRule
{

    protected $sdoFactory;
    protected $lifeCycleJournalController;

    /**
     * Constructor
     * @param \dependency\sdo\Factory $sdoFactory The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;

        $this->lifeCycleJournalController = \laabs::newController("lifeCycle/journal");
    }

    /**
     * List the access rule's code
     *
     * @return recordManagement/accessRule The list of access code
     */
    public function index()
    {
        $accessRules = $this->sdoFactory->find('recordsManagement/accessRule');
        foreach ($accessRules as $accessRule) {
            if ($accessRule->duration == null) {
                continue;
            }
            if ($accessRule->duration->y == 999999999) {
                $accessRule->duration = null;
                $accessRule->durationUnit = "Illimité";
            }
        }

        return $accessRules;
    }

    /**
     * Edit an access code
     * @param string $code The access rule's code
     *
     * @return recordsManagement/accessRule The profile object
     */
    public function edit($code)
    {
        $accessRule = $this->sdoFactory->read('recordsManagement/accessRule', $code);

        $accessRule->accessEntry = $this->sdoFactory->readChildren('recordsManagement/accessEntry', $accessRule);

        return $accessRule;
    }

    /**
     * create an access code
     * @param recordsManagement/accessRule $accessRule The access code
     *
     * @return boolean The result of the request
     */
    public function create($accessRule)
    {
        if ($accessRule->duration == null || $accessRule->duration == "") {
            $accessRule->duration = "P0D";
        }
        try {
            $this->sdoFactory->create($accessRule, 'recordsManagement/accessRule');

            if (!empty($accessRule->accessEntry)) {
                foreach ($accessRule->accessEntry as $accessEntry) {
                    $accessEntry->accessRuleCode = $accessRule->code;
                    $this->sdoFactory->create($accessEntry, 'recordsManagement/accessEntry');
                }
            }
        } catch (\Exception $e) {
            throw new \Exception("Access Code not created.");
        }

        return true;
    }

    /**
     * update an access code
     * @param recordsManagement/accessRule $accessRule The access code
     *
     * @return boolean The result of the request
     */
    public function update($accessRule)
    {
        // previous access rules
        if ($accessRule->duration == null || $accessRule->duration == "") {
            $accessRule->duration = "P0D";
        }

        try {
            $this->sdoFactory->update($accessRule, 'recordsManagement/accessRule');

            $this->sdoFactory->deleteChildren('recordsManagement/accessEntry', $accessRule, 'recordsManagement/accessRule');
            if (!empty($accessRule->accessEntry)) {
                foreach ($accessRule->accessEntry as $accessEntry) {
                    $accessEntry->accessRuleCode = $accessRule->code;
                    $this->sdoFactory->create($accessEntry, 'recordsManagement/accessEntry');
                }
            }

            $archivalProfiles = $this->sdoFactory->find('recordsManagement/archivalProfile', "accessRuleCode='$accessRule->code'");
            if ($archivalProfiles) {
                foreach ($archivalProfiles as $archivalProfile) {
                    $eventItems = array('archivalProfileId' => $archivalProfile->archivalProfileId, 'archivalProfileReference' => $archivalProfile->reference);
                    $this->lifeCycleJournalController->logEvent('recordsManagement/ArchivalProfileModification', 'recordsManagement/accessRule', $accessRule->code, $eventItems);
                }
            }

        } catch (\Exception $e) {
            throw $e;
            throw new \Exception("Access Code not updated.");
        }

        return true;
    }

    /**
     * delete an access code
     * @param string $code The access code
     *
     * @return boolean The result of the request
     */
    public function delete($code)
    {
        try {
            $accessRule = $this->sdoFactory->read('recordsManagement/accessRule', $code);

            $this->sdoFactory->deleteChildren('recordsManagement/accessEntry', $code, 'recordsManagement/accessRule');
        } catch (\Exception $e) {
            throw new \Exception("Access Code not deleted.");
        }

        return $this->sdoFactory->delete($accessRule);
    }
}
