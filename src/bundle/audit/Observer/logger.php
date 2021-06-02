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

namespace bundle\audit\Observer;

/**
 * Observer to notify audit entries
 */
class logger
{

    /**
     * @var \dependency\sdo\factory $sdoFactory
     */
    public $sdoFactory;
    public $currentAuditFile;
    public $servicePath;
    public $input;
    public $output;
    public $ignoreMethods = [];
    public $ignorePaths = [];

    /**
     * Constructor
     * @param \dependency\sdo\factory $sdoFactory     The sdo factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;

        if (isset(\laabs::configuration('audit')['ignoreMethods'])) {
            $this->ignoreMethods = \laabs::configuration('audit')['ignoreMethods'];
        }

        if (isset(\laabs::configuration('audit')['ignorePaths'])) {
            $this->ignorePaths = \laabs::configuration('audit')['ignorePaths'];
        }
        $this->ignorePaths[] = ("audit/*");
    }

    /**
     * Log a given event by observation
     * @param audit/entry &$entry              The event to log
     * @param array       &$entryRelationships An array of entry ids to create relationships with
     *
     * @return void
     *
     * @subject bundle\audit\AUDIT_ENTRY
     */
    public function log(\bundle\audit\Model\entry &$entry, array &$entryRelationships = null)
    {
        if (empty($entry->entryId)) {
            $entry->entryId = \laabs::newId();
        }

        if ($accountToken = \laabs::getToken('AUTH')) {
            $entry->accountId = $accountToken->accountId;
        } else {
            $entry->accountId = '__system__';
        }

        $this->sdoFactory->create($entry, 'audit/entry');

        if (count($entryRelationships) > 0) {
            foreach ($entryRelationships as $fromEntryId) {
                $entryRelationship = \laabs::newInstance("audit/entryRelationship");
                $entryRelationship->fromEntryId = $fromEntryId;
                $entryRelationship->toEntryId = $entry->entryId;

                $this->sdoFactory->create($entryRelationship);
            }
        }
        
        if ($currentOrganization = \laabs::getToken("ORGANIZATION")) {
            $organizationController = \laabs::newController('organization/organization');
            $organization = $organizationController->read($currentOrganization->ownerOrgId);

            $event->orgRegNumber = $organization->registrationNumber;
            $event->orgUnitRegNumber = $currentOrganization->registrationNumber;
        }
        $event->instanceName = \laabs::getInstanceName();

        return $entry;
    }

    /**
     * Log a given event by observation
     * @param mixed &$userCommand
     *
     * @return void
     *
     * @subject LAABS_USER_COMMAND
     */
    public function notifyUserCommand(&$userCommand)
    {
        $this->userCommand = $userCommand;

        // User account
    }

    /**
     * Log a given event by observation
     * @param mixed &$servicePath
     * @param mixed &$serviceMessage
     *
     * @return void
     *
     * @subject LAABS_SERVICE_PATH
     */
    public function notifyServicePath(&$servicePath, &$serviceMessage = null)
    {
        if (in_array($servicePath->method, $this->ignoreMethods)) {
            return;
        }

        foreach ($this->ignorePaths as $ignorePath) {
            if (fnmatch($ignorePath, $servicePath->domain . LAABS_URI_SEPARATOR . $servicePath->interface . LAABS_URI_SEPARATOR . $servicePath->name)) {
                return;
            }
        }

        $this->servicePath = $servicePath;

        // Extract revealant info from input message
        if ($serviceMessage) {
            $this->input = array();
            foreach ($serviceMessage as $name => $value) {
                switch (true) {
                    // Avoid service path variables in input
                    case array_key_exists($name, $this->servicePath->variables):
                        continue 2;

                    case $name == 'password':
                    case $name == 'oldPassword':
                    case $name == 'newPassword':
                        break;

                    // Scalar revealant values
                    case (is_scalar($value) && ctype_print($value)) :
                    case is_bool($value):
                    case is_numeric($value):
                        $this->input[$name] = $value;
                        break;

                    // Stringifyable objects
                    case (is_object($value) && method_exists($value, '__toString')) :
                        $this->input[$name] = (string) $value;
                        break;
                }
            }
        }
    }

    /**
     * Log a given event by observation
     * @param mixed &$serviceReturn
     *
     * @return void
     *
     * @subject LAABS_SERVICE_RETURN
     */
    public function notifyServiceReturn(&$serviceReturn)
    {
        // Output with success      
        if (!isset($this->servicePath)) {
            return;
        }

        if (is_array($this->output)) {
            $this->output = json_encode($this->output);
        }

        $eventController = \laabs::newController('audit/event');
        $eventController->add(
            $this->servicePath->getName(),
            $this->servicePath->variables,
            $this->input,
            $this->output,
            true
        );

        $this->servicePath = null;
        $this->output = null;
        $this->input = null;
    }

    /**
     * Log a given event by observation
     * @param mixed &$businessException
     *
     * @return void
     *
     * @subject LAABS_BUSINESS_EXCEPTION
     */
    public function notifyBusinessException(&$businessException)
    {
        // Output with success      
        if (!isset($this->servicePath)) {
            return;
        }

        // Output with failure
        $output = utf8_encode($businessException->getMessage());

        $eventController = \laabs::newController('audit/event');
        $eventController->add(
            $this->servicePath->getName(),
            $this->servicePath->variables,
            $this->input,
            $output,
            false
        );
    }

    /**
     * Log a given output by observation
     * @param object &$output
     *
     * @return void
     *
     * @subject bundle\audit\AUDIT_ENTRY_OUTPUT
     */
    public function notifyOutput(&$output)
    {
        if (isset($output['variables'])) {
            if (!is_array($output['variables'])) {
                $output['variables'] = [$output['variables']];
            }
            $output['fullMessage'] = vsprintf($output['message'], $output['variables']);
        } else {
            $output['fullMessage'] = $output['message'];
        }
        $this->output[] = $output;
    }
}
