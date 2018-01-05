<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tests\Unit\App\Service;

use App\Service\DynamicServiceLoadingService;
use App\Service\RankingSystem\EloRankingInterface;
use Tests\Helpers\TestCase;

/**
 * Class EloRankingTest
 * @package Tests\Unit\App\Service\RankingSystem
 */
class DynamicServiceLoadingServiceTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testLoadRankingSystemService()
  {
    $e = new DynamicServiceLoadingService();
    $elo_service = $e->loadRankingSystemService("EloRanking");

    self::assertInstanceOf(EloRankingInterface::class, $elo_service);
    self::assertTrue($elo_service === $e->loadRankingSystemService("EloRankingInterface"));
    self::assertTrue($elo_service === $e->loadRankingSystemService("App\Service\RankingSystem\EloRanking"));
    self::assertTrue($elo_service === $e->loadRankingSystemService("App\Service\RankingSystem\EloRankingInterface"));
  }
//</editor-fold desc="Public Methods">
}