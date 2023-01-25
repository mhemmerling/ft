<?php

namespace App\Message\Application;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class SendMessageNotificationHandler
{
    private const EMAIL_FROM = 'test@localhost';

    public function __construct(
        private readonly MailerInterface $mailer
    ) {
    }

    public function __invoke(MessageSentEvent $message)
    {
        $recipients = $message->getMessage()->getMessageRecipients();

        foreach ($recipients as $recipient) {
            $this->mailer->send(
                (new Email())
                    ->from(self::EMAIL_FROM)
                    ->to($recipient->getUser()->getEmail())
                    ->subject('You have a new message')
                    ->text('You have a new message with title: '.$message->getMessage()->getTitle())
            );
        }
    }
}
