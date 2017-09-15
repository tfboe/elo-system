<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
//<editor-fold desc="Public Methods">
  /**
   * Boot the authentication services for the application.
   *
   * @return void
   */
  public function boot()
  {
    // Here you may define how you wish users to be authenticated for your Lumen
    // application. The callback which receives the incoming request instance
    // should return either a User instance or null. You're free to obtain
    // the User instance via an API token or any other method necessary.

    /** @var \Auth $auth */
    $auth = $this->app['auth'];
    $auth->viaRequest('api', function (/* @var \Request $request */
      $request) {
      if ($request->input('api_token')) {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        return User::where('api_token', $request->input('api_token'))->first();
      }
      return null;
    });
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
//</editor-fold desc="Public Methods">
}
