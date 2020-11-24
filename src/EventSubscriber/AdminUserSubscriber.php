<?php


namespace App\EventSubscriber;


use App\Entity\AdminUser;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserSubscriber implements EventSubscriberInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $this->setPassword($args->getEntity());
    }

    public function preUpdate(LifecycleEventArgs $args):void
    {
        $this->setPassword($args->getEntity());
    }

    private function setPassword($entity){
        if (!$entity instanceof AdminUser) {
            return;
        }

        if($entity->getPlainPassword() !== '' || $entity->getPlainPassword() !== null){
            $entity->setPassword( $encoded = $this->passwordEncoder->encodePassword(
                $entity,
                $entity->getPlainPassword()
            ));
        }
    }


    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate
        ];
    }
}