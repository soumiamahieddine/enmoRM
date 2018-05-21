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
 * Managemet of the retention rule
 *
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class retentionRule
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
     * List the retention rules
     *
     * @return recordsManagement/retentionRule[] The list of retention rules
     */
    public function index()
    {
        $retentionRules = $this->sdoFactory->find('recordsManagement/retentionRule');

        return $retentionRules;
    }

    /**
     * Create a retention rule
     * @param recordsManagement/retentionRule $retentionRule The retention rule
     *
     * @return boolean The request result
     */
    public function create($retentionRule)
    {
        try {
            return $this->sdoFactory->create($retentionRule, 'recordsManagement/retentionRule');
        } catch (\Exception $e) {
            throw new \bundle\recordsManagement\Exception\retentionRuleException("Retention rule not created.");
        }
    }

    /**
     * Read a retention rule
     * @param string $code The retention rule code
     *
     * @return recordsManagement/retentionRule The retention rule
     */
    public function read($code)
    {
        return $this->sdoFactory->read('recordsManagement/retentionRule', $code);
    }

    /**
     * Update a retention rule
     * @param recordsManagement/retentionRule $retentionRule The retention rule
     *
     * @return boolean The request result
     */
    public function update($retentionRule)
    {
        try {
            $res = $this->sdoFactory->update($retentionRule, 'recordsManagement/retentionRule');

            $archivalProfiles = $this->sdoFactory->find('recordsManagement/archivalProfile', "retentionRuleCode='$retentionRule->code'");
            for ($i = 0; $i < count($archivalProfiles); $i++) {
                $eventItems = array('archivalProfileId' => $archivalProfiles[$i]->archivalProfileId);
                $this->lifeCycleJournalController->logEvent('recordsManagement/archivalProfileModification', 'recordsManagement/retentionRule', $retentionRule->code, $eventItems);
            }
        } catch (\core\Exception $e) {
            throw new \bundle\recordsManagement\Exception\retentionRuleException("Retention rule not updated.");
        }

        return $res;
    }

    /**
     * Delete a retention rule
     * @param string $code The retention rule code
     *
     * @return boolean The request result
     */
    public function delete($code)
    {
        $retentionRule = $this->sdoFactory->read('recordsManagement/retentionRule', $code);

        if (!$retentionRule) {
            return false;
        }
        try {
            $this->sdoFactory->delete($retentionRule);
        } catch (\Exception $e) {
            throw new \bundle\recordsManagement\Exception\retentionRuleException("Retention rule not deleted.");
        }

        return true;
    }

    /**
     * Get retentionRule by code
     * @param string $code The retention rule code
     *
     * @return recordsManagement/retentionRule
     */
    public function getRetentionRule($code)
    {
        $retentionRule = $this->sdoFactory->find('recordsManagement/retentionRule', "code = '$code'");

        return $retentionRule;
    }
}
