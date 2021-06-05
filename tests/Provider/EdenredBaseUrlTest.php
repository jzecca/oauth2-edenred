<?php

namespace Jzecca\OAuth2\Client\Test\Provider;

use Jzecca\OAuth2\Client\Provider\Edenred;
use PHPUnit\Framework\TestCase;

class EdenredBaseUrlTest extends TestCase
{
    public function provideEnvironment(): array
    {
        return [
            [new Edenred(), 'sso.eu.edenred.io'],
            [new Edenred(['sandbox' => true]), 'sso.sbx.edenred.io'],
        ];
    }

    /**
     * @dataProvider provideEnvironment
     */
    public function testBaseAuthorizationUrl(Edenred $provider, string $host): void
    {
        $url = $provider->getAuthorizationUrl();
        $uri = parse_url($url);

        $this->assertEquals('https', $uri['scheme']);
        $this->assertEquals($host, $uri['host']);
        $this->assertEquals('/connect/authorize', $uri['path']);
    }

    /**
     * @dataProvider provideEnvironment
     */
    public function testBaseAccessTokenUrl(Edenred $provider, string $host): void
    {
        $url = $provider->getBaseAccessTokenUrl([]);
        $uri = parse_url($url);

        $this->assertEquals('https', $uri['scheme']);
        $this->assertEquals($host, $uri['host']);
        $this->assertEquals('/connect/token', $uri['path']);
    }

    /**
     * @dataProvider provideEnvironment
     */
    public function testResourceOwnerDetailsUrl(Edenred $provider, string $host): void
    {
        $url = $provider->getResourceOwnerDetailsUrl(EdenredTest::mockAccessToken());
        $uri = parse_url($url);

        $this->assertEquals('https', $uri['scheme']);
        $this->assertEquals($host, $uri['host']);
        $this->assertEquals('/connect/userinfo', $uri['path']);
    }
}
