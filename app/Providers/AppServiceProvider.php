<?php
declare(strict_types=1);

namespace App\Providers;

use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Support\ServiceProvider;

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
    if ($this->app->environment() !== 'production') {
      $this->app->register(IdeHelperServiceProvider::class);
    }
  }
//</editor-fold desc="Public Methods">
}
