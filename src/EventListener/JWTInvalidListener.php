<?php
// src/App/EventListener/JWTInvalidListener.php
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTInvalidEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;

/**
 * @param JWTInvalidEvent $event
 */
function onJWTInvalid(JWTInvalidEvent $event)
{
    $response = new JWTAuthenticationFailureResponse('Your token is invalid, please login again to get a new one', 403);

    $event->setResponse($response);
}