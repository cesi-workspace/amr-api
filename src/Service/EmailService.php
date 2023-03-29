<?php

namespace App\Service;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * Summary of EmailService
 */
class EmailService
{


    public function __construct(private MailerInterface $mailerInterface, private string $to, private string $from){

    }

    public function sendText(string $from = "", string $to = "", string $subject = "", string $text = "")
    {
        $email = (new Email())->from($from == "" ? $this->from : $from)
        ->to($to == "" ? $this->to : $to)
        ->subject($subject)
        ->text($text);
     
        $this->mailerInterface->send($email);
    }

    public function sendHtml(string $from = "", string $to = "", string $subject = "", string $html = "")
    {
        $email = (new Email())->from($from == "" ? $this->from : $from)
        ->to($to == "" ? $this->to : $to)
        ->subject($subject)
        ->html($html);
     
        $this->mailerInterface->send($email);
    }

    public function sendTemplate(string $from = "", string $to = "", string $subject = "", string $twigname = "", array $data = []){
        $email = (new TemplatedEmail())->from($from ? $this->from : $from)
        ->to($to == "" ? $this->to : $to)
        ->subject($subject)
        ->htmlTemplate($twigname)
        ->context($data);
        $this->mailerInterface->send($email);
    }

}