<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tests\Tfboe\FmLib\Entity\CategoryTraits;

use Tfboe\FmLib\Entity\Categories\GameMode;
use Tfboe\FmLib\Exceptions\ValueNotValid;
use Tests\Helpers\UnitTestCase;

/**
 * Class GameModeTest
 * @package Tests\Tfboe\FmLib\Entity\CategoryTraits
 */
class GameModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\GameMode::getGameMode
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\GameMode::setGameMode
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
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
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\GameMode::setGameMode
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Exceptions\ValueNotValid::__construct
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in Tfboe\FmLib\Entity\Categories\GameMode. Possible values: 0, 1, 2.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setGameMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|\Tfboe\FmLib\Entity\CategoryTraits\GameMode
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(\Tfboe\FmLib\Entity\CategoryTraits\GameMode::class);
  }
//</editor-fold desc="Private Methods">
}