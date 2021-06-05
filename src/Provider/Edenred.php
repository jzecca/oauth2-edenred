<?php

namespace Jzecca\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class Edenred extends AbstractProvider
{
    use BearerAuthorizationTrait;

    /**
     * @var string gives a hint about the desired display language of the login UI
     */
    protected $locale;

    /**
     * @var string can be used to pre-fill the username field on the login page
     */
    protected $loginHint;

    /**
     * @var bool if true, uses sandbox instead of production endpoints
     */
    protected $sandbox;

    /**
     * @var array list of scopes that will be used for authentication
     */
    protected $scopes = [];

    /**
     * @var string can be used to pass a tenant name to the user service
     */
    protected $tenant;

    public function getBaseAuthorizationUrl(): string
    {
        return $this->getBaseUrl().'/connect/authorize';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return $this->getBaseUrl().'/connect/token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return $this->getBaseUrl().'/connect/userinfo';
    }

    protected function getDefaultScopes(): array
    {
        return [
            'openid',
            'offline_access',
        ];
    }

    protected function getScopeSeparator(): string
    {
        return ' ';
    }

    protected function getAuthorizationParameters(array $options): array
    {
        if (empty($options['acr_values']) && $this->tenant) {
            $options['acr_values'] = sprintf('tenant:%s', $this->tenant);
        }

        if (empty($options['login_hint']) && $this->loginHint) {
            $options['login_hint'] = $this->loginHint;
        }

        if (empty($options['ui_locales']) && $this->locale) {
            $options['ui_locales'] = $this->locale;
        }

        // Default scopes MUST be included.
        // Additional scopes MAY be added by constructor or option.
        $scopes = array_merge($this->getDefaultScopes(), $this->scopes);

        if (!empty($options['scope'])) {
            $scopes = array_merge($scopes, $options['scope']);
        }

        $options['scope'] = array_unique($scopes);

        $options = parent::getAuthorizationParameters($options);

        unset($options['approval_prompt']);

        return $options;
    }

    protected function checkResponse(ResponseInterface $response, $data): void
    {
        if (empty($data['error'])) {
            return;
        }

        $code = 0;
        $error = $data['error'];

        if (is_array($error)) {
            $code = $error['code'];
            $error = $error['message'];
        }

        throw new IdentityProviderException($error, $code, $data);
    }

    protected function createResourceOwner(array $response, AccessToken $token): EdenredUser
    {
        return new EdenredUser($response);
    }

    private function getBaseUrl(): string
    {
        return $this->sandbox
            ? 'https://sso.sbx.edenred.io'
            : 'https://sso.eu.edenred.io';
    }
}
