<?php
/*
 * Copyright (C) 2017 Maarch
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
namespace bundle\batchProcessing;

/**
 * Standard interface for a task class
 */
interface notificationInterface
{
    /**
     * Create a notification in the stack
     * @param string $title     The title of the notification
     * @param string $message   The message of the notification
     * @param array  $receivers The receivers of the notification
     *
     * @action batchProcessing/notification/create
     */
    public function create($title, $message, $receivers);

    /**
     * Process notifications in the stack
     *
     * @action batchProcessing/notification/process
     */
    public function updateProcess();
}
