<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:05 PM
 */

namespace Tests\Unit\App\Entity\CategoryTraits;

use App\Entity\Categories\ScoreMode;
use App\Exceptions\ValueNotValid;
use Tests\Helpers\UnitTestCase;

/**
 * Class ScoreModeTest
 * @package Tests\Unit\App\Entity\CategoryTraits
 */
class ScoreModeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\CategoryTraits\ScoreMode::getScoreMode
   * @covers \App\Entity\CategoryTraits\ScoreMode::setScoreMode
   * @uses   \App\Helpers\BasicEnum
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
   * @covers \App\Entity\CategoryTraits\ScoreMode::setScoreMode
   * @uses   \App\Helpers\BasicEnum
   * @uses   \App\Exceptions\ValueNotValid::__construct
   */
  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in App\Entity\Categories\ScoreMode. Possible values: 0, 1, 2.');
    /** @noinspection PhpUnhandledExceptionInspection */
    $mock->setScoreMode(100);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|\App\Entity\CategoryTraits\ScoreMode
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(\App\Entity\CategoryTraits\ScoreMode::class);
  }
//</editor-fold desc="Private Methods">
}