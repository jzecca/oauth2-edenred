<?php

namespace Jzecca\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class EdenredUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    private $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    public function getId()
    {
        return $this->response['sub'];
    }

    public function getUsername()
    {
        return $this->response['username'];
    }

    public function toArray(): array
    {
        return $this->response;
    }
}
