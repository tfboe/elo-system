<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/11/18
 * Time: 8:58 AM
 */

namespace Tests\Integration;


use Tests\Helpers\ApplicationGetter;

abstract class AuthenticatedTestCase extends \Tfboe\FmLib\TestHelpers\AuthenticatedTestCase
{
  use ApplicationGetter;

//<editor-fold desc="Protected Methods">
  protected function getUserDb()
  {
    return "elo_users";
  }
//</editor-fold desc="Protected Methods">
}