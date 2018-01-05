<?php
declare(strict_types=1);

namespace App\Providers;

use App\Service\DynamicServiceLoadingService;
use App\Service\DynamicServiceLoadingServiceInterface;
use App\Service\RankingSystem\EloRanking;
use App\Service\RankingSystem\EloRankingInterface;
use App\Service\RankingSystemService;
use App\Service\RankingSystemServiceInterface;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Container\Container;
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

    $this->app->singleton(DynamicServiceLoadingServiceInterface::class, function () {
      return new DynamicServiceLoadingService();
    });

    $this->app->singleton(RankingSystemServiceInterface::class, function (Container $app) {
      return new RankingSystemService($app->make(DynamicServiceLoadingServiceInterface::class));
    });

    $this->app->singleton(EloRankingInterface::class, function () {
      return new EloRanking();
    });
  }
//</editor-fold desc="Public Methods">
}
