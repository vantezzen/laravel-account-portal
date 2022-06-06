<?php

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use Vantezzen\LaravelAccountPortal\Exceptions\AccountPortalNotAllowedForUserException;
use Vantezzen\LaravelAccountPortal\Exceptions\NotInAccountPortalException;
use Vantezzen\LaravelAccountPortal\LaravelAccountPortal;

it('can tell that a user is not in a portal', function () {
    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->withArgs(["accountPortal"])->andReturn(false);

    $service = new LaravelAccountPortal();
    $isInPortal = $service->isInPortal($mockSession);
    expect($isInPortal)->toBeFalse();
});

it('can tell that a user is in a portal', function () {
    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->withArgs(["accountPortal"])->andReturn(true);

    $service = new LaravelAccountPortal();
    $isInPortal = $service->isInPortal($mockSession);
    expect($isInPortal)->toBeTrue();
});

it('can use the gate to find out that a user can use a portal', function () {
    $mockUser = Mockery::mock(Authenticatable::class);
    $mockUser->email = "test@allowed.com";
    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->andReturn(false);

    $service = new LaravelAccountPortal();
    $canUsePortal = $service->canUsePortal($mockSession, $mockUser);
    expect($canUsePortal)->toBeTrue();
});

it('can use the gate to find out that a user cannot use a portal', function () {
    $mockUser = Mockery::mock(Authenticatable::class);
    $mockUser->email = "test@denied.com";
    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->andReturn(false);

    $service = new LaravelAccountPortal();
    $canUsePortal = $service->canUsePortal($mockSession, $mockUser);
    expect($canUsePortal)->toBeFalse();
});

it('can indicate that multi-level portal is not possible', function () {
    $mockUser = Mockery::mock(Authenticatable::class);
    $mockUser->email = "test@allowed.com";
    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->andReturn(true);

    $service = new LaravelAccountPortal();
    $canUsePortal = $service->canUsePortal($mockSession, $mockUser);
    expect($canUsePortal)->toBeFalse();
});

/**
 * @return Authenticatable|LegacyMockInterface|MockInterface
 */
function getMockUser(string $mail): LegacyMockInterface|Authenticatable|MockInterface
{
    $mockPortalUser = Mockery::mock(Authenticatable::class);
    $mockPortalUser->email = $mail;
    $mockPortalUser->shouldReceive('getAuthIdentifier')
        ->andReturn("email");

    return $mockPortalUser;
}

it('can open a portal', function () {
    $mockUser = getMockUser("test@allowed.com");
    $mockPortalUser = getMockUser("portal@allowed.com");

    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->andReturn(false);
    $mockSession->shouldReceive('put');

    $service = new LaravelAccountPortal();
    $service->openPortal($mockSession, $mockUser, $mockPortalUser);
    expect(Auth::user()->email)->toEqual("portal@allowed.com");
});

it('throws if trying to open a portal for a user that isnt allowed to', function () {
    $mockUser = getMockUser("test@denied.com");
    $mockPortalUser = getMockUser("portal@denied.com");

    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->andReturn(false);

    $service = new LaravelAccountPortal();

    try {
        $service->openPortal($mockSession, $mockUser, $mockPortalUser);
        expect(false)->toBeTrue();
    } catch (AccountPortalNotAllowedForUserException $e) {
    }
    expect(Auth::user())->toBeNull();
});

it('throws if trying to open a portal while already in a portal', function () {
    $mockUser = getMockUser("test@allowed.com");
    $mockPortalUser = getMockUser("portal@allowed.com");

    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->andReturn(true);

    $service = new LaravelAccountPortal();

    try {
        $service->openPortal($mockSession, $mockUser, $mockPortalUser);
        expect(false)->toBeTrue();
    } catch (AccountPortalNotAllowedForUserException $e) {
    }
    expect(Auth::user())->toBeNull();
});

it('can close a portal', function () {
    $mockUser = getMockUser("test@allowed.com");
    $mockPortalUser = getMockUser("portal@allowed.com");
    $mockPortalUser->shouldReceive("getRememberToken")->andReturn(null);

    Auth::login($mockPortalUser);

    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->andReturn(true);
    $mockSession->shouldReceive('get')->andReturn("100");
    $mockSession->shouldReceive('forget');

    $service = new LaravelAccountPortal();
    $service->closePortal($mockSession, function ($id) use ($mockUser) {
        expect($id)->toEqual("100");

        return $mockUser;
    });
    expect(Auth::user()->email)->toEqual("test@allowed.com");
});

it('throws if trying to close a portal that hasnt been opened', function () {
    $mockSession = Mockery::mock(Store::class);
    $mockSession->shouldReceive('has')->andReturn(false);

    $service = new LaravelAccountPortal();

    try {
        $service->closePortal($mockSession, fn ($id) => null);
        expect(false)->toBeTrue();
    } catch (NotInAccountPortalException $e) {
    }
});
