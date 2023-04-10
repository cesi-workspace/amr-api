<?php

namespace App\Subscriber;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Event as JWTEvent;
class JWTSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            // the priority must be greater than the Security HTTP
            // ExceptionListener, to make sure it's called before
            // the default exception listener
            JWTEvent\JWTInvalidEvent::class => 'onJWTInvalidEvent'
        ];
    }

    public function onJWTInvalidEvent(JWTEvent\JWTInvalidEvent $event): void
    {
        dd('test');
    }
}