<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

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
