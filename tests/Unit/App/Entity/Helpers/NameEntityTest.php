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
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class NameEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Helpers\NameEntity::getName
   * @covers \App\Entity\Helpers\NameEntity::setName
   */
  public function testName()
  {
    $entity = $this->mock();
    $entity->setName("Name");
    self::assertEquals("Name", $entity->getName());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return MockObject|NameEntity
   */
  private function mock(): MockObject
  {
    return $this->getMockForTrait(NameEntity::class);
  }
//</editor-fold desc="Private Methods">
}