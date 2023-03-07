<?php

namespace Devvime\KiichiPhpEngine;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService 
{
    public $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->mail->isSMTP();
        $this->mail->setLanguage("br");
        $this->mail->Charset = "utf-8";
        $this->mail->SMTPAuth   = true;
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Host       = EMAIL_HOST;
        $this->mail->Username   = EMAIL_USER;
        $this->mail->Password   = EMAIL_PASSWORD;
        $this->mail->Port       = EMAIL_PORT;
    }

    public function set($email,$name)
    {
        $this->mail->setFrom($email,$name);
    }

    public function add($email,$name)
    {
        $this->mail->addAddress($email,$name);
    }

    public function content($subject, $body, $altbody)
    {
        $this->mail->isHTML(true);
        $this->mail->Subject = $subject;
        $this->mail->Body    = $body;
        $this->mail->AltBody = $altbody;
    }

    public function send()
    {
        $this->mail->send();
    }
}