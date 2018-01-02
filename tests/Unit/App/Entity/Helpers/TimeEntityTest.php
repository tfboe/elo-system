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
use Tests\Helpers\TestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class TimeEntityTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testEndTime()
  {
    $entity = $this->mock();
    $time = new \DateTime('2017-12-31 16:00', new \DateTimeZone('Europe/Vienna'));
    $entity->setEndTime($time);
    self::assertEquals($time, $entity->getEndTime());
  }

  public function testInitialState()
  {
    $entity = $this->mock();
    self::assertNull($entity->getStartTime());
    self::assertNull($entity->getEndTime());
  }

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
    return $this->getMockForTrait(TimeEntity::class);
  }
//</editor-fold desc="Private Methods">
}