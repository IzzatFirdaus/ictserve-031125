<?php

declare(strict_types=1);

namespace App\Mail\Transport;

use App\Services\GmailService;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\RawMessage;

class GmailTransport implements TransportInterface
{
    public function __construct(
        private GmailService $gmailService
    ) {}

    public function send(RawMessage $message, ?Envelope $envelope = null): ?SentMessage
    {
        if (! $message instanceof Email) {
            throw new \InvalidArgumentException('Gmail transport only supports Email messages');
        }

        $to = $envelope?->getRecipients()[0]?->getAddress() ?? $message->getTo()[0]->getAddress();
        $subject = $message->getSubject() ?? '';
        $body = $message->getHtmlBody() ?? $message->getTextBody() ?? '';
        $from = $envelope?->getSender()?->getAddress() ?? $message->getFrom()[0]->getAddress();

        try {
            $messageId = $this->gmailService->sendEmail($to, $subject, $body, $from);

            return new SentMessage($message, $envelope ?? Envelope::create($message));
        } catch (\Exception $e) {
            throw new \Symfony\Component\Mailer\Exception\TransportException(
                'Failed to send email via Gmail API: '.$e->getMessage(),
                0,
                $e
            );
        }
    }

    public function __toString(): string
    {
        return 'gmail';
    }
}
