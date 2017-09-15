<?php

class ExampleTest extends TestCase
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
//</editor-fold desc="Public Methods">
}
