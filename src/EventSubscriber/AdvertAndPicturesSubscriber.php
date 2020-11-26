<?php


namespace App\EventSubscriber;

use App\Entity\Advert;
use App\Entity\Picture;
use App\Notification\PublishedAdvertNotification;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class AdvertAndPicturesSubscriber implements EventSubscriber
{

    private NotifierInterface $notifier;

    public function __construct(NotifierInterface $notifier){
        $this->notifier = $notifier;
    }

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if(!$entity instanceof Picture && !$entity instanceof Advert){
            return;
        }

        $entity->setCreatedAt(new DateTime());
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity =$args->getEntity();

        if($entity instanceof Advert)
        {
            if($entity->getState() === 'published') {
                $entity->setPublishedAt(new DateTime());
                $this->notifier->send(new PublishedAdvertNotification($entity), new Recipient($entity->getEmail()));
            }
        }

    }
}