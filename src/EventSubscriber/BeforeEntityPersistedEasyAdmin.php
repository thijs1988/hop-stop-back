<?php


namespace App\EventSubscriber;


use App\Entity\Transactions;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeCrudActionEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class BeforeEntityPersistedEasyAdmin implements EventSubscriberInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['setPassword'],
            BeforeEntityUpdatedEvent::class => ['updatePassword'],
        ];
    }

    public function setPassword(BeforeEntityPersistedEvent $event){
        $entity = $event->getEntityInstance();

        if (!($entity instanceof User)) {
            return;
        }

        $password = $this->passwordEncoder->encodePassword($entity, $entity->getPlainPassword());

        $entity->setPassword($password);
    }

    public function updatePassword(BeforeEntityUpdatedEvent $event){
        $entity = $event->getEntityInstance();
        if (!($entity instanceof User)) {
            return;
        }

        $password = $this->passwordEncoder->encodePassword($entity, $entity->getPassword());

        $entity->setPassword($password);
    }
}

