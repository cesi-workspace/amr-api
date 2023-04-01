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
class ExceptionSubscriber implements EventSubscriberInterface
{

    public function __construct(private Security $security, private TranslatorInterface $translator){

    }

    public static function getSubscribedEvents(): array
    {
        return [
            // the priority must be greater than the Security HTTP
            // ExceptionListener, to make sure it's called before
            // the default exception listener
            KernelEvents::EXCEPTION => ['onKernelException', 2],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $user = $this->security->getUser();
        $exception = $event->getThrowable();

        // Récupération de l'erreur 404 
        if ($exception instanceof NotFoundHttpException) {
            $event->setResponse(
                new JsonResponse(['message' => 'Route non trouvée'], Response::HTTP_NOT_FOUND)
            );
            return;
        }
        // Récupération de l'erreur d'accès aux routes via IsGranted, si l'utilisateur n'est pas authentifié, cela signifie que le jeton est absent
        if (($exception instanceof AccessDeniedException)&&($user == null)) {
            $event->setResponse(
                new JsonResponse(['message' => 'Vous devez être authentifié pour accéder à cette route, le jeton est absent'], Response::HTTP_UNAUTHORIZED)
            );
            return;
        }
        // Récupération de l'erreur d'accès aux routes via IsGranted, si l'utilisateur est authentifié, cela signifie que l'utilisateur n'a pas les droits
        if ($exception instanceof AccessDeniedException) {
            $event->setResponse(
                new JsonResponse(['message' => 'Accès interdit, votre habilitation ne vous permet d\'accéder à cette route'], Response::HTTP_FORBIDDEN)
            );
            return;
        }
        // Récupération des autres erreurs générales
        $event->setResponse(
            new JsonResponse(['message' => 'Erreur Serveur', 'data' => $exception->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR)
        );

    }
}