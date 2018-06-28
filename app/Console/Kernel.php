<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/28/18
 * Time: 1:13 PM
 */

namespace App\Console;

use App\Console\Commands\RecomputeRankingsCommand;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

/**
 * Class Kernel
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{
//<editor-fold desc="Fields">
  /**
   * The Artisan commands provided by your application.
   *
   * @var array
   */
  protected $commands = [
    RecomputeRankingsCommand::class
  ];
//</editor-fold desc="Fields">
}