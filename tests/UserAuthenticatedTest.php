<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 PM
 */

class UserAuthenticatedTest extends AuthenticatedTestCase
{
//<editor-fold desc="Public Methods">
  public function testUserId()
  {
    $this->jsonAuth('GET', '/userId')->seeJsonEquals(['id' => $this->user->getId()]);
  }
//</editor-fold desc="Public Methods">
}