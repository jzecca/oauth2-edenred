<?php

namespace Jzecca\OAuth2\Client\Test\Provider;

use Eloquent\Phony\Phpunit\Phony;
use GuzzleHttp\Psr7\Utils;
use Jzecca\OAuth2\Client\Provider\Edenred;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\QueryBuilderTrait;
use PHPUnit\Framework\TestCase;

class EdenredTest extends TestCase
{
    use QueryBuilderTrait;

    /** @var Edenred */
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new Edenred([
            'clientId' => 'mock_client_id',
            'clientSecret' => 'mock_secret',
            'redirectUri' => 'none',
            'locale' => 'mock_locale',
            'loginHint' => 'mock_login_hint',
            'scopes' => ['mock_scope'],
            'tenant' => 'mock_tenant',
        ]);
    }

    public function testAuthorizationUrl(): void
    {
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);

        $this->assertArrayHasKey('acr_values', $query);
        $this->assertArrayHasKey('client_id', $query);
        $this->assertArrayHasKey('login_hint', $query);
        $this->assertArrayHasKey('redirect_uri', $query);
        $this->assertArrayHasKey('response_type', $query);
        $this->assertArrayHasKey('scope', $query);
        $this->assertArrayHasKey('state', $query);
        $this->assertArrayHasKey('ui_locales', $query);
        $this->assertArrayNotHasKey('approval_prompt', $query);

        $this->assertEquals('tenant:mock_tenant', $query['acr_values']);
        $this->assertEquals('mock_locale', $query['ui_locales']);
        $this->assertEquals('mock_login_hint', $query['login_hint']);

        $this->assertStringContainsString('mock_scope', $query['scope']);
        $this->assertStringContainsString('offline_access', $query['scope']);
        $this->assertStringContainsString('openid', $query['scope']);

        $this->assertNotEmpty($this->provider->getState());
    }

    public function testUserData(): void
    {
        $userJson = '{"sub":"mock_tenant\\\\mock.name@example.com","tenant":"mock_tenant","username":"mock.name@example.com"}';

        $response = Phony::mock('GuzzleHttp\Psr7\Response');
        $response->getHeader->returns(['application/json']);
        $response->getBody->returns(Utils::streamFor($userJson));

        $provider = Phony::partialMock(Edenred::class);
        $provider->getResponse->returns($response);

        $edenred = $provider->get();
        $token = $this->mockAccessToken();

        $user = $edenred->getResourceOwner($token);

        Phony::inOrder(
            $provider->fetchResourceOwnerDetails->called(),
        );

        $this->assertInstanceOf(ResourceOwnerInterface::class, $user);

        $this->assertEquals('mock_tenant\\mock.name@example.com', $user->getId());
        $this->assertEquals('mock.name@example.com', $user->getUsername());

        $user = $user->toArray();

        $this->assertArrayHasKey('sub', $user);
        $this->assertArrayHasKey('tenant', $user);
        $this->assertArrayHasKey('username', $user);
    }

    public function testUserError(): void
    {
        $errorJson = '{"error": {"code": 400, "message": "I am an error"}}';

        $response = Phony::mock('GuzzleHttp\Psr7\Response');
        $response->getHeader->returns(['application/json']);
        $response->getBody->returns(Utils::streamFor($errorJson));

        $provider = Phony::partialMock(Edenred::class);
        $provider->getResponse->returns($response);

        $edenred = $provider->get();
        $token = $this->mockAccessToken();

        $this->expectException(IdentityProviderException::class);

        $user = $edenred->getResourceOwner($token);

        Phony::inOrder(
            $provider->getResponse->calledWith($this->instanceOf('GuzzleHttp\Psr7\Request')),
            $response->getHeader->called(),
            $response->getBody->called()
        );
    }

    public static function mockAccessToken(): AccessToken
    {
        return new AccessToken([
            'access_token' => 'mock_access_token',
        ]);
    }
}
