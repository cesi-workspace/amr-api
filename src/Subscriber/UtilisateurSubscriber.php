<?php

namespace App\Subscriber;

use App\Entity\Utilisateur;
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
        if (!$entity instanceof Utilisateur) {
            return;
        }
        $this->encryptUtilisateur($entity);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Utilisateur) {
            return;
        }
        $this->encryptUtilisateur($entity);
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Utilisateur) {
            return;
        }
        $this->decryptUtilisateur($entity);
    }

    public function decryptUtilisateur(Utilisateur $utilisateur)
    {

        $email = $utilisateur->getEmail();
        $codepostal = $utilisateur->getCodePostal();
        $ville = $utilisateur->getVille();

        $utilisateur->setEmail(
            $this->cryptService->decrypt($email)
        );
        $utilisateur->setCodePostal(
            $this->cryptService->decrypt($codepostal)
        );
        $utilisateur->setVille(
            $this->cryptService->decrypt($ville)
        );
    }

    public function encryptUtilisateur(Utilisateur $utilisateur)
    {

        $email = $utilisateur->getEmail();
        $codepostal = $utilisateur->getCodePostal();
        $ville = $utilisateur->getVille();

        $utilisateur->setEmail(
            $this->cryptService->encrypt($email)
        );
        $utilisateur->setCodePostal(
            $this->cryptService->encrypt($codepostal)
        );
        $utilisateur->setVille(
            $this->cryptService->encrypt($ville)
        );
    }
}