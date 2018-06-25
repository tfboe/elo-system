<?php
declare(strict_types=1);

namespace App\Providers;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use LaravelDoctrine\Extensions\BeberleiExtensionsServiceProvider;
use LaravelDoctrine\Migrations\MigrationsServiceProvider;
use Tfboe\FmLib\Providers\FmLibServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
//<editor-fold desc="Public Methods">
  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    $this->app->register(BeberleiExtensionsServiceProvider::class);
    $this->app->register(FmLibServiceProvider::class);

    $this->app->singleton(
      ExceptionHandler::class,
      Handler::class
    );

    $this->app->register(CatchAllOptionsRequestsProvider::class);

    //optional service providers
    if (class_exists('\LaravelDoctrine\Migrations\MigrationsServiceProvider')) {
      $this->app->register(MigrationsServiceProvider::class);
    }
  }
//</editor-fold desc="Public Methods">
}
