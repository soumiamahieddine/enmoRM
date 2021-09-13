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

namespace bundle\batchProcessing\Controller;

/**
 * Class scheduling
 *
 * @package batchProcessing
 * @author  Alexandre Morin <alexandre.morin@maarch.org>
 */
class scheduling
{
    /* Properties */

    public $sdoFactory;

    public $logSchedulingController;

    /**
     * Constructor of access control class
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->logSchedulingController = \laabs::newController("batchProcessing/logScheduling");
    }

    /**
     * List the schedulings
     *
     * @return array Array of batchProcessing/scheduling object
     */
    public function index()
    {
        $schedulingList = $this->sdoFactory->find("batchProcessing/scheduling");

        return $schedulingList;
    }

    /**
     * List the tasks
     *
     * @return batchProcessing/task List task
     */
    public function readTaskList()
    {
        $tasks= \laabs::configuration('batchProcessing')['tasks'];

        $taskList = [];
        foreach ($tasks as $value) {
            $task = \laabs::newInstance('batchProcessing/task');
            $task->taskId = $value['taskId'];
            $task->route = $value['route'];
            $task->description = $value['description'];

            $taskList[$task->taskId] = $task;
        }

        return $taskList;
    }

    /**
     * Get task by id
     *
     * @return batchProcessing/task task
     */
    public function readTask($taskId)
    {
        $tasks= \laabs::configuration('batchProcessing')['tasks'];

        foreach ($tasks as $value) {
            if ($taskId === $value['taskId']) {
                $task = \laabs::newInstance('batchProcessing/task');
                $task->taskId = $value['taskId'];
                $task->route = $value['route'];
                $task->description = $value['description'];

                return $task;
            }
        }
        return false;
    }

    /**
     * Create the requested scheduling
     * @param batchProcessing/scheduling $scheduling
     *
     * @return boolean status of the query
     */
    public function create($scheduling)
    {

        $scheduling->schedulingId = \laabs::newId();
        $scheduling->status = 'scheduled';

        $this->sdoFactory->create($scheduling, "batchProcessing/scheduling");

        return $scheduling->schedulingId;
    }

    /**
     * Update a scheduling
     * @param batchProcessing/scheduling $scheduling
     *
     * @return bool True if the operation succeed
     */
    public function update($scheduling)
    {
        try {
            $this->sdoFactory->update($scheduling, "batchProcessing/scheduling");
        } catch (\Exception $e) {
            throw \laabs::newException("batchProcessing/schedulingException", "Scheduling not updated.");
        }

        return true;
    }

    /**
     * Delete a scheduling
     * @param id $schedulingId Scheduling identifier
     *
     * @return boolean True if the operation succeed
     */
    public function delete($schedulingId)
    {
        try {
            $this->sdoFactory->delete($schedulingId, "batchProcessing/scheduling");
        } catch (\Exception $e) {
            throw \laabs::newException("batchProcessing/schedulingException", "Scheduling not deleted.");
        }

        return true;
    }

    /**
     * Execute a scheduling task
     *
     * @param string $schedulingId The Scheduling identifier
     *
     * @return batchProcessing/scheduling The scheduling object
     */
    public function execute($schedulingId)
    {
        $success = true;
        $info = null;

        $scheduling = $this->sdoFactory->read("batchProcessing/scheduling", $schedulingId);
        $task = $this->readTask($scheduling->taskId);

        if (!$scheduling || !$task) {
            throw \laabs::newException("batchProcessing/schedulingException", "Invalid identifier.");
        }

        if ($accountToken = \laabs::getToken('AUTH')) {
            $launchedBy = $accountToken->accountId;
        } else {
            $launchedBy = "__system__";
        }

        $this->applyServiceTokens($scheduling->executedBy);

        $this->changeStatus($schedulingId, "running");

        try {
            $pathRouter = new \core\Route\PathRouter($task->route);
            \core\Observer\Dispatcher::notify(LAABS_SERVICE_PATH, $pathRouter->path);
            if (!empty($scheduling->parameters)) {
                \laabs::callServiceArgs($task->route, (array) $scheduling->parameters);
            } else {
                \laabs::callService($task->route);
            }
        } catch (\Exception $info) {
            $this->changeStatus($schedulingId, "error");
            $success = false;
            
            \laabs::notify(LAABS_BUSINESS_EXCEPTION, $info);
        }

        $this->removeServiceTokens();

        $scheduling->lastExecution = \laabs::newDateTime(null, 'UTC');
        $frequency = explode(";", $scheduling->frequency);
        $scheduling->nextExecution = $this->nextExecution($frequency);
        
        if ($success) {
            $scheduling->status = "scheduled";
        } else {
            $scheduling->status = "error";
        }

        $this->update($scheduling);

        $observerPool = \core\Observer\Dispatcher::getPool(\bundle\audit\AUDIT_ENTRY_OUTPUT);
        foreach ($observerPool as $key => $value) {
            if ($value instanceof \bundle\audit\Observer\logger) {
                $info = $value->output;
                break;
            }
        }

        $this->logSchedulingController->add($schedulingId, $scheduling->executedBy, $launchedBy, $success, $info);

        return $scheduling;
    }

