<?php

namespace GcpRestGuzzleAdapter\Tests;

use GcpRestGuzzleAdapter\Authentication\Credential;
use GcpRestGuzzleAdapter\Authentication\TokenProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Request;

class BaseTestCase extends \PHPUnit_Framework_TestCase
{
    const TOKEN = [
        'token_type' => 'Bearer',
        'access_token' => 'test_token'
    ];

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getGuzzleClientMock()
    {
        $clientStub = $this
            ->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->getMock();

        return $clientStub;
    }

    /**
     * @return Credential
     */
    protected function getCredentialFixture()
    {
        $sampleKey = openssl_pkey_new(
            [
                'digest_alg' => 'sha256',
                'private_key_bits' => 1024,
                'private_key_type' => OPENSSL_KEYTYPE_RSA
            ]
        );

        return new Credential('test@test.com', $sampleKey, 'test');
    }
}