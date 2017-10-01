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
use Tests\Helpers\TestCase;

/**
 * Class TeamModeTest
 * @package Tests\Unit\App\Entity\CategoryTraits
 */
class TeamModeTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getTeamMode());
    $mock->setTeamMode(TeamMode::DOUBLE);
    self::assertEquals(TeamMode::DOUBLE, $mock->getTeamMode());
  }

  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in App\Entity\Categories\TeamMode. Possible values: 0, 1, 2.');
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