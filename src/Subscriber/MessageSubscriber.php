<?php

namespace App\Subscriber;

use App\Entity\Message;
use App\Service\CryptService;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class MessageSubscriber implements EventSubscriber
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
        if (!$entity instanceof Message) {
            return;
        }
        $this->encryptMessage($entity);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Message) {
            return;
        }
        $this->encryptMessage($entity);
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Message) {
            return;
        }
        $this->decryptMessage($entity);
    }

    public function decryptMessage(Message $message)
    {
        $content=$message->getContent();

        $message->setContent(
            $this->cryptService->decrypt($content)
        );
    }

    public function encryptMessage(Message $message)
    {
        $content=$message->getContent();

        $message->setContent(
            $this->cryptService->encrypt($content)
        );
    }
}