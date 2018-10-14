<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/14/18
 * Time: 1:31 PM
 */

namespace App\Service;


interface AsyncRunnerInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @param $serviceName
   * @param $input
   * @param $reportProgress
   * @return array
   */
  public function runAsync($serviceName, $input, $reportProgress = null): array;
//</editor-fold desc="Public Methods">
}