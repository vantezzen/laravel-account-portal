# Laravel Account Portal

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vantezzen/laravel-account-portal.svg?style=flat-square)](https://packagist.org/packages/vantezzen/laravel-account-portal)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/vantezzen/laravel-account-portal/run-tests?label=tests)](https://github.com/vantezzen/laravel-account-portal/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/vantezzen/laravel-account-portal/Check%20&%20fix%20styling?label=code%20style)](https://github.com/vantezzen/laravel-account-portal/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/vantezzen/laravel-account-portal.svg?style=flat-square)](https://packagist.org/packages/vantezzen/laravel-account-portal)

> 🌌 Quickly switch into user accounts of your Laravel app for debugging, testing etc.

This package allows your admin or support staff to easily log into any user account to view your app with their data.

Under the hood, this package simply logs you into the portal user account, saving the original account info in the
session to allow you to switch back.

## About

When building systems I noticed that we build the same functionality into almost all of our products:

We create a simple page, a button or API where support staff or developers can enter an email of a user or search though
a list of
users and log in to our app using that user's account. In some other apps I've also seen similar functionality or even
worse just a "master password" that can be input as the password to log into every account without further verification.

This functionality is useful when users complain about something not working or have questions about data they created
in their account so that we can view the app exactly the way they would.

In different projects this functionality might have different names - account portal, guest view, user view, support
view, direct access, support login etc. - but they mostly work the same.

This package tries to be as unopinionated as possible: It doesn't define a fixed API, pre-build dashboard or makes
assumptions about your user models. Instead, this package tries to provide an abstracted API for working with
authenticatable classes and user sessions. Everything else - an API, interface or buttons in your frontend - can be
added by you based on your apps needs.

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
Gate::define("use-account-portal", function (User $currentUser, ?User $portalUser = null) {
    return $currentUser->isAdmin();
});
```

Your gate should determine, if the user `$currentUser` is authorized to open a portal into the account of `$portalUser`
though please note that `$portalUser` might be `null` if checking that the current user is allowed to use the feature at
all.

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

### Session Storage

This package uses the current session to store information about the portal but you might choose to implement your own
way of storing this information.

For this, the package uses the `PortalStorage` interface that defines the functions needed for the package.

By default, you'll simply wrap the Laravel session into a `SessionPortalStorage`:

```PHP
use \Vantezzen\LaravelAccountPortal\PortalStorage\SessionPortalStorage;

class MyController extends Controller {
    public function index(Request $request) {
        $storage = new SessionPortalStorage($request->session());
    }
}
```

### Open portal

To open a portal into an account, simply call the `openPortal` method:

```PHP
class MyController extends Controller {
    // Example for a route like "/account-portal/open/{portalUserId}"
    public function openPortal(Request $request, string $portalUserId, LaravelAccountPortal $laravelAccountPortal) {
        $storage = new SessionPortalStorage($request->session());
        
        $laravelAccountPortal->openPortal(
            // Current session object used to store the portal information
            $storage,
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
        $storage = new SessionPortalStorage($request->session());
        
        $laravelAccountPortal->closePortal(
            // Current session object used to store the portal information
            $storage,
            
            // Get the authenticatable model instance by id
            fn($id) => User::find($id)
        );
    }
}
```

The second argument should be a `callable` that returns the model of a user by its ID. Internally, this package only
stores the ID of the original user that opened the portal to get back to the account once the portal is closed.
To stay unopinionated about your user modelling, you need to provide this callback to lookup the model by this saved ID.

Please note that this will throw a "NotInAccountPortalException" if the session doesn't currently have an active portal.

### Portal status

To check the current status of the portal, you can use the helper methods `isInPortal` and `canUsePortal`:

```php
$storage = new SessionPortalStorage($request->session());
      
// True if the session has information about being in a portal
$isInPortal = $laravelAccountPortal->isInPortal($storage);

// True if a portal can be opened into the portal user
// Please note that "$portalUser" might be left as null to check generally
$canUsePortal = $laravelAccountPortal->canUsePortal($storage, $portalUser);
```

Based on these parameters, you might choose to display an "Open account portal", "Close account portal" button or no
button in your frontend.

### Multi-level portal

Please note that due to security and complexity reasons, users cannot open a portal while already in a portal.

If a user is currently in a portal, `canUsePortal` will always return `false`, making it impossible to open further
portals.

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
