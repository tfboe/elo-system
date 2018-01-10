<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tests\Unit\App\Entity\Helpers;


use App\Entity\Helpers\UnsetProperty;
use App\Exceptions\ValueNotSet;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class UnsetPropertyTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testEnsureNotNull()
  {
    $entity = $this->mock();
    /** @noinspection PhpUndefinedFieldInspection */
    $entity->prop = 5;
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->ensureNotNull('prop');
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property unset of the class " . get_class($entity) . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the " .
      "constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->ensureNotNull('unset');
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|UnsetProperty
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(UnsetProperty::class);
  }
//</editor-fold desc="Private Methods">
}