<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/15/18
 * Time: 5:55 PM
 */

namespace App\Jobs;


class RecalculateRankingSystemsJob extends RunAsyncRequestJob
{
//<editor-fold desc="Fields">
  /**
   * The number of times the job may be attempted.
   *
   * @var int
   */
  public $tries = 0;
//</editor-fold desc="Fields">
}