<?php
declare(strict_types=1);

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
//<editor-fold desc="Fields">
  /**
   * The event listener mappings for the application.
   *
   * @var array
   */
  protected $listen = [
    'App\Events\SomeEvent' => [
      'App\Listeners\EventListener',
    ],
  ];
//</editor-fold desc="Fields">
}
