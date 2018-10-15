<?php
declare(strict_types=1);

namespace App\Providers;

use App\Exceptions\Handler;
use App\Service\AsyncRunner;
use App\Service\AsyncRunnerInterface;
use App\Service\AsyncServices\CreateOrReplaceTournament;
use App\Service\AsyncServices\CreateOrReplaceTournamentInterface;
use App\Service\AsyncServices\RecalculateRankingSystems;
use App\Service\AsyncServices\RecalculateRankingSystemsInterface;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\ServiceProvider;
use LaravelDoctrine\Extensions\BeberleiExtensionsServiceProvider;
use LaravelDoctrine\Migrations\MigrationsServiceProvider;
use Tfboe\FmLib\Providers\FmLibServiceProvider;
use Tfboe\FmLib\Service\LoadingServiceInterface;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;

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

    $this->app->singleton(CreateOrReplaceTournamentInterface::class, function (Container $app) {
      return new CreateOrReplaceTournament(
        $app->make(EntityManagerInterface::class),
        $app->make(LoadingServiceInterface::class)
      );
    });

    $this->app->singleton(AsyncRunnerInterface::class, function (Container $app) {
      return new AsyncRunner($app);
    });

    $this->app->singleton(RecalculateRankingSystemsInterface::class, function (Container $app) {
      return new RecalculateRankingSystems($app->make(RankingSystemServiceInterface::class),
        $app->make(EntityManagerInterface::class));
    });
  }
//</editor-fold desc="Public Methods">
}
