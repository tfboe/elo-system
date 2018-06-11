<?php
declare(strict_types=1);

namespace App\Providers;

use App\Entity\Player;
use App\Entity\Tournament;
use App\Policies\PlayerPolicy;
use App\Policies\TournamentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
//<editor-fold desc="Public Methods">

  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    /** @noinspection PhpUndefinedMethodInspection */
    Gate::policy(Tournament::class, TournamentPolicy::class);

    /** @noinspection PhpUndefinedMethodInspection */
    Gate::policy(Player::class, PlayerPolicy::class);
  }
//</editor-fold desc="Public Methods">
}
