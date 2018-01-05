<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Unit\App\Entity\Helpers;


use App\Entity\Helpers\NameEntity;
use App\Exceptions\ValueNotSet;
use Tests\Helpers\TestCase;
use Tests\Helpers\TestNameEntity;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class NameEntityTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testName()
  {
    $e = $this->mock();
    $e->setName("Name");
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals("Name", $e->getName());
  }

  public function testNameException()
  {
    $e = $this->mock();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property name of the class " . get_class($e) . " must be set before it can" .
      " be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getName();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return NameEntity
   */
  private function mock()
  {
    return new TestNameEntity();
  }
//</editor-fold desc="Private Methods">
}