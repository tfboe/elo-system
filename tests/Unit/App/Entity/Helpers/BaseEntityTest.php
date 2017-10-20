<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tests\Unit\App\Entity\Helpers;

use App\Exceptions\ValueNotSet;
use Tests\Helpers\TestCase;
use Tests\Helpers\TestEntity;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class BaseEntityTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testEnsureNotNull()
  {
    $e = new TestEntity();
    $e->setProp(5);
    self::assertEquals(5, $e->getProp());
  }

  public function testEnsureNotNullException()
  {
    $e = new TestEntity();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property prop of the class " . TestEntity::class . " must be set before it can" .
      " be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $e->getProp();
  }

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