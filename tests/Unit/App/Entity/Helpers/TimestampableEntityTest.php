<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tests\Unit\App\Entity\Helpers;

use App\Entity\Helpers\TimestampableEntity;
use Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class TimestampableEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Helpers\TimestampableEntity::setCreatedAt
   * @covers \App\Entity\Helpers\TimestampableEntity::getCreatedAt
   */
  public function testCreatedAt()
  {
    $e = $this->mock();
    $dt = new \DateTime();
    $e->setCreatedAt($dt);
    self::assertEquals($dt, $e->getCreatedAt());
  }

  /**
   * @covers \App\Entity\Helpers\TimestampableEntity::setUpdatedAt
   * @covers \App\Entity\Helpers\TimestampableEntity::getUpdatedAt
   */
  public function testUpdateAt()
  {
    $e = $this->mock();
    $dt = new \DateTime();
    $e->setUpdatedAt($dt);
    self::assertEquals($dt, $e->getUpdatedAt());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|TimestampableEntity
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(TimestampableEntity::class);
  }
//</editor-fold desc="Private Methods">
}