    /**
     * Process all scheduling
     *
     * @return array The list of executedTask status
     */
    public function process()
    {
        $schedulings = $this->sdoFactory->find("batchProcessing/scheduling");
        $currentDate = \laabs::newDateTime(null, 'UTC');

        $res = [];

        foreach ($schedulings as $scheduling) {
            $executedTask = null;

            if ($scheduling->status == "paused") {
                continue;
            }

            if (empty($scheduling->nextExecution)) {
                $frequency = explode(";", $scheduling->frequency);
                $scheduling->nextExecution = $this->nextExecution($frequency);
                $this->sdoFactory->update($scheduling, "batchProcessing/scheduling");
            }

            $interval = $scheduling->nextExecution->getTimestamp() - $currentDate->getTimestamp();

            if ($interval <= 0) {
                $executedTask = $this->execute($scheduling->schedulingId);
            }

            if (!empty($executedTask)) {
                $res[$executedTask->schedulingId] = $executedTask->status;
            }
        }

        return $res;
    }

    /**
     * Change status
     *
     * @param type $schedulingId
     * @param type $status
     *
     * @return batchProcessing/scheduling The scheduling object
     */
    public function changeStatus($schedulingId, $status)
    {
        $scheduling = $this->sdoFactory->read("batchProcessing/scheduling", $schedulingId);

        if ($status == "paused") {
            $scheduling->nextExecution = null;
        }
        
        if (!$scheduling) {
            throw \laabs::newException("batchProcessing/schedulingException", "Invalid identifier.");
        }

        $scheduling->status = $status;
        $this->update($scheduling);

        return $scheduling;
    }

