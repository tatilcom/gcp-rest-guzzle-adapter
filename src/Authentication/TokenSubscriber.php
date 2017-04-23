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
        $this->setAuthorizationHeader($event->getRequest());
    }

    /**
     * @param ErrorEvent $event
     */
    public function onError(ErrorEvent $event)
    {
        if ($event->getResponse() != null && $event->getResponse()->getStatusCode() == 401) {
            $request = $this->setAuthorizationHeader($event->getRequest());

            $newResponse = $event->getClient()->send($request);

            $event->intercept($newResponse);
        }
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     */
    protected function setAuthorizationHeader(RequestInterface $request)
    {
        $token = $this->tokenProvider->getToken($this->credential);

        $request->setHeader(
            'Authorization',
            sprintf('%s %s', $token['token_type'], $token['access_token'])
        );

        return $request;
    }
}