<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tests\Unit\App\Service\RankingSystem;


use App\Helpers\Level;
use App\Service\RankingSystem\GameRankingSystem;
use Tests\Helpers\TestCase;

/**
 * Class EloRankingTest
 * @package Tests\Unit\App\Service\RankingSystem
 */
class GameRankingSystemTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testLevel()
  {
    $e = $this->mock();
    self::assertEquals(Level::GAME, self::callProtectedMethod($e, "getLevel"));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|GameRankingSystem
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForAbstractClass(GameRankingSystem::class);
  }
//</editor-fold desc="Private Methods">
}