    /**
     * Date of next execution
     *
     * @param array $frequency
     * @return DateTime
     */
    private function nextExecution($frequency)
    {
        $currentDate = \laabs::newDateTime(null, 'UTC');
        $endDate = \laabs::newDateTime(null, 'UTC');
        $UTC_Offset = date('Z');

        $H_Offset = $UTC_Offset/3600;
        /**
         * [0] start Minutes
         * [1] start Hours
         * [2] Day week ex : Fri
         * [3] Day month ex : 1,2,3
         * [4] month ex : apr, may, jun
         * [5] number of case 7
         * [6] Frequence unity minute (m) or hour (h)
         * [7] end Minutes
         * [8] end Hours
         * Exemple frequency = [0;18;"Thu";"";"";"5";"m";"0";"20"];
         * Thursday 18h -> 20h every 5 Minutes
         */
        if (!empty($frequency[1])) {
            $frequency[1] -= $H_Offset;
        }
        if (!empty($frequency[8] && $frequency[8] != "00")) {
            $frequency[8] -= $H_Offset;
        }
        if (intval($frequency[1]) < 0) {
            $frequency[1] = 24 + intval($frequency[1]);
        } elseif (intval($frequency[1]) >= 24) {
            $frequency[1] = intval($frequency[1]) - 24;
        }
        if (intval($frequency[8]) < 0) {
            $frequency[8] = 24 + intval($frequency[8]);
        } elseif (intval($frequency[8]) >= 24) {
            $frequency[8] = intval($frequency[8]) - 24;
        }
        
        if ($frequency[6] != "") {
            if ($frequency[2] == "" && $frequency[3] == "") {
                $timeAdd = strtoupper("PT".$frequency[5].$frequency[6]);
                $currentDate->add(new \DateInterval($timeAdd));

                if (($frequency[7] != "" && $frequency[8] != "") && ($frequency[7] != "00" && $frequency[8] != "00")) {
                    $endDate->setTime($frequency[8], $frequency[7], "0");
                } else {
                    $endDate->add(new \DateInterval("P1D"));
                    $endDate->setTime("0", "0", "0");
                }

                $interval = $currentDate->diff($endDate, false);

                if ($interval->invert == 0) {
                    return $currentDate;
                } else {
                    $endDate->add(new \DateInterval("P1D"));
                    $endDate->setTime($frequency[1], $frequency[0], "0");

                    return $endDate;
                }
            } else {
                if ($frequency[2] != "") {
                    $daysWeek = explode(",", $frequency[2]);
                    $timeAdd = strtoupper("PT".$frequency[5].$frequency[6]);
                    $currentDate->add(new \DateInterval($timeAdd));
                    if ($frequency[7] != "" && $frequency[8] != "" && ($frequency[7] != "00" && $frequency[8] != "00")) {
                        $endDate->setTime($frequency[8], $frequency[7], "0");
                    } else {
                        $endDate->add(new \DateInterval("P1D"));
                        $endDate->setTime("0", "0", "0");
                    }
                    $interval = $currentDate->diff($endDate, false);

                    if ($interval->invert == 0) {
                        return $currentDate;
                    } else {
                        if (sizeof($daysWeek) == 1) {
                            $endDate->add(new \DateInterval("P7D"));
                            $endDate->setTime($frequency[1], $frequency[0], "0");
                        } else {
                            $endDate = $this->nextDayOfWeek($daysWeek, $currentDate);
                            $endDate->setTime($frequency[1], $frequency[0], "0");
                        }

                        return $endDate;
                    }
                } else {
                    $dayMonths = explode(",", $frequency[3]);
                    $timeAdd = strtoupper("PT".$frequency[5].$frequency[6]);
                    $currentDate->add(new \DateInterval($timeAdd));

                    if ($frequency[7] != "" && $frequency[8] != "" && ($frequency[7] != "00" && $frequency[8] != "00")) {
                        $endDate->setTime($frequency[8], $frequency[7], "0");
                    } else {
                        $endDate->add(new \DateInterval("P1M"));
                        $endDate->setTime("0", "0", "0");
                    }
                    $interval = $currentDate->diff($endDate, false);

                    if ($interval->invert == 0) {
                        return $currentDate;
                    } else {
                        if (count($dayMonths) == 1) {
                            $endDate->add(new \DateInterval("P1M"));
                            $endDate->setTime($frequency[1], $frequency[0], "0");
                        } else {
                            $endDate = $this->nextDayOfMonth($dayMonths);
                            $endDate->setTime($frequency[1], $frequency[0], "0");
                        }

                        if ($frequency[4] != "") {
                            $currentMonth = $endDate->format("m");
                            $monthInterval = $this->getNextMonthInterval($currentMonth, $frequency[4]);
                            $endDate->add(new \DateInterval("P".$monthInterval."M"));
                        }

                        return $endDate;
                    }
                }
            }
        } else {
            if ($frequency[2] != "") {
                $daysWeek = explode(",", $frequency[2]);

                if (count($daysWeek) == 1) {
                    $endDate->add(new \DateInterval("P7D"));
                    $endDate->setTime($frequency[1], $frequency[0], "0");
                } else {
                    $endDate = $this->nextDayOfWeek($daysWeek, $currentDate);
                    $endDate->setTime($frequency[1], $frequency[0], "0");
                }
            } elseif ($frequency[3] != "" && $frequency[4] != "") {
                $dayMonths = explode(",", $frequency[3]);

                if (count($dayMonths) == 1) {
                    $endDate->add(new \DateInterval("P1M"));
                    $endDate->setTime($frequency[1], $frequency[0], "0");
                } else {
                    $endDate = $this->nextDayOfMonth($dayMonths);
                    $endDate->setTime($frequency[1], $frequency[0], "0");
                }

                $currentMonth = $endDate->format("m");
                $monthInterval = $this->getNextMonthInterval($currentMonth, $frequency[4]);
                $endDate->add(new \DateInterval("P".$monthInterval."M"));
            } elseif ($frequency[3] != "") {
                $dayMonths = explode(",", $frequency[3]);

                if (count($dayMonths) == 1) {
                    $endDate->add(new \DateInterval("P1M"));
                    $endDate->setTime($frequency[1], $frequency[0], "0");
                } else {
                    $endDate = $this->nextDayOfMonth($dayMonths);
                    $endDate->setTime($frequency[1], $frequency[0], "0");
                }
            } else {
                $endDate->add(new \DateInterval("P1D"));
                $endDate->setTime($frequency[1], $frequency[0], "0");
            }

            return $endDate;
        }
    }

