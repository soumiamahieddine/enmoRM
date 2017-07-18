<?php
/*
 * Copyright (C) 2017 Maarch
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
namespace dependency\notification\Adapter\Mailer;

require_once __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer-master".DIRECTORY_SEPARATOR."PHPMailerAutoload.php";

/**
 * Logger class
 */
class Notification implements \dependency\notification\NotificationInterface
{

    protected $mail;
    protected $mailAdminReceiver;

    public function __construct($mailHost, $mailUsername, $mailPassword, $mailPort, $mailSender, $mailAdminReceiver, $mailSMTPAuth, $mailSMTPSecure)
    {
        $this->mail = new \PHPMailer();
        $this->mail->isSMTP();
        $this->mail->Host = $mailHost;
        $this->mail->Username = $mailUsername;
        $this->mail->Password = $mailPassword;
        $this->mail->Port = $mailPort;
        $this->mail->setFrom($mailSender);
        $this->mail->SMTPAuth = $mailSMTPAuth;
        $this->mail->SMTPSecure = $mailSMTPSecure;
        $this->mailAdminReceiver = $mailAdminReceiver;
    }

    /**
     * Send a notification
     * @param string $title     The title of message
     * @param string $message   The message
     * @param array  $receivers Array of receiver, if array is empty it's send to the administator
     */
    public function send($title, $message, $receivers = [])
    {
        if (empty($receivers)) {
            $this->mail->addAddress($this->mailAdminReceiver);
        } else {
            foreach ($receivers as $receiver) {
                $this->mail->addAddress($receiver);
            }
        }

        $this->mail->Subject = $title;
        $this->mail->Body = $message;

        $this->mail->Timeout = 15;
        
        if(!$this->mail->send()) {
            throw new \dependency\notification\Exception($this->mail->ErrorInfo);
        } 
        
        return true;
    }
}