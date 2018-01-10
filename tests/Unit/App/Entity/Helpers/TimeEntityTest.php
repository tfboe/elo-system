<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tests\Unit\App\Entity\Helpers;

use App\Entity\Helpers\TimeEntity;
use Tests\Helpers\UnitTestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class TimeEntityTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Helpers\TimeEntity::setEndTime
   * @covers \App\Entity\Helpers\TimeEntity::getEndTime
   */
  public function testEndTime()
  {
    $entity = $this->mock();
    $time = new \DateTime('2017-12-31 16:00', new \DateTimeZone('Europe/Vienna'));
    $entity->setEndTime($time);
    self::assertEquals($time, $entity->getEndTime());
  }

  /**
   * @covers \App\Entity\Helpers\TimeEntity::getEndTime
   */
  public function testGetEndTimeNotLocalized()
  {
    $entity = $this->mock();
    $parentClass = (new \ReflectionObject($entity))->getParentClass();
    $property = $parentClass->getProperty('endTime');
    $property->setAccessible(true);

    self::getProperty(get_class($entity), 'endTime')->setValue($entity,
      new \DateTime("2017-01-01 05:00:00", new \DateTimeZone("UTC")));
    self::getProperty(get_class($entity), 'endTimezone')->setValue($entity, "+02:00");

    $time = $entity->getEndTime();
    self::assertEquals("2017-01-01 07:00:00 +0200", $time->format("Y-m-d H:i:s O"));
  }

  /**
   * @covers \App\Entity\Helpers\TimeEntity::getStartTime
   */
  public function testGetStartTimeNotLocalized()
  {
    $entity = $this->mock();
    $parentClass = (new \ReflectionObject($entity))->getParentClass();
    $property = $parentClass->getProperty('startTime');
    $property->setAccessible(true);
    $property->setValue($entity, new \DateTime("2017-01-01 05:00:00", new \DateTimeZone("UTC")));

    $property = $parentClass->getProperty('startTimezone');
    $property->setAccessible(true);
    $property->setValue($entity, "+02:00");

    $time = $entity->getStartTime();
    self::assertEquals("2017-01-01 07:00:00 +0200", $time->format("Y-m-d H:i:s O"));
  }

  /**
   * @covers \App\Entity\Helpers\TimeEntity
   */
  public function testInitialState()
  {
    $entity = $this->mock();
    self::assertNull($entity->getStartTime());
    self::assertNull($entity->getEndTime());
  }

  /**
   * @covers \App\Entity\Helpers\TimeEntity::setStartTime
   * @covers \App\Entity\Helpers\TimeEntity::getStartTime
   */
  public function testStartTime()
  {
    $entity = $this->mock();
    $time = new \DateTime('2017-12-31 16:00', new \DateTimeZone('Europe/Vienna'));
    $entity->setStartTime($time);
    self::assertEquals($time, $entity->getStartTime());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|TimeEntity
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(TimeEntity::class, [], "MockedTrait");
  }
//</editor-fold desc="Private Methods">
}