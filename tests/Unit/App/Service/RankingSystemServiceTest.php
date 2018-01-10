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
use App\Service\DynamicServiceLoadingServiceInterface;
use App\Service\RankingSystem\RankingSystemInterface;
use App\Service\RankingSystemService;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Tests\Helpers\UnitTestCase;

/**
 * Class EloRankingTest
 * @package Tests\Unit\App\Service
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RankingSystemServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Service\RankingSystemService::adaptOpenSyncFromValues
   * @uses   \App\Service\RankingSystemService::__construct
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystemService::getRankingSystems
   * @uses   \App\Service\RankingSystemService::getRankingSystemsEarliestInfluences
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testAdaptOpenSyncFromValues()
  {
    $serviceLoader = $this->createMock(DynamicServiceLoadingService::class);
    $serviceLoader->expects(self::exactly(2))
      ->method("loadRankingSystemService")
      ->willReturnCallback(function ($earliestInfluence) {
        $mock = $this->createMock(\App\Service\RankingSystem\RankingSystem::class);
        $mock->method("getEarliestInfluence")->willReturn(new \DateTime($earliestInfluence));
        return $mock;
      });
    /** @var DynamicServiceLoadingService $serviceLoader */

    /** @noinspection PhpParamsInspection */
    $service = new RankingSystemService($serviceLoader,
      $this->getMockForAbstractClass(EntityManagerInterface::class));


    $tournament = new Tournament();
    $ranking = $this->createStubWithId(RankingSystem::class, 'r1');
    $ranking->method('getServiceName')->willReturn("2017-01-01");
    $ranking->method('getOpenSyncFrom')->willReturn(new \DateTime("2017-01-01 15:00:00"));
    $ranking->expects(self::once())->method('setOpenSyncFrom')->with(new \DateTime("2017-01-01"));
    /** @var RankingSystem $ranking */
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);

    $ranking2 = $this->createStubWithId(RankingSystem::class, 'r2');
    $ranking2->method('getServiceName')->willReturn("2017-02-01");
    $ranking2->method('getOpenSyncFrom')->willReturn(new \DateTime("2017-01-30 15:00:00"));
    $ranking2->expects(self::once())->method('setOpenSyncFrom')->with(new \DateTime("2017-01-30"));
    /** @var RankingSystem $ranking2 */
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking2->getId(), $ranking2);

    $ranking3 = $this->createStubWithId(RankingSystem::class, 'r3');
    $ranking3->method('getOpenSyncFrom')->willReturn(null);
    $ranking3->expects(self::once())->method('setOpenSyncFrom')->with(new \DateTime("2017-03-01"));
    $ranking4 = $this->createStubWithId(RankingSystem::class, 'r4');
    $ranking4->method('getOpenSyncFrom')->willReturn(new \DateTime("2017-04-01"));
    $ranking4->expects(self::never())->method('setOpenSyncFrom');

    $service->adaptOpenSyncFromValues($tournament, [
      'r1' => ["rankingSystem" => $ranking, "earliestInfluence" => new \DateTime("2017-01-02")],
      'r2' => ["rankingSystem" => $ranking2, "earliestInfluence" => new \DateTime("2017-01-30")],
      'r3' => ["rankingSystem" => $ranking3, "earliestInfluence" => new \DateTime("2017-03-01")],
      'r4' => ["rankingSystem" => $ranking4, "earliestInfluence" => new \DateTime("2017-06-01")],
    ]);
  }

  /**
   * @covers \App\Service\RankingSystemService::applyRankingSystems
   * @covers \App\Service\RankingSystemService::getRankingSystems
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testApplyRankingSystems()
  {
    $tournament = new Tournament();
    /** @var RankingSystem $ranking2 */
    $ranking2 = $this->createStubWithId(RankingSystem::class, 's2');
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking2->getId(), $ranking2);
    /** @var RankingSystem $ranking3 */
    $ranking3 = $this->createStubWithId(RankingSystem::class, 's3');

    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking3->getId(), $ranking3);

    /** @var RankingSystem $ranking4 */
    $ranking4 = $this->createStubWithId(RankingSystem::class, 's4');

    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    $oldInfluences = [
      $ranking2->getId() => ["rankingSystem" => $ranking2, "earliestInfluence" => new \DateTime("2017-02-01")],
      $ranking4->getId() => ["rankingSystem" => $ranking4, "earliestInfluence" => new \DateTime("2017-04-01")]
    ];

    $serviceLoader = $this->createMock(DynamicServiceLoadingService::class);
    $mock = $this->createMock(\App\Service\RankingSystem\RankingSystem::class);
    $mock->expects(self::exactly(3))->method("updateRankingForTournament")->withConsecutive(
      [$ranking2, $tournament, self::equalTo(new \DateTime("2017-02-01"))],
      [$ranking4, $tournament, self::equalTo(new \DateTime("2017-04-01"))],
      [$ranking3, $tournament, null]
    );
    $serviceLoader->expects(self::exactly(3))
      ->method("loadRankingSystemService")
      ->willReturn($mock);

    /** @var DynamicServiceLoadingService $serviceLoader */

    /** @noinspection PhpParamsInspection */
    $service = new RankingSystemService($serviceLoader,
      $this->getMockForAbstractClass(EntityManagerInterface::class));
    $service->applyRankingSystems($tournament, $oldInfluences);
  }

  /**
   * @covers \App\Service\RankingSystemService::__construct
   */
  public function testConstruct()
  {
    $dsls = $this->getMockForAbstractClass(DynamicServiceLoadingServiceInterface::class);
    $entityManager = $this->getMockForAbstractClass(EntityManagerInterface::class);
    /** @var DynamicServiceLoadingServiceInterface $dsls */
    /** @var EntityManagerInterface $entityManager */
    $system = new RankingSystemService($dsls, $entityManager);
    self::assertInstanceOf(RankingSystemService::class, $system);
    self::assertEquals($entityManager, self::getProperty(get_class($system), 'entityManager')->getValue($system));
    self::assertEquals($dsls, self::getProperty(get_class($system), 'dsls')->getValue($system));
  }

  /**
   * @covers \App\Service\RankingSystemService::getRankingSystemsEarliestInfluences
   * @covers \App\Service\RankingSystemService::getRankingSystems
   * @uses   \App\Entity\Tournament
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Match
   * @uses   \App\Entity\Game
   * @uses   \App\Entity\Helpers\NameEntity
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Service\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testGetRankingSystemsEarliestInfluences()
  {
    $serviceLoader = $this->createMock(DynamicServiceLoadingService::class);
    $serviceLoader->expects(self::exactly(3))
      ->method("loadRankingSystemService")
      ->willReturnCallback(function ($earliestInfluence) {
        $mock = $this->createMock(\App\Service\RankingSystem\RankingSystem::class);
        $mock->method("getEarliestInfluence")->willReturn(new \DateTime($earliestInfluence));
        return $mock;
      });
    /** @var DynamicServiceLoadingService $serviceLoader */

    /** @noinspection PhpParamsInspection */
    $service = new RankingSystemService($serviceLoader,
      $this->getMockForAbstractClass(EntityManagerInterface::class));
    $tournament = new Tournament();
    $ranking2 = $this->createStubWithId(RankingSystem::class, 'r2');
    $ranking2->method('getServiceName')->willReturn("2017-04-01");
    /** @var RankingSystem $ranking2 */
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking2->getId(), $ranking2);

    $competition = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->setPhaseNumber(1)->setCompetition($competition);
    $ranking3 = $this->createStubWithId(RankingSystem::class, 'r3');
    $ranking3->method('getServiceName')->willReturn("2017-02-01");
    /** @var RankingSystem $ranking3 */

    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getRankingSystems()->set($ranking3->getId(), $ranking3);

    $match = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->setMatchNumber(1)->setPhase($phase);
    $game = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->setGameNumber(1)->setMatch($match);
    $ranking4 = $this->createStubWithId(RankingSystem::class, 'r4');
    $ranking4->method('getServiceName')->willReturn("2017-03-01");
    /** @var RankingSystem $ranking4 */
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getRankingSystems()->set($ranking4->getId(), $ranking4);


    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(
      [
        $ranking2->getId() => ["rankingSystem" => $ranking2, "earliestInfluence" => new \DateTime("2017-04-01")],
        $ranking3->getId() => ["rankingSystem" => $ranking3, "earliestInfluence" => new \DateTime("2017-02-01")],
        $ranking4->getId() => ["rankingSystem" => $ranking4, "earliestInfluence" => new \DateTime("2017-03-01")],
      ],
      $service->getRankingSystemsEarliestInfluences($tournament));
  }

  /**
   * @covers \App\Service\RankingSystemService::recalculateRankingSystems
   * @uses   \App\Service\RankingSystemService::__construct
   */
  public function testRecalculateRankingSystems()
  {
    $rs1 = $this->createMock(RankingSystem::class);
    $rs1->expects(self::once())->method('getServiceName')->willReturn('service');
    $rs1->expects(self::once())->method('getOpenSyncFrom')->willReturn(new \DateTime("2017-02-01"));
    $rs1->expects(self::once())->method('setOpenSyncFrom')->with(null);
    $rs2 = $this->createMock(RankingSystem::class);
    $rs2->expects(self::once())->method('getServiceName')->willReturn('service');
    $rs2->expects(self::once())->method('getOpenSyncFrom')->willReturn(new \DateTime("2017-05-01"));
    $rs2->expects(self::once())->method('setOpenSyncFrom')->with(null);
    $entityManager = $this->getMockForAbstractClass(EntityManager::class, [], '',
      false, true, true, ['createQueryBuilder']);
    $queryBuilder = $this->getMockForAbstractClass(QueryBuilder::class, [$entityManager],
      '', true, true, true, ['getQuery']);
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn([$rs1, $rs2]);
    $queryBuilder->expects(static::once())->method('getQuery')->willReturnCallback(
      function () use ($queryBuilder, $query) {
        $slash = '\\';
        $first = 'SELECT s';
        $second = ' FROM App';
        $rest = 'RankingSystem s WHERE s.openSyncFrom IS NOT NULL';
        /** @var $queryBuilder QueryBuilder */
        self::assertEquals(
          $first . $second . $slash . 'Entity' . $slash . $rest,
          $queryBuilder->getDQL());
        return $query;
      });
    $entityManager->expects(static::once())->method('createQueryBuilder')->willReturn($queryBuilder);
    $dsls = $this->getMockForAbstractClass(DynamicServiceLoadingServiceInterface::class);
    $service = $this->getMockForAbstractClass(RankingSystemInterface::class);
    $service->expects(self::exactly(2))->method('updateRankingFrom')
      ->withConsecutive([$rs1, new \DateTime("2017-02-01")], [$rs2, new \DateTime("2017-05-01")]);
    $dsls->expects(self::exactly(2))->method('loadRankingSystemService')->with('service')->willReturn($service);
    /** @var DynamicServiceLoadingServiceInterface $dsls */
    /** @var EntityManagerInterface $entityManager */
    $system = new RankingSystemService($dsls, $entityManager);
    $system->recalculateRankingSystems();
  }
//</editor-fold desc="Public Methods">
}