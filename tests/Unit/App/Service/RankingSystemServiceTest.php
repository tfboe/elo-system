<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tests\Unit\App\Service;

use App\Entity\Competition;
use App\Entity\Game;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\RankingSystem;
use App\Entity\Tournament;
use App\Service\DynamicServiceLoadingService;
use App\Service\RankingSystemService;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class EloRankingTest
 * @package Tests\Unit\App\Service\RankingSystem
 */
class RankingSystemServiceTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testApplyRankingSystems()
  {
    $tournament = new Tournament();
    $ranking1 = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking1);
    $ranking2 = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking2);
    $ranking2->setServiceName("");
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking2->getId(), $ranking2);
    $ranking3 = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking3);
    $ranking3->setServiceName("");

    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking3->getId(), $ranking3);

    $ranking4 = new RankingSystem([]);
    $ranking4->setServiceName("");
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking4);

    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    $old_influences = [
      $ranking2->getId() => ["rankingSystem" => $ranking2, "earliestInfluence" => new \DateTime("2017-02-01")],
      $ranking4->getId() => ["rankingSystem" => $ranking4, "earliestInfluence" => new \DateTime("2017-04-01")]
    ];

    $service_loader = $this->createMock(DynamicServiceLoadingService::class);
    $mock = $this->getMockForAbstractClass(\App\Service\RankingSystem\RankingSystem::class, [], "", true, true, true,
      ["updateRankingForTournament"]);
    $mock->expects(self::exactly(3))->method("updateRankingForTournament")->withConsecutive(
      [$ranking2, $tournament, self::equalTo(new \DateTime("2017-02-01"))],
      [$ranking4, $tournament, self::equalTo(new \DateTime("2017-04-01"))],
      [$ranking3, $tournament, null]
    );
    $service_loader->expects(self::exactly(3))
      ->method("loadRankingSystemService")
      ->willReturn($mock);

    /** @var DynamicServiceLoadingService $service_loader */

    $service = new RankingSystemService($service_loader);
    $service->applyRankingSystems($tournament, $old_influences);
  }

  public function testGetRankingSystemsEarliestInfluences()
  {
    $service_loader = $this->createMock(DynamicServiceLoadingService::class);
    $service_loader->expects(self::exactly(3))
      ->method("loadRankingSystemService")
      ->willReturnCallback(function ($earliestInfluence) {
        $mock = $this->getMockForAbstractClass(
          \App\Service\RankingSystem\RankingSystem::class, [], "", true, true, true, ["getEarliestInfluence"]);
        $mock->method("getEarliestInfluence")->willReturn(new \DateTime($earliestInfluence));
        return $mock;
      });
    /** @var DynamicServiceLoadingService $service_loader */

    $service = new RankingSystemService($service_loader);
    $tournament = new Tournament();
    $ranking1 = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking1);
    $ranking2 = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking2);
    $ranking2->setServiceName("2017-04-01");
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking2->getId(), $ranking2);

    $competition = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->setPhaseNumber(1)->setCompetition($competition);
    $ranking3 = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking3);
    $ranking3->setServiceName("2017-02-01");

    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getRankingSystems()->set($ranking3->getId(), $ranking3);

    $match = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->setMatchNumber(1)->setPhase($phase);
    $game = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->setGameNumber(1)->setMatch($match);
    $ranking4 = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking4);
    $ranking4->setServiceName("2017-03-01");
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getRankingSystems()->set($ranking4->getId(), $ranking4);


    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(
      [
        $ranking2->getId() => ["rankingSystem" => $ranking2, "earliestInfluence" => new \DateTime("2017-04-01")],
        $ranking3->getId() => ["rankingSystem" => $ranking3, "earliestInfluence" => new \DateTime("2017-02-01")],
        $ranking4->getId() => ["rankingSystem" => $ranking4, "earliestInfluence" => new \DateTime("2017-03-01")],
      ],
      $service->getRankingSystemsEarliestInfluences($tournament));
  }
//</editor-fold desc="Public Methods">
}