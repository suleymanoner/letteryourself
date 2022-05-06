<?php
require_once dirname(__FILE__) . '/../config.php';
require_once dirname(__FILE__) . '/../../vendor/autoload.php';

class SMTPClient
{

    private $mailer;

    /**
     * Constructor for SMTPClient class.
     */
    public function __construct()
    {
        $transport = (new Swift_SmtpTransport(Config::SMTP_HOST(), Config::SMTP_PORT(), 'tls'))
            ->setUsername(Config::SMTP_USER())
            ->setPassword(Config::SMTP_PASSWORD());
        $this->mailer = new Swift_Mailer($transport);
    }

    /**
     * Sending register token.
     * @param object person for getting person's informations.
     */
    public function send_register_token($person)
    {
        $message = (new Swift_Message('Confirm your account'))
            ->setFrom(['suyo571oner@gmail.com' => 'LetterYourself'])
            ->setTo([$person['email']])
            ->setBody('Here is the confirmation token: http://localhost/letteryourself/api/confirm/' . $person['token']);

        $this->mailer->send($message);
    }

    /**
     * Sending recovery token.
     * @param object person for getting person's informations.
     */
    public function send_person_recovery_token($person)
    {
        $message = (new Swift_Message('Reset your account'))
            ->setFrom(['suyo571oner@gmail.com' => 'LetterYourself'])
            ->setTo([$person['email']])
            ->setBody('Here is the recovery token: http://localhost/letteryourself/login.html?token=' . $person['token']);

        $this->mailer->send($message);
    }
}
