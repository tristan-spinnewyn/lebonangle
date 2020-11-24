<?php


namespace App\EventSubscriber;


use App\Entity\Advert;
use App\Entity\Picture;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class CreatedAtSubscriber implements EventSubscriber
{

    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
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
}