<?php

namespace Jzecca\OAuth2\Client\Test\Provider;

use Jzecca\OAuth2\Client\Provider\Edenred;
use PHPUnit\Framework\TestCase;

class EdenredScopeTest extends TestCase
{
    public function testDefaultScopes(): void
    {
        $provider = new Edenred([]);

        $params = $this->getQueryParams($provider->getAuthorizationUrl());

        self::assertSame('openid offline_access', $params['scope']);
    }

    public function testProviderScopes(): void
    {
        $provider = new Edenred([
            'scopes' => [
                'mock_scope',
            ],
        ]);

        $params = $this->getQueryParams($provider->getAuthorizationUrl());

        self::assertStringContainsString('mock_scope', $params['scope']);
    }

    public function testOptionScopes(): void
    {
        $provider = new Edenred([]);

        $params = $this->getQueryParams($provider->getAuthorizationUrl([
            'scope' => [
                'mock_scope',
            ],
        ]));

        self::assertStringContainsString('mock_scope', $params['scope']);
    }

    private function getQueryParams(string $url): array
    {
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        return $query;
    }
}
