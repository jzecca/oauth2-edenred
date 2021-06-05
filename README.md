# Edenred Provider for OAuth 2.0 Client

[![Build Status](https://img.shields.io/github/workflow/status/jzecca/oauth2-edenred/CI?logo=github&style=flat-square)](https://github.com/jzecca/oauth2-edenred/actions/workflows/ci.yaml)
[![Coverage](https://img.shields.io/codecov/c/gh/jzecca/oauth2-edenred?logo=codecov&style=flat-square)](https://codecov.io/gh/jzecca/oauth2-edenred)

This package provides [Edenred Connect][edenred-connect] OAuth 2.0 support
for the PHP League's [OAuth 2.0 Client][oauth2-client].

[edenred-connect]: https://anypoint.mulesoft.com/exchange/portals/edenred-corporate/9a5c9842-3e68-452a-aa88-d100c3bdefba/edenred-connect
[oauth2-client]: https://github.com/thephpleague/oauth2-client

## Installation

To install, use composer:

```sh
composer require jzecca/oauth2-edenred
```

## Usage

Usage is the same as The League's OAuth client, using `\Jzecca\OAuth2\Client\Provider\Edenred` as the provider.

#### Available Options

The `Edenred` provider has the following [options][edenred-options]:

| Name        | Type     | Default | Description                                                     |
|-------------|:--------:|:-------:|-----------------------------------------------------------------|
| `locale`    | `string` |         | Gives a hint about the desired display language of the login UI |
| `loginHint` | `string` |         | Pre-fills the username field on the login page                  |
| `sandbox`   | `bool`   | `false` | Uses the sandbox API if set to `true`                           |
| `scopes`    | `array`  | `[]`    | Adds one or more registered scopes                              |
| `tenant`    | `string` |         | Passes a tenant name to the user service                        |

[edenred-options]: https://anypoint.mulesoft.com/exchange/portals/edenred-corporate/9a5c9842-3e68-452a-aa88-d100c3bdefba/edenred-connect/minor/1.0/console/method/%23263

## Testing

Tests can be run with:

```sh
composer test
```

Code style can be fixed with:

```sh
composer fix
```
