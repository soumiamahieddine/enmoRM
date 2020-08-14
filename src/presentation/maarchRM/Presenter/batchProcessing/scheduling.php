<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle batchProcessing.
 *
 * Bundle batchProcessing is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle batchProcessing is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle batchProcessing.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace presentation\maarchRM\Presenter\batchProcessing;

/**
 * search batchProcessing html serializer
 *
 * @package batchProcessing
 * @author Prosper DE LAURE <prosper.delaure@maarch.org>
 */
class scheduling
{
    use \presentation\maarchRM\Presenter\exceptions\exceptionTrait;

    public $view;
    public $json;
    protected $translator;

    /**
     * Constuctor of batchProcessing html serializer
     * @param \dependency\html\Document                    $view       The view
     * @param \dependency\json\JsonObject                  $json       The Json object
     * @param \dependency\localisation\TranslatorInterface $translator The translator service
     */
    public function __construct(\dependency\html\Document $view, \dependency\json\JsonObject $json, \dependency\localisation\TranslatorInterface $translator)
    {
        $this->view = $view;
        $this->json = $json;
        $this->translator = $translator;
        $this->translator->setCatalog('batchProcessing/messages');
    }

    /**
     * get a form to search resource
     * @param array $scheduledTasks Array of scheduled tasks
     *
     * @return string
     */
    public function index($scheduledTasks)
    {
        $this->view->addContentFile("batchProcessing/scheduling/scheduling.html");

        $tasks = \laabs::callService('batchProcessing/scheduling/readTasks');

        $serviceAccounts = \laabs::callService('auth/serviceAccount/readSearch');

        foreach ($serviceAccounts as $key => $serviceAccount) {
            $serviceURI = [];
            $privileges = \laabs::callService('auth/serviceAccount/readPrivilege_serviceAccountId_', $serviceAccount->accountId);
            if (!$serviceAccount->enabled) {
                unset($serviceAccounts[$key]);
                continue;
            }
            foreach ($privileges as $privilege) {
                $serviceURI[] = $privilege->serviceURI;
            }
            $serviceAccount->privileges = json_encode($serviceURI);
        }

        foreach ($scheduledTasks as $scheduledTask) {
            $scheduledTask->taskName = $tasks[$scheduledTask->taskId]->description;
            $frequency = explode(';', $scheduledTask->frequency);

            $scheduledTask->startMinutes = $frequency[0];
            $scheduledTask->startHours = $frequency[1];

            if ($frequency[2] != '') {
                $scheduledTask->weekDays = explode(',', $frequency[2]);
            } else {
                $scheduledTask->weekDays = array();
            }

            if ($frequency[3] != '') {
                $scheduledTask->monthDays = explode(',', $frequency[3]);
            } else {
                $scheduledTask->monthDays = array();
            }
            $scheduledTask->monthDaysText = implode(', ', $scheduledTask->monthDays);

            if ($frequency[4] != '') {
                $scheduledTask->month = explode(',', $frequency[4]);
                $translateMonth = [];
                foreach ($scheduledTask->month as $month) {
                    $translateMonth[] = $this->translator->getText(strtoupper($month), "month");
                }
                $scheduledTask->month = $translateMonth;
            } else {
                $scheduledTask->month = array();
            }
            $scheduledTask->monthText = implode(', ', $scheduledTask->month);

            $scheduledTask->frequencyNumber = $frequency[5];
            $scheduledTask->frequencyUnit = $frequency[6];
            $scheduledTask->endMinutes = $frequency[7];
            $scheduledTask->endHours = $frequency[8];

            $scheduledTask->json = json_encode($scheduledTask);
        }

        $accountId = \laabs::getToken("AUTH")->accountId;
        $account = \laabs::callService("auth/userAccount/read_userAccountId_", $accountId);
        $hasSecurityLevel = isset(\laabs::configuration('auth')['useSecurityLevel']) ? (bool) \laabs::configuration('auth')['useSecurityLevel'] : false;

        if (is_null($account->securityLevel)
            || !$account->securityLevel === \bundle\auth\Model\account::SECLEVEL_USER
            || !$hasSecurityLevel
        ) {
            $isUser = false;
        } else {
            $isUser = true;
        }

        $this->view->translate();
        $this->view->setSource("serviceAccount", $serviceAccounts);
        $this->view->setSource("tasks", $tasks);
        $this->view->setSource("scheduledTasks", $scheduledTasks);
        $this->view->setSource("timezone", date_default_timezone_get());
        $this->view->setSource("isUser", $isUser);
        $this->view->merge();

        return $this->view->saveHtml();
    }

    //JSON
    /**
     * Serializer JSON for create method
     * @param bool $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function create($result)
    {
        $this->json->status = true;
        $this->json->result = $result;

        $this->json->message = "New scheduling created";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for update method
     * @param object $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function update($result)
    {
        $this->json->status = $result;
        $this->json->message = "Scheduling updated";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for delete method
     * @param object $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function delete($result)
    {
        $this->json->status = $result;
        $this->json->message = "Scheduling deleted";
        $this->json->message = $this->translator->getText($this->json->message);

        return $this->json->save();
    }

    /**
     * Serializer JSON for execute method
     * @param bool $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function execute($result)
    {
        if ($result->status == "error") {
            $this->json->status = false;
            $this->json->message = "An error occurred during execution";
        } else {
            $this->json->status = true;
            $this->json->message = "Task execution triggered";
        }

        $this->json->message = $this->translator->getText($this->json->message);
        $result->lastExecution = \laabs::newDateTime($result->lastExecution)->setTimezone(timezone_open(date_default_timezone_get()));
        $result->lastExecution = $result->lastExecution->format("Y-m-d H:i:s P");

        $result->nextExecution = \laabs::newDateTime($result->nextExecution)->setTimezone(timezone_open(date_default_timezone_get()));
        $result->nextExecution = $result->nextExecution->format("Y-m-d H:i:s P");
        $this->json->object = $result;

        return $this->json->save();
    }

    /**
     * Serializer JSON for update status
     * @param object $result
     *
     * @return object JSON object with a status and message parameters
     */
    public function changeStatus($result)
    {
        $this->json->status = true;
        $this->json->message = "Status updated";
        $this->json->message = $this->translator->getText($this->json->message);
        $this->json->object = $result;

        return $this->json->save();
    }
}
