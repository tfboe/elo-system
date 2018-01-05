<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tests\Unit\App\Service\RankingSystem;


use App\Entity\RankingSystem;
use App\Service\RankingSystem\EloRanking;
use Tests\Helpers\TestCase;

/**
 * Class EloRankingTest
 * @package Tests\Unit\App\Service\RankingSystem
 */
class EloRankingTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testUpdateRankingFrom()
  {
    //TODO
    $service = new EloRanking();
    $ranking = new RankingSystem([]);
    $service->updateRankingFrom($ranking, new \DateTime("2017-01-01"));
    self::assertTrue(True);
  }
//</editor-fold desc="Public Methods">
}