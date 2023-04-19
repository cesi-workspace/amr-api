<?php

namespace App\Subscriber;

use App\Entity\User;
use App\Service\CryptService;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class UtilisateurSubscriber implements EventSubscriber
{
    private CryptService $cryptService;

    public function __construct(CryptService $cryptService)
    {
        $this->cryptService = $cryptService;
    }
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist => 'prePersist',
            Events::preUpdate => 'preUpdate',
            Events::postLoad => 'postLoad',
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof User) {
            return;
        }
        $this->encryptUser($entity);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof User) {
            return;
        }
        $this->encryptUser($entity);
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof User) {
            return;
        }
        $this->decryptUser($entity);
    }

    public function decryptUser(User $user)
    {
        $surname=$user->getSurname();
        $firstname=$user->getFirstname();
        $email = $user->getEmail();
        $codepostal = $user->getPostalCode();
        $ville = $user->getCity();

        $user->setEmail(
            $this->cryptService->decrypt($email)
        );
        $user->setPostalCode(
            $this->cryptService->decrypt($codepostal)
        );
        $user->setCity(
            $this->cryptService->decrypt($ville)
        );
        $user->setSurname(
            $this->cryptService->decrypt($surname)
        );
        $user->setFirstname(
            $this->cryptService->decrypt($firstname)
        );
    }

    public function encryptUser(User $user)
    {
        $surname=$user->getSurname();
        $firstname=$user->getFirstname();
        $email = $user->getEmail();
        $codepostal = $user->getPostalCode();
        $ville = $user->getCity();

        $user->setEmail(
            $this->cryptService->encrypt($email)
        );
        $user->setPostalCode(
            $this->cryptService->encrypt($codepostal)
        );
        $user->setCity(
            $this->cryptService->encrypt($ville)
        );
        $user->setSurname(
            $this->cryptService->encrypt($surname)
        );
        $user->setFirstname(
            $this->cryptService->encrypt($firstname)
        );
    }
}