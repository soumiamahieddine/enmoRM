<?php
/*
 * Copyright (C) 2016 Maarch
 *
 * This file is part of dependency notification.
 *
 * Dependency notification is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Dependency notification is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with dependency notification.  If not, see <http://www.gnu.org/licenses/>.
 */
namespace dependency\notification;

/**
 * Notification interface
 *
 * @package dependency\logger
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
interface NotificationInterface
{
    /**
     * Notify
     * @param array  $receivers Array of receiver
     * @param string $title     The title of message
     * @param string $message   The message
     */
    public function notify($receivers, $title, $message);
}
