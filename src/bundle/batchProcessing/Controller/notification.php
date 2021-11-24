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

namespace bundle\batchProcessing\Controller;

/**
 * Class notification
 *
 * @package batchProcessing
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class notification
{
    /**
     * The status of the new notification
     */
    const _NEW_ = "NEW";

    /**
     * The status of the notification in processing
     */
    const _PROCESSING_ = "PROCESSING";

    /**
     * The ended status of the notification when it's finish
     */
    const _CLOSE_ = "CLOSE";

    public $sdoFactory;
    public $notificationDependency;

    /**
     * Constructor of notification class
     * @param \dependency\sdo\Factory $sdoFactory The factory
     */
    public function __construct(\dependency\sdo\Factory $sdoFactory)
    {
        $this->sdoFactory = $sdoFactory;
        $this->notificationDependency = \laabs::newService("dependency/notification/Notification");
    }
    
    /**
     * Create a notification in the stack
     * @param string $title     The title of the notification
     * @param string $message   The message of the notification
     * @param array  $receivers The receivers of the notification
     *
     * @return The notification identifier
     */
    public function create($title, $message, $receivers)
    {
        $notification = \laabs::newInstance("batchProcessing/notification");
        $notification->notificationId = \laabs::newId();
        $notification->status = self::_NEW_;
        $notification->createdDate = \laabs::newDateTime();

        if (!empty(\laabs::getToken("AUTH")->accountId)) {
            $notification->createdBy = \laabs::getToken("AUTH")->accountId;
        }

        $notification->title = $title;
        $notification->message = $message;
        $notification->receivers = json_encode($receivers);

        $this->sdoFactory->create($notification, "batchProcessing/notification");

        return $notification->notificationId;
    }

    /**
     * Process all notifications
     */
    public function process()
    {
        $queryString = "status='".self::_NEW_."'";
        $notifications = $this->sdoFactory->find("batchProcessing/notification", $queryString, [], "createdDate <");
        
        foreach ($notifications as $notification) {
            $notification->status = self::_PROCESSING_;
            $this->sdoFactory->update($notification, "batchProcessing/notification");
            $this->notificationDependency->send($notification->title, $notification->message, json_decode($notification->receivers));
            $notification->status = self::_CLOSE_;
            $notification->sendDate = \laabs::newDateTime();
            $notification->sendBy = \laabs::getToken("AUTH")->accountId;
            $this->sdoFactory->update($notification, "batchProcessing/notification");
        }

        return count($notifications);
    }
}
