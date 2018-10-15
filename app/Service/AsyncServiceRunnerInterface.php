<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/14/18
 * Time: 11:44 AM
 */

namespace App\Service;

use App\Jobs\RunAsyncRequestJob;

interface AsyncServiceRunnerInterface
{

//<editor-fold desc="Public Methods">
  /**
   * @param mixed $input
   * @param $reportProgress
   * @return mixed[] dictionary with keys data and status
   */
  function run($input, $reportProgress): array;

  /**
   * @param string $id
   * @return RunAsyncRequestJob
   */
  function getJob(string $id): RunAsyncRequestJob;
//</editor-fold desc="Public Methods">
}