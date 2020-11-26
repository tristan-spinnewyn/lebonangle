<?php

declare(strict_types=1);

namespace App\Notification;

use App\Entity\Advert;
use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Notification\EmailNotificationInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;

class PublishedAdvertNotification extends Notification implements EmailNotificationInterface
{
    private Advert $advert;

    public function __construct(Advert $advert)
    {
        $this->advert = $advert;

        parent::__construct('Your advert published');
    }

    public function asEmailMessage(Recipient $recipient, string $transport = null): ?EmailMessage
    {
        $message = EmailMessage::fromNotification($this, $recipient);
        if (null !== $transport) {
            $message->transport($transport);
        }

        $message
            ->getMessage()
            ->htmlTemplate('emails/advert_published.html.twig')
            ->context(['advert' => $this->advert]);

        return $message;
    }

    public function getChannels(Recipient $recipient): array
    {
        return ['email'];
    }
}
