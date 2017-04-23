<?php

namespace GcpRestGuzzleAdapter\Client;

use GcpRestGuzzleAdapter\Authentication\Credential;
use GcpRestGuzzleAdapter\Authentication\TokenProvider;
use GcpRestGuzzleAdapter\Authentication\TokenSubscriber;
use GcpRestGuzzleAdapter\Cache\ApcuCache;
use GuzzleHttp\Client;
use GuzzleHttp\Event\Emitter;

class ClientFactory
{
    /**
     * @param string $email Service acount email
     * @param string $key Private Key
     * @param string $scope Project Scope
     * @param string $projectBaseUrl Full base url of Google Rest API
     * @param string $cacheClass Cache class
     * @return Client
     */
    public static function createClient($email, $key, $scope, $projectBaseUrl, $cacheClass = ApcuCache::class)
    {
        $credential = new Credential($email, $key, $scope);

        $tokenProvider = new TokenProvider(new Client(), new $cacheClass);

        $tokenSubscriber = new TokenSubscriber($credential, $tokenProvider);

        $client = new Client(
            [
                'base_url' => $projectBaseUrl,
                'emitter' => new Emitter(),
            ]
        );

        $client->getEmitter()->attach($tokenSubscriber);

        return $client;
    }
}