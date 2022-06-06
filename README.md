# Laravel Account Portal

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vantezzen/laravel-account-portal.svg?style=flat-square)](https://packagist.org/packages/vantezzen/laravel-account-portal)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/vantezzen/laravel-account-portal/run-tests?label=tests)](https://github.com/vantezzen/laravel-account-portal/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/vantezzen/laravel-account-portal/Check%20&%20fix%20styling?label=code%20style)](https://github.com/vantezzen/laravel-account-portal/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/vantezzen/laravel-account-portal.svg?style=flat-square)](https://packagist.org/packages/vantezzen/laravel-account-portal)

> 🌌 Quickly switch into user accounts of your Laravel app for debugging, testing etc.

This package allows your admin or support staff to easily log into any user account to view your app with their data.

Under the hood, this package simply logs you into the portal user account, saving the original account info in the
session to allow you to switch back.

## Installation

You can install the package via composer:

```bash
composer require vantezzen/laravel-account-portal
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-account-portal-config"
```

## Defining Gate

This package requires you to define the Gate "use-account-portal" in order to protect the portal functionality from
unauthorized use.

For example, you might add such a Gate definition to your AppServiceProvider:

```PHP
Gate::define("use-account-portal", function (User $currentUser, User $portalUser) {
    return $currentUser->isAdmin();
});
```

Your gate should determine, if the user `$currentUser` is authorized to open a portal into the account of `$portalUser`.

## Usage

This package only provides a class to easily open and close account portals - you probably need to create an API
controller or similar to suit the needs of your app.

### Get instance

To control the portal, you will need an instance of the "Vantezzen\LaravelAccountPortal\LaravelAccountPortal" class. You
can use Laravel's dependency injection or create the class manually:

```PHP
// Use Laravel's dependency injection
class MyController extends Controller {
    public function index(Vantezzen\LaravelAccountPortal\LaravelAccountPortal $laravelAccountPortal) {
        // ...
    }
}

// ...or...

// Create manually
$laravelAccountPortal = new Vantezzen\LaravelAccountPortal\LaravelAccountPortal();
```

### Open portal

To open a portal into an account, simply call the `openPortal` method:

```PHP
class MyController extends Controller {
    // Example for a route like "/account-portal/open/{portalUserId}"
    public function openPortal(Request $request, string $portalUserId, LaravelAccountPortal $laravelAccountPortal) {
        $laravelAccountPortal->openPortal(
            // Current session object used to store the portal information
            $request->session(),
            // Current user that wants to open the portal
            $request->user(),
            // User the current user wants to portal into
            User::find($portalUserId)
        );
    }
}
```

Please note that this will internally automatically check your defined gate and throw a
"AccountPortalNotAllowedForUserException" if your gate denies the request.

### Close portal

To close the portal, simply call `closePortal` with the current session

```PHP
class MyController extends Controller {
    public function closePortal(Request $request, LaravelAccountPortal $laravelAccountPortal) {
        $laravelAccountPortal->closePortal(
            // Current session object used to store the portal information
            $request->session(),
        );
    }
}
```

Please note that this will throw a "NotInAccountPortalException" if the session doesn't currently have an active portal.

### Portal status

To check the current status of the portal, you can use the helper methods `isInPortal` and `canUsePortal`:

```php
// True if the session has information about being in a portal
$isInPortal = $laravelAccountPortal->isInPortal($request->session());

// Simply a wrapper around your defined Gate
$canUsePortal = $laravelAccountPortal->canUsePortal($portalUser);
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

- [Bennett](https://github.com/vantezzen)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
