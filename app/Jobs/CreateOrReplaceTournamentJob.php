<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/15/18
 * Time: 5:55 PM
 */

namespace App\Jobs;


class CreateOrReplaceTournamentJob extends RunAsyncRequestJob
{
//<editor-fold desc="Fields">
  /**
   * The number of times the job may be attempted.
   *
   * @var int
   */
  public $tries = 1;
//</editor-fold desc="Fields">
}