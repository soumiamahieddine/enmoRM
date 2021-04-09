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

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."PHPMailer.php";
require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."Exception.php";
require __DIR__.DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."PHPMailer".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."SMTP.php";


/**
 * Logger class
 */
class Notification implements \dependency\notification\NotificationInterface
{

    protected $mail;
    protected $mailHost;
    protected $mailUsername;
    protected $mailPassword;
    protected $mailPort;
    protected $mailSender;
    protected $mailSMTPAuth;
    protected $mailSMTPSecure;
    protected $mailAdminReceiver;
    protected $mailSMTPAutoTLS;

    public function __construct($mailHost, $mailUsername, $mailPassword, $mailPort, $mailSender, $mailAdminReceiver, $mailSMTPAuth, $mailSMTPSecure, $mailSMTPAutoTLS=true)
    {
        $this->mailHost = $mailHost;
        $this->mailUsername = $mailUsername;
        $this->mailPassword = $mailPassword;
        $this->mailPort = $mailPort;
        $this->mailSender = $mailSender;
        $this->mailSMTPAuth = $mailSMTPAuth;
        $this->mailSMTPSecure = $mailSMTPSecure;
        $this->mailAdminReceiver = $mailAdminReceiver;
        $this->mailSMTPAutoTLS = $mailSMTPAutoTLS;
    }

    /**
     * Send a notification
     * @param string $title     The title of message
     * @param string $message   The message
     * @param array  $receivers Array of receiver, if array is empty it's send to the administator
     */
    public function send($title, $message, $receivers = [])
    {
        // $mail = new \PHPMailer();
        $mail = new \PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = $this->mailHost;
        $mail->Username = $this->mailUsername;
        $mail->Password = $this->mailPassword;
        $mail->Port = $this->mailPort;
        $mail->setFrom($this->mailSender);
        $mail->SMTPAuth = $this->mailSMTPAuth;
        $mail->SMTPSecure = $this->mailSMTPSecure;
        $mail->SMTPAutoTLS = $this->mailSMTPAutoTLS;
        
        if (empty($receivers)) {
            $mail->addAddress($this->mailAdminReceiver);
        } else {
            foreach ($receivers as $receiver) {
                $mail->addAddress($receiver);
            }
        }

        $mail->Subject = $title;
        $mail->Body = $message;

        if(!$mail->send()) {
            throw new \dependency\notification\Exception($mail->ErrorInfo);
        } 
        
        return true;
    }
}