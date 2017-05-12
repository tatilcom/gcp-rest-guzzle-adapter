<?php

namespace GcpRestGuzzleAdapter\Authentication;

use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Event\SubscriberInterface;
use GuzzleHttp\Message\RequestInterface;

class TokenSubscriber implements SubscriberInterface
{
    /**
     * @var Credential
     */
    protected $credential;

    /**
     * @var TokenProvider
     */
    protected $tokenProvider;

    /**
     * TokenSubscriber constructor.
     * @param Credential $credential
     * @param TokenProvider $tokenProvider
     */
    public function __construct(Credential $credential, TokenProvider $tokenProvider)
    {
        $this->credential = $credential;
        $this->tokenProvider = $tokenProvider;
    }

    /**
     * @inheritDoc
     */
    public function getEvents()
    {
        return [
            'before' => ['onBefore'],
            'error' => ['onError'],
        ];
    }

    /**
     * @param BeforeEvent $event
     */
    public function onBefore(BeforeEvent $event)
    {
        $token = $this->tokenProvider->getToken($this->credential);
        $this->setAuthorizationHeader($event->getRequest(), $token);
    }

    /**
     * @param ErrorEvent $event
     */
    public function onError(ErrorEvent $event)
    {
        if ($event->getResponse() != null && $event->getResponse()->getStatusCode() == 401) {
            $token = $this->tokenProvider->getToken($this->credential, false);

            $request = $this->setAuthorizationHeader($event->getRequest(), $token);

            $newResponse = $event->getClient()->send($request);

            $event->intercept($newResponse);
        }
    }

    /**
     * @param RequestInterface $request
     * @param array $token
     * @return RequestInterface
     */
    protected function setAuthorizationHeader(RequestInterface $request, array $token)
    {
        $request->setHeader(
            'Authorization',
            sprintf('%s %s', $token['token_type'], $token['access_token'])
        );

        return $request;
    }
}