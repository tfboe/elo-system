<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/15/17
 * Time: 4:40 PM
 */

class LumenTest extends \PHPUnit\Framework\TestCase
{
//<editor-fold desc="Public Methods">
  public function testBootstrap()
  {
    $app = require __DIR__ . '/../bootstrap/app.php';
    $this->assertNotNull($app);
  }
//</editor-fold desc="Public Methods">
}