    /**
     * Next day of execution (Week)
     *
     * @param array    $daysWeek
     * @param Datetime $currentDate
     * @return DateTime
     */
    private function nextDayOfWeek($daysWeek, $currentDate)
    {
        $currentTime = strtotime($currentDate->format("Y-m-d\TH:i:s"));
        $nextDayTime = 0;

        foreach ($daysWeek as $day) {
            $dayTime = strtotime($day.",".$currentDate->format("Y-m-d\TH:i:s"));
            if ($dayTime != $currentTime) {
                if (!$nextDayTime || $dayTime < $nextDayTime) {
                    $nextDayTime = $dayTime;
                }
            }
        }
        $nextDate = \laabs::newDateTime('UTC');
        $nextDate->setTimestamp($nextDayTime);

        return $nextDate;
    }

    /**
     * Next day of execution (Month)
     *
     * @param array $dayMonths
     * @return DateTime
     */
    private function nextDayOfMonth($dayMonths)
    {
        $lastDayOfMonth = date('t', strtotime('today'));
        $currentDayNum = strtoupper(date("d"));
        $endDate = \laabs::newDateTime('UTC');
        $totalMore = 0;
        $totalLess = 0;

        foreach ($dayMonths as $dayMonth) {
            if ($dayMonth > $lastDayOfMonth) {
                $dayMonth = $lastDayOfMonth;
            }
            $dayDiff = $dayMonth - $currentDayNum;

            if ($dayDiff > 0) {
                if ($totalMore == 0 || $totalMore > $dayDiff) {
                    $totalMore = $dayDiff;
                }
            } else {
                if ($totalLess > $dayDiff) {
                    $totalLess = $dayDiff;
                }
            }
        }

        if ($totalMore != 0) {
            $endDate->add(new \DateInterval("P".$totalMore."D"));

            return $endDate;
        } else {
            $totalLess = $totalLess * -1;
            $endDate->add(new \DateInterval("P1M"));
            $endDate->sub(new \DateInterval("P".$totalLess."D"));

            return $endDate;
        }
    }

    /**
     * Get positive interval of next month in frequency
     *
     * @param int    $currentMonth The numerical current month
     * @param string $frequency    Months in string with comma separator
     *
     * @return int The interval between the current month and the next month in frequency
     */
    private function getNextMonthInterval($currentMonth, $frequency)
    {
        $months = \laabs\explode(',', $frequency);
        $numericMonths = [];

        foreach ($months as $month) {
            $numericMonths[] = (int) date('m', strtotime($month))-1;
        }

        sort($numericMonths);

        for ($i = 0; !in_array(($currentMonth -1 + $i) % 12, $numericMonths); $i++);

        return $i;
    }

    protected function removeServiceTokens()
    {
        unset($GLOBALS["TOKEN"]['AUTH']);
        unset($GLOBALS["TOKEN"]['ORGANIZATION']);
    }
    
    private function applyServiceTokens($serviceAccountId)
    {
        $account = $this->sdoFactory->read("auth/account", $serviceAccountId);

        if (!$account->enabled) {
            throw \laabs::newException("auth/authenticationException", "Service account disabled");
        }

        $cryptedToken = base64_decode($account->password);
        $jsonToken = \laabs::decrypt($cryptedToken, \laabs::getCryptKey());
        $authToken= json_decode(trim($jsonToken));
        $GLOBALS["TOKEN"]['AUTH'] = $authToken;

        if ($account->accountType == "service") {
            $servicePositionController = \laabs::newController("organization/servicePosition");
            $servicePosition = $servicePositionController->getPosition($serviceAccountId);

            if ($servicePosition != null) {
                $orgToken = new \core\token($servicePosition->organization, 0);
                $GLOBALS["TOKEN"]["ORGANIZATION"] = $orgToken;
            }
        }
    }
}
