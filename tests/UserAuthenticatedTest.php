<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:04 PM
 */

class UserAuthenticatedTest extends AuthenticatedTestCase
{
//<editor-fold desc="Public Methods">
  public function testGetUserId()
  {
    $this->json_auth('GET', '/getUserId')->seeJsonEquals(['id' => $this->user->getId()]);
  }
//</editor-fold desc="Public Methods">
}