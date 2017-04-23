<?php

namespace GcpRestGuzzleAdapter\Authentication;

use GcpRestGuzzleAdapter\Cache\CacheInterface;
use GuzzleHttp\Client;

class TokenProvider
{
    /**
     * Token URL of Google Class Authentication endpoint
     */
    const TOKEN_URL = 'https://www.googleapis.com/oauth2/v4/token';

    /**
     * OAUTH 2 grant type
     */
    const GRANT_TYPE = 'urn:ietf:params:oauth:grant-type:jwt-bearer';

    /**
     * @var Client
     */
    protected $tokenClient;

    /**
     * @var CacheInterface
     */
    protected $cacheClient;

    /**
     * TokenProvider constructor.
     * @param Client $tokenClient
     * @param CacheInterface $cacheClient
     */
    public function __construct(Client $tokenClient, CacheInterface $cacheClient)
    {
        $tokenClient->setDefaultOption(
            'headers',
            [
                "Content-Type" => "application/x-www-form-urlencoded"
            ]
        );

        $this->tokenClient = $tokenClient;
        $this->cacheClient = $cacheClient;
    }

    /**
     * @param Credential $credential
     * @return array|mixed
     */
    public function getToken(Credential $credential)
    {
        $key = $credential->createCacheKey();

        $token = $this->cacheClient->get($key);

        if (!$token) {
            $token = $this->tokenClient->post(
                self::TOKEN_URL,
                [
                    "body" => [
                        'assertion' => $credential->createJwtToken(),
                        'grant_type' => self::GRANT_TYPE,
                    ],
                ]
            )->json();

            $this->cacheClient->set($key, $token);
        }

        return $token;
    }
}