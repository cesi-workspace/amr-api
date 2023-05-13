<?php

namespace App\Subscriber;

use App\Entity\Connection;
use App\Service\CryptService;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Common\EventSubscriber;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class ConnectionSubscriber implements EventSubscriber
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
        if (!$entity instanceof Connection) {
            return;
        }
        $this->encryptConnection($entity);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Connection) {
            return;
        }
        $this->encryptConnection($entity);
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (!$entity instanceof Connection) {
            return;
        }
        $this->decryptConnection($entity);
    }

    public function decryptConnection(Connection $connection)
    {
        $ip_address=$connection->getIpAddress();

        $connection->setIpAddress(
            $this->cryptService->decrypt($ip_address)
        );
    }

    public function encryptConnection(Connection $connection)
    {
        $ip_address=$connection->getIpAddress();

        $connection->setIpAddress(
            $this->cryptService->encrypt($ip_address)
        );
    }
}