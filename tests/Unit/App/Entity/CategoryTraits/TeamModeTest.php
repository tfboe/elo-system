<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tests\Unit\App\Entity\CategoryTraits;

use App\Entity\Categories\TeamMode;
use App\Exceptions\ValueNotValid;
use Tests\Helpers\UnitTestCase;

/**
 * Class TeamModeTest
 * @package Tests\Unit\App\Entity\CategoryTraits
 */
class TeamModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Entity\CategoryTraits\TeamMode::getTeamMode
   * @covers \App\Entity\CategoryTraits\TeamMode::setTeamMode
   * @uses   \App\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getTeamMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setTeamMode(TeamMode::DOUBLE);
    self::assertEquals(TeamMode::DOUBLE, $mock->getTeamMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setTeamMode(null);
    self::assertNull($mock->getTeamMode());
  }

  /**
   * @covers \App\Entity\CategoryTraits\TeamMode::setTeamMode
   * @uses   \App\Helpers\BasicEnum
   * @uses   \App\Exceptions\ValueNotValid::__construct
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in App\Entity\Categories\TeamMode. Possible values: 0, 1, 2.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setTeamMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|\App\Entity\CategoryTraits\TeamMode
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(\App\Entity\CategoryTraits\TeamMode::class);
  }
//</editor-fold desc="Private Methods">
}