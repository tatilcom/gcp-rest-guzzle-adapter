<?php

namespace GcpRestGuzzleAdapter\Tests\Authentication;


use GcpRestGuzzleAdapter\Authentication\Credential;
use GcpRestGuzzleAdapter\Authentication\TokenProvider;
use GcpRestGuzzleAdapter\Authentication\TokenSubscriber;
use GcpRestGuzzleAdapter\Cache\CacheInterface;
use GcpRestGuzzleAdapter\Tests\BaseTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Event\BeforeEvent;
use GuzzleHttp\Event\ErrorEvent;
use GuzzleHttp\Message\Request;
use GuzzleHttp\Message\Response;

class TokenSubscriberTest extends BaseTestCase
{
    public function testOnBefore()
    {
        $credential = $this->getCredentialFixture();

        $tokenProviderMock = $this->getTokenProviderMock($credential);

        $requestMock = $this->getRequestMock();

        $eventMock = $this->getEventMock(BeforeEvent::class);
        $eventMock
            ->method('getRequest')
            ->willReturn($requestMock);

        $tokenSubscriber = new TokenSubscriber($credential, $tokenProviderMock);
        $tokenSubscriber->onBefore($eventMock);
    }

    public function testOnError()
    {
        $credential = $this->getCredentialFixture();

        $tokenProviderMock = $this->getTokenProviderMock($credential);

        $eventMock = $this->getEventMock(ErrorEvent::class);

        $requestMock = $this->getRequestMock();

        $responseMock = $this
            ->getMockBuilder(Response::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock
            ->method('getStatusCode')
            ->willReturn(401);

        $clientMock = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        $clientMock
            ->expects($this->exactly(1))
            ->method('send')
            ->with($requestMock)
            ->willReturn($responseMock);

        $eventMock
            ->method('getRequest')
            ->willReturn($requestMock);

        $eventMock
            ->method('getResponse')
            ->willReturn($responseMock);

        $eventMock
            ->method('getClient')
            ->willReturn($clientMock);

        $eventMock
            ->expects($this->exactly(1))
            ->method('intercept')
            ->with($responseMock);

        $tokenSubscriber = new TokenSubscriber($credential, $tokenProviderMock);
        $tokenSubscriber->onError($eventMock);
    }

    /**
     * @param $credential
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getTokenProviderMock($credential)
    {
        $tokenProviderMock = $this
            ->getMockBuilder(TokenProvider::class)
            ->disableOriginalClone()
            ->disableOriginalConstructor()
            ->getMock();

        $tokenProviderMock
            ->expects($this->exactly(1))
            ->method('getToken')
            ->with($credential)
            ->willReturn(self::TOKEN);

        return $tokenProviderMock;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getRequestMock()
    {
        $requestMock = $this
            ->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock
            ->expects($this->exactly(1))
            ->method('setHeader')
            ->with(
                $this->equalTo('Authorization'),
                $this->equalTo(implode(' ', array_values(self::TOKEN)))
            );
        return $requestMock;
    }

    /**
     * @param $class
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getEventMock($class)
    {
        $eventMock = $this
            ->getMockBuilder($class)
            ->disableOriginalConstructor()
            ->getMock();

        return $eventMock;
    }
}