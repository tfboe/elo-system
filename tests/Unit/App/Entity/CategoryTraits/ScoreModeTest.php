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
use Tests\Helpers\TestCase;

class ScoreModeTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testProperty()
  {
    $mock = $this->mock();
    self::assertNull($mock->getScoreMode());
    $mock->setScoreMode(ScoreMode::BEST_OF_FIVE);
    self::assertEquals(ScoreMode::BEST_OF_FIVE, $mock->getScoreMode());
  }

  public function testPropertyException()
  {
    $mock = $this->mock();
    $this->expectException(ValueNotValid::class);
    $this->expectExceptionMessage(
      'The following value is not valid: 100 in App\Entity\Categories\ScoreMode. Possible values: 0, 1, 2.');
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