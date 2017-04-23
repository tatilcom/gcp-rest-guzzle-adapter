<?php

namespace GcpRestGuzzleAdapter\Authentication;

use Firebase\JWT\JWT;

class Credential
{
    /**
     * client_email value in service account file
     * @var string
     */
    protected $email;

    /**
     * private_key value in service account file
     *
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $scope;

    /**
     * Credential constructor.
     * @param string $email
     * @param string $key
     * @param string $scope
     */
    public function __construct($email, $key, $scope)
    {
        $this->email = $email;
        $this->key = $key;
        $this->scope = $scope;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Credential
     */
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return Credential
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return Credential
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return string
     */
    public function createJwtToken()
    {
        $now = time();

        $assertion = [
            "iss" => $this->getEmail(),
            "aud" => "https://www.googleapis.com/oauth2/v4/token",
            "exp" => $now + 3660,
            "iat" => $now - 60,
            "scope" => $this->getScope(),
        ];

        $jwtToken = JWT::encode($assertion, $this->getKey(), 'RS256');

        return $jwtToken;
    }

    /**
     * @return string
     */
    public function createCacheKey()
    {
        $key = md5(sprintf('%s%s%s', $this->getEmail(), $this->getKey(), $this->getScope()));

        return $key;
    }
}