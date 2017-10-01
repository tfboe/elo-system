<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tests\Unit\App\Entity\Helpers;

use Tests\Helpers\TestCase;
use Tests\Helpers\TestEntity;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class BaseEntityTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testMethodExists()
  {
    $e = new TestEntity();
    self::assertTrue($e->methodExists('getProp'));
    self::assertTrue($e->methodExists('setProp'));
    self::assertTrue($e->methodExists('getUser'));
    self::assertTrue($e->methodExists('setUser'));
    self::assertTrue($e->methodExists('methodExists'));
    self::assertFalse($e->methodExists('isUser'));
  }
//</editor-fold desc="Public Methods">
}