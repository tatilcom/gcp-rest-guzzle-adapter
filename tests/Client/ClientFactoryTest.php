<?php

namespace GcpRestGuzzleAdapter\Tests\Client;

use GcpRestGuzzleAdapter\Authentication\TokenSubscriber;
use GcpRestGuzzleAdapter\Client\ClientFactory;
use GuzzleHttp\Client;

class ClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClient()
    {
        $client = ClientFactory::createClient(
            'test@test.com',
            'test_key',
            'test_scope',
            'http://www.google.com'
        );

        $this->assertInstanceOf(Client::class, $client);

        $emitter = $client->getEmitter();

        $this->assertNotNull($emitter);
        $this->assertTrue($emitter->hasListeners('error'));
        $this->assertInstanceOf(TokenSubscriber::class, $emitter->listeners('error')[0][0]);
        $this->assertTrue($emitter->hasListeners('before'));
        $this->assertInstanceOf(TokenSubscriber::class, $emitter->listeners('before')[0][0]);
    }
}