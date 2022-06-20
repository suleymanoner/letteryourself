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
            ->setFrom(['suleymanoner1999@gmail.com' => 'LetterYourself'])
            ->setTo([$person['email']])
            ->setBody('Here is the confirmation token: https://letteryourself.herokuapp.com/confirmation.html?token=' . $person['token']);

        $this->mailer->send($message);
    }

    /**
     * Sending recovery token.
     * @param object person for getting person's informations.
     */
    public function send_person_recovery_token($person)
    {
        $message = (new Swift_Message('Reset your password'))
            ->setFrom(['suleymanoner1999@gmail.com' => 'LetterYourself'])
            ->setTo([$person['email']])
            ->setBody('Here is the recovery token: https://letteryourself.herokuapp.com/login.html?token=' . $person['token']);

        $this->mailer->send($message);
    }
}
