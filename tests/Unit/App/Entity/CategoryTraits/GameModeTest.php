<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tests\Unit\App\Entity\CategoryTraits;

use App\Entity\Categories\GameMode;
use App\Exceptions\ValueNotValid;
use Tests\Helpers\UnitTestCase;

/**
 * Class GameModeTest
 * @package Tests\Unit\App\Entity\CategoryTraits
 */
class GameModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\CategoryTraits\GameMode::getGameMode
   * @covers \App\Entity\CategoryTraits\GameMode::setGameMode
   * @uses   \App\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getGameMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setGameMode(GameMode::SPEEDBALL);
    self::assertEquals(GameMode::SPEEDBALL, $mock->getGameMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setGameMode(null);
    self::assertNull($mock->getGameMode());
  }

  /**
   * @covers \App\Entity\CategoryTraits\GameMode::setGameMode
   * @uses   \App\Helpers\BasicEnum
   * @uses   \App\Exceptions\ValueNotValid::__construct
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in App\Entity\Categories\GameMode. Possible values: 0, 1, 2.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setGameMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|\App\Entity\CategoryTraits\GameMode
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(\App\Entity\CategoryTraits\GameMode::class);
  }
//</editor-fold desc="Private Methods">
}