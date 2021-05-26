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
     * @return recordsManagement/accessRule The list of access code
     */
    public function index()
    {
        try {
            $accessRules = $this->sdoFactory->find('recordsManagement/accessRule');
        } catch (\Exception $exception) {
            return null;
        }

        return $accessRules;
    }

    /**
     * Edit an access code
     * @param string $code The access rule's code
     *
     * @return recordsManagement/accessRule The profile object
     */
    public function edit($code = null)
    {
        if (is_null($code)) {
            $defaultCommunicationRule = isset(\laabs::configuration("recordsManagement")['actionWithoutCommunicationRule']) ? \laabs::configuration("recordsManagement")['actionWithoutCommunicationRule'] : null;

            if (is_null($defaultCommunicationRule) || !in_array($defaultCommunicationRule, ['allow', 'deny'])) {
                throw new \core\Exception\ConflictException("Missing or wrong default communication Rule");
            }

            $accessRule = \laabs::newInstance('recordsManagement/accessRule');
            if ($defaultCommunicationRule == 'allow') {
                $accessRule->duration = "PD0";
            } else {
                $accessRule->duration = "P999999999Y";
            }
        } else {
            $accessRule = $this->sdoFactory->read('recordsManagement/accessRule', $code);
        }

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
        } catch (\Exception $e) {
            throw new \core\Exception\ConflictException("Access Code not created.");
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
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            throw new \Exception("Access Code not deleted.");
        }

        return $this->sdoFactory->delete($accessRule);
    }
}
