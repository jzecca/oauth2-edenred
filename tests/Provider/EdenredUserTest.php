<?php

namespace Jzecca\OAuth2\Client\Test\Provider;

use Jzecca\OAuth2\Client\Provider\EdenredUser;
use PHPUnit\Framework\TestCase;

class EdenredUserTest extends TestCase
{
    public function testUserDefaults(): void
    {
        $user = new EdenredUser([
            'sub' => 'mock_tenant\\mock.name@example.com',
            'tenant' => 'mock_tenant',
            'username' => 'mock.name@example.com',
        ]);

        self::assertEquals('mock_tenant\\mock.name@example.com', $user->getId());
        self::assertEquals('mock.name@example.com', $user->getUsername());
    }
}
