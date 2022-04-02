<?php
namespace App\Services;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use Latte\Engine;

class Mailler
{
    private Message $mail;
    private SmtpMailer $mailer;
    private $file_name;
    private $subject;
    private $emailAddress;
    private $pushData;
    private Engine $latte;


    function __construct($email, $subject, $file_name, $data = array()){
        $this->emailAddress = $email;
        $this->subject = $subject;
        $this->file_name = $file_name;
        $this->pushData = $data;

    }

    public function SendMail(){
        $this->mail = new Message;
        $this->latte = new Engine;
        $this->mail->setFrom(getEnvData('FROM_EMAIL'), $this->subject)
            ->addTo($this->emailAddress)
            ->setSubject($this->subject)
            ->setHtmlBody(
                $this->latte->renderToString(__DIR__. '/../../resources/views/emails/' .$this->file_name.'.latte', $this->pushData),
                __DIR__. '/../../resources/views/emails/images'
            );


        $this->mailer = new SmtpMailer([
            'host' => getEnvData('MAIL_HOST'),
            'port' => getEnvData('PORT'),
            'username' => getEnvData('MAIL_USERNAME'),
            'password' => getEnvData('MAIL_PASSWORD'),
            'secure' => getEnvData('MAIL_ENCRYPTION')
        ]);

        $this->mailer->send($this->mail);
    }

}
