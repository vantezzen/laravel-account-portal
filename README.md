# Laravel Account Portal

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vantezzen/laravel-account-portal.svg?style=flat-square)](https://packagist.org/packages/vantezzen/laravel-account-portal)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/vantezzen/laravel-account-portal/run-tests?label=tests)](https://github.com/vantezzen/laravel-account-portal/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/vantezzen/laravel-account-portal/Check%20&%20fix%20styling?label=code%20style)](https://github.com/vantezzen/laravel-account-portal/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/vantezzen/laravel-account-portal.svg?style=flat-square)](https://packagist.org/packages/vantezzen/laravel-account-portal)

> 🌌 Quickly switch into user accounts of your Laravel app for debugging, testing etc.

This package allows your admin or support staff to easily log into any user account to view your app with their data.

## Installation

You can install the package via composer:

```bash
composer require vantezzen/laravel-account-portal
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-account-portal-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="laravel-account-portal-views"
```

## Usage

```php
$laravelAccountPortal = new Vantezzen\LaravelAccountPortal();
echo $laravelAccountPortal->echoPhrase('Hello, Vantezzen!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Bennett](https://github.com/vantezzen)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
