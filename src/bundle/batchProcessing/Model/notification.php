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
namespace bundle\batchProcessing\Model;
/**
 *
 * @package BatchProcessing
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 * 
 * @pkey [notificationId]
 */
class notification
{
    /**
     * The notification identifier
     *
     * @var string
     * @notempty
     */
    public $notificationId;
    
    /**
     * The title
     *
     * @var string
     * @notempty
     */
    public $title;

    /**
     * The message
     *
     * @var string
     * @notempty
     */
    public $message;

    /**
     * The receivers
     *
     * @var string
     * @notempty
     */
    public $receivers;

    /**
     * The status
     *
     * @var string
     * @notempty
     */
    public $status;

    /**
     * The creation date
     *
     * @var timestamp
     * @notempty
     */
    public $createdDate;

    /**
     * The creator
     *
     * @var string
     */
    public $createdBy;

    /**
     * The send date
     *
     * @var timestamp
     */
    public $sendDate;

    /**
     * The sender
     *
     * @var string
     */
    public $sendBy;
}