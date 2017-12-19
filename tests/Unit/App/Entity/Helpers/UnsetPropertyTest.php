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
class UnsetPropertyTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testEnsureNotNull()
  {
    $e = new TestEntity();
    $e->setProp(5);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(5, $e->getProp());
  }

  public function testEnsureNotNullException()
  {
    $e = new TestEntity();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property prop of the class " . TestEntity::class . " must be set before it can" .
      " be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getProp();
  }
//</editor-fold desc="Public Methods">
}