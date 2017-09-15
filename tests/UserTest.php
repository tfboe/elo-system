<?php

class UserTest extends TestCase
{
//<editor-fold desc="Public Methods">
  /**
   * A basic test example.
   *
   * @return void
   */
  public function testExample()
  {
    $this->get('/');

    $this->assertEquals(
      $this->app->version(), $this->response->getContent()
    );
  }

  public function testAuthenticationError()
  {
    $this->json('GET', '/getUserId')->seeJsonEquals(
      ["status" => 401, "message" => "Not logged in!"]);
  }
//</editor-fold desc="Public Methods">
}
