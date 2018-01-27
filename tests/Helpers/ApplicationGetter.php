<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/25/18
 * Time: 3:27 PM
 */

namespace Tests\Helpers;

/**
 * Trait ApplicationGetter
 * @package Tests\Helpers
 */
trait ApplicationGetter
{
//<editor-fold desc="Public Methods">
  /**
   * Creates the application.
   *
   * @return \Laravel\Lumen\Application
   */
  public function createApplication()
  {
    return require __DIR__ . '/../../bootstrap/app.php';
  }
//</editor-fold desc="Public Methods">
}