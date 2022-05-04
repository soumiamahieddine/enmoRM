<?php

/*
 * Copyright (C) 2015 Maarch
 *
 * This file is part of bundle medona
 *
 * Bundle medona is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Bundle medona is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with bundle medona.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace bundle\medona\Controller;

/**
 * Class for archiveAuthorizationTrait
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class AuthorizationRequestReply extends abstractMessage
{
    /**
     * Send a new authorization originating agency request reply
     * @param string $requestIdentifier The request message identifier
     * @param string $replyCode         The reply code
     *
     * @return The reply message generated
     */
    public function sendOriginatingAgency($requestIdentifier, $replyCode = "000")
    {
        return $this->send($requestIdentifier, $replyCode, "AuthorizationOriginatingAgencyRequestReply");
    }


    /**
     * Send a new authorization authority request reply
     * @param string $requestIdentifier The request message identifier
     * @param string $replyCode         The reply code
     *
     * @return The reply message generated
     */
    public function sendControlAuthority($requestIdentifier, $replyCode = "000")
    {
        return $this->send($requestIdentifier, $replyCode, "AuthorizationControlAuthorityRequestReply");
    }

    /**
     * Send a new authorization request reply
     * @param string $requestIdentifier The request message identifier
     * @param string $replyCode         The reply code
     * @param string $messageType       The message type
     * @return medona/message The reply message generated
     */
    private function send($requestIdentifier, $replyCode, $messageType)
    {
        $requestMessage = $this->read($requestIdentifier);

        $message = \laabs::newInstance('medona/message');
        $message->messageId = \laabs::newId();
        $message->type = $messageType;
        $message->replyCode = $replyCode;
        $message->schema = $requestMessage->schema;
        $message->status = "sent";

        $message->date = \laabs::newDatetime(null, "UTC");
        $message->receptionDate = $message->date;

        $message->reference = $requestMessage->reference.'_Reply_'.date("Y-m-d_H-i-s");
        $message->requestReference = $requestMessage->reference;
        $requestMessage->replyReference = $message->reference;

        $message->senderOrgRegNumber = $requestMessage->recipientOrgRegNumber;
        $message->recipientOrgRegNumber = $requestMessage->senderOrgRegNumber;

        $message->senderOrgName = $requestMessage->recipientOrgName;
        $message->recipientOrgName = $requestMessage->senderOrgName;

        try {
            $this->generate($message);
            $this->save($message);

            $this->update($requestMessage);
        } catch (\Exception $e) {
            $message->status = "invalid";
            $this->create($message);
            $this->logValidationErrors($message, $e);

            throw $e;
        }

        $this->create($message);

        return $message;
    }
}
