<?php

namespace GcpRestGuzzleAdapter\Tests\Authentication;

use GcpRestGuzzleAdapter\Authentication\Credential;
use GcpRestGuzzleAdapter\Authentication\TokenProvider;
use GcpRestGuzzleAdapter\Cache\CacheInterface;
use GcpRestGuzzleAdapter\Tests\BaseTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;

class TokenProviderTest extends BaseTestCase
{
    public function testGetTokenFromCache()
    {
        $clientMock = $this->getGuzzleClientMock();
        $credential = $this->getCredentialFixture();

        $cacheMock = $this->getCacheMock();

        $cacheMock
            ->expects($this->exactly(1))
            ->method('get')
            ->with($credential->createCacheKey())
            ->willReturn(self::TOKEN);

        $cacheMock
            ->expects($this->exactly(0))
            ->method('set');

        $clientMock
            ->expects($this->exactly(0))
            ->method('post');

        $tokenProvider = new TokenProvider($clientMock, $cacheMock);
        $token = $tokenProvider->getToken($credential);

        $this->assertEquals($token, self::TOKEN);
    }

    public function testGetTokenFromRest()
    {
        $clientMock = $this->getGuzzleClientMock();
        $credential = $this->getCredentialFixture();

        $cacheMock = $this->getCacheMock();

        $cacheMock
            ->expects($this->exactly(1))
            ->method('get')
            ->with($credential->createCacheKey())
            ->willReturn(false);

        $responseMock = $this
            ->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock
            ->expects($this->exactly(1))
            ->method('json')
            ->willReturn(self::TOKEN);

        $clientMock
            ->expects($this->exactly(1))
            ->method('post')
            ->with(
                TokenProvider::TOKEN_URL,
                [
                    "body" => [
                        'assertion' => $credential->createJwtToken(),
                        'grant_type' => TokenProvider::GRANT_TYPE,
                    ],
                ]
            )
            ->willReturn(
                $responseMock
            );

        $cacheMock
            ->expects($this->exactly(1))
            ->method('set')
            ->with(
                $credential->createCacheKey(),
                self::TOKEN
            );

        $tokenProvider = new TokenProvider($clientMock, $cacheMock);
        $token = $tokenProvider->getToken($credential);

        $this->assertEquals($token, self::TOKEN);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCacheMock()
    {
        $cacheMock = $this
            ->getMockBuilder(CacheInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        return $cacheMock;
    }


}