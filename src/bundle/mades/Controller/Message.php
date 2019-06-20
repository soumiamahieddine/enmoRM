<?php
/* 
 * Copyright (C) Maarch
 *
 * This file is part of bundle Mades
 *
 * Bundle Mades is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle Mades is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle Mades. If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\mades\Controller;

/**
 * Class for archive transfer
 *
 * @package Mades
 * @author  Alexis Ragot <alexis.ragot@maarch.org>
 */
abstract class Message
{
    /**
     * Load a message
     * @param medona\message $message The message object
     */
    public function loadMessage($message)
    {
        $data = file_get_contents($message->path);

        $message->object = json_decode($data);
        $message->object->binaryDataObject = get_object_vars($message->object->dataObjectPackage->binaryDataObject);
        $message->object->descriptiveMetadata = get_object_vars($message->object->dataObjectPackage->descriptiveMetadata);
    }

    protected function sendError($code, $message = false)
    {
        if ($message) {
            array_push($this->errors, new \core\Error($message, null, $code));
        } else {
            array_push($this->errors, new \core\Error($this->getReplyMessage($code), null, $code));
        }

        if ($this->replyCode == null) {
            $this->replyCode = $code;
        }
    }
}