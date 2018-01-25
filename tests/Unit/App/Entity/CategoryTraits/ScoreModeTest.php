<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tests\Tfboe\FmLib\Entity\CategoryTraits;

use Tfboe\FmLib\Entity\Categories\ScoreMode;
use Tfboe\FmLib\Exceptions\ValueNotValid;
use Tests\Helpers\UnitTestCase;

/**
 * Class ScoreModeTest
 * @package Tests\Tfboe\FmLib\Entity\CategoryTraits
 */
class ScoreModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\ScoreMode::getScoreMode
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\ScoreMode::setScoreMode
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getScoreMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setScoreMode(ScoreMode::BEST_OF_FIVE);
    self::assertEquals(ScoreMode::BEST_OF_FIVE, $mock->getScoreMode());
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setScoreMode(null);
    self::assertNull($mock->getScoreMode());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\CategoryTraits\ScoreMode::setScoreMode
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Exceptions\ValueNotValid::__construct
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in Tfboe\FmLib\Entity\Categories\ScoreMode. Possible values: 0, 1, 2.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setScoreMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|\Tfboe\FmLib\Entity\CategoryTraits\ScoreMode
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(\Tfboe\FmLib\Entity\CategoryTraits\ScoreMode::class);
  }
//</editor-fold desc="Private Methods">
}