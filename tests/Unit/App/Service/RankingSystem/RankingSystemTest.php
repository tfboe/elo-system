<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:54 PM
 */

namespace Tests\Unit\App\Service\RankingSystem;

use App\Entity\Competition;
use App\Entity\Game;
use App\Entity\Helpers\TreeStructureEntityInterface;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\RankingSystemList;
use App\Entity\Tournament;
use App\Helpers\Level;
use App\Service\RankingSystem\EntityComparerInterface;
use App\Service\RankingSystem\RankingSystem;
use App\Service\RankingSystem\TimeServiceInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Tests\Helpers\UnitTestCase;


/**
 * Class RankingSystemTest
 * @package Tests\Unit\App\Service\RankingSystem
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RankingSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testConstruct()
  {
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $entityComparer = $this->createMock(EntityComparerInterface::class);
    $system = $this->getMockForAbstractClass(RankingSystem::class, [$entityManager, $timeService, $entityComparer]);
    self::assertInstanceOf(RankingSystem::class, $system);
    self::assertEquals($entityManager, self::getProperty(get_class($system), 'entityManager')->getValue($system));
    self::assertEquals($timeService, self::getProperty(get_class($system), 'timeService')->getValue($system));
    self::assertEquals($entityComparer, self::getProperty(get_class($system), 'entityComparer')->getValue($system));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @covers \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Game
   * @uses   \App\Entity\Helpers\NameEntity
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testGetEarliestInfluenceGameLevel()
  {
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TreeStructureEntityInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    /** @var \App\Entity\RankingSystem $ranking */
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)]);
    $service->method("getLevel")->willReturn(Level::GAME);
    /** @var RankingSystem $service */
    $tournament = new Tournament();
    $competition = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->setPhaseNumber(1)->setCompetition($competition);
    $match = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->setMatchNumber(1)->setPhase($phase);
    self::assertNull($service->getEarliestInfluence($ranking, $tournament));

    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertNull($service->getEarliestInfluence($ranking, $tournament));

    $game = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->setGameNumber(1)->setMatch($match);
    $gameEndTime = new \DateTime("2017-06-01 00:00:00");
    $game->setEndTime($gameEndTime);
    self::assertEquals($gameEndTime, $service->getEarliestInfluence($ranking, $tournament));

    $game2 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game2->setGameNumber(2)->setMatch($match);
    $game2EndTime = new \DateTime("2017-05-01 00:00:00");
    $game2->setEndTime($game2EndTime);
    self::assertEquals($game2EndTime, $service->getEarliestInfluence($ranking, $tournament));

    $game3 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game3->setGameNumber(3)->setMatch($match);
    $game3EndTime = new \DateTime("2017-07-01 00:00:00");
    $game3->setEndTime($game3EndTime);
    self::assertEquals($game2EndTime, $service->getEarliestInfluence($ranking, $tournament));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @covers \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Game
   * @uses   \App\Entity\Helpers\NameEntity
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
   */
  public function testGetEarliestInfluenceGameLevelWithDifferentImpactLevels()
  {
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TreeStructureEntityInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    /** @var \App\Entity\RankingSystem $ranking */
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)]);
    $service->method("getLevel")->willReturn(Level::GAME);
    /** @var RankingSystem $service */
    $tournament = new Tournament();
    $competition = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->setPhaseNumber(1)->setCompetition($competition);
    $match = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->setMatchNumber(1)->setPhase($phase);
    $game = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->setGameNumber(1)->setMatch($match);
    $endTime1 = new \DateTime("2017-12-01 00:00:00");
    $game->setEndTime($endTime1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime1, $service->getEarliestInfluence($ranking, $tournament));

    $game2 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game2->setGameNumber(2)->setMatch($match);
    $endTime2 = new \DateTime("2017-11-01 00:00:00");
    $game2->setEndTime($endTime2);
    self::assertEquals($endTime1, $service->getEarliestInfluence($ranking, $tournament));

    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime2, $service->getEarliestInfluence($ranking, $tournament));

    $match2 = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    $match2->setMatchNumber(2)->setPhase($phase);
    $game3 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game3->setGameNumber(1)->setMatch($match2);
    $endTime3 = new \DateTime("2017-10-01 00:00:00");
    $game3->setEndTime($endTime3);
    self::assertEquals($endTime2, $service->getEarliestInfluence($ranking, $tournament));

    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime3, $service->getEarliestInfluence($ranking, $tournament));

    $phase2 = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase2->setPhaseNumber(2)->setCompetition($competition);
    $match3 = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    $match3->setMatchNumber(1)->setPhase($phase2);
    $game4 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game4->setGameNumber(1)->setMatch($match3);
    $endTime4 = new \DateTime("2017-09-01 00:00:00");
    $game4->setEndTime($endTime4);
    self::assertEquals($endTime3, $service->getEarliestInfluence($ranking, $tournament));

    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime4, $service->getEarliestInfluence($ranking, $tournament));

    $competition2 = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition2->setName("TestCompetition2")->setTournament($tournament);
    $phase3 = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase3->setPhaseNumber(1)->setCompetition($competition2);
    $match4 = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    $match4->setMatchNumber(1)->setPhase($phase3);
    $game5 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game5->setGameNumber(1)->setMatch($match4);
    $endTime5 = new \DateTime("2017-01-01 00:00:00");
    $game5->setEndTime($endTime5);
    self::assertEquals($endTime4, $service->getEarliestInfluence($ranking, $tournament));

    $game6 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game6->setGameNumber(2)->setMatch($match4);
    $endTime6 = new \DateTime("2017-10-01 00:00:00");
    $game6->setEndTime($endTime6);
    /** @noinspection PhpUnhandledExceptionInspection */
    $game6->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime4, $service->getEarliestInfluence($ranking, $tournament));

    $game7 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game7->setGameNumber(3)->setMatch($match4);
    $endTime7 = new \DateTime("2017-08-01 00:00:00");
    $game7->setEndTime($endTime7);
    /** @noinspection PhpUnhandledExceptionInspection */
    $game7->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime7, $service->getEarliestInfluence($ranking, $tournament));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @covers \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testGetEarliestInfluenceTournamentLevel()
  {
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TreeStructureEntityInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    /** @var \App\Entity\RankingSystem $ranking */
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)]);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    /** @var RankingSystem $service */
    $tournament = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $endTime = new \DateTime("2017-03-01 00:00:00");
    $tournament->setEndTime($endTime);
    self::assertEquals($endTime, $service->getEarliestInfluence($ranking, $tournament));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::getEntities
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testGetEntities()
  {
    //create mock for input
    $ranking = $this->createMock(\App\Entity\RankingSystem::class);

    //create service mock
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class), $this->createMock(TimeServiceInterface::class),
        $this->createMock(EntityComparerInterface::class)]);

    //create mock for queryBuilder
    $entityList = ['e1', 'e2'];
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn($entityList);
    //create query builder mock for getEntities
    $queryBuilder = $this->createMock(QueryBuilder::class);
    $queryBuilder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2017-01-01"))->willReturn($queryBuilder);

    /** @var $service RankingSystem */
    self::assertEquals($entityList, static::getMethod(get_class($service), 'getEntities')
      ->invokeArgs($service, [$ranking, new \DateTime("2017-01-01")]));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentOldEarliestIsEarlier()
  {
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TreeStructureEntityInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-02-01 00:00:00");
    $tournament->setUpdatedAt($endedAt);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $oldInfluence = new \DateTime("2017-01-01 00:00:00");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01 00:00:00"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, $oldInfluence);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentOldEarliestIsNotNullAndTournamentNotRanked()
  {
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($endedAt);
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class),
        $this->createMock(TimeServiceInterface::class),
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $oldInfluence = new \DateTime("2017-02-01 00:00:00");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-02-01 00:00:00"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, $oldInfluence);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentOldEarliestIsNull()
  {
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-01-01 00:00:00");
    $tournament->setEndTime($endedAt);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TreeStructureEntityInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01 00:00:00"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, null);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentOldEarliestIsNullAndTournamentNotRanked()
  {
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($endedAt);
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class), $this->createMock(TimeServiceInterface::class),
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $service->expects(self::never())
      ->method('updateRankingFrom');

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, null);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentTournamentIsEarlier()
  {
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-01-01");
    $tournament->setEndTime($endedAt);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TreeStructureEntityInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $oldInfluence = new \DateTime("2017-02-01");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, $oldInfluence);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystem::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystem::cloneInto
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEntities
   */
  public function testUpdateRankingFrom()
  {
    //create mock for input
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);

    //create mocks for ranking lists
    $list1 = $this->createMock(RankingSystemList::class);
    $list1->method('isCurrent')->willReturn(false);
    $list1->method('getLastEntryTime')->willReturn(new \DateTime("2017-01-01"));

    $list2 = $this->createMock(RankingSystemList::class);
    $list2->method('isCurrent')->willReturn(false);
    $list2->method('getLastEntryTime')->willReturn(new \DateTime("2017-02-01"));

    $list3 = $this->createMock(RankingSystemList::class);
    $list3->method('isCurrent')->willReturn(false);
    $list3->method('getLastEntryTime')->willReturn(new \DateTime("2017-03-01"));

    $list4 = $this->createMock(RankingSystemList::class);
    $list4->method('isCurrent')->willReturn(false);
    $list4->method('getLastEntryTime')->willReturn(new \DateTime("2017-04-01"));

    $list5 = $this->createMock(RankingSystemList::class);
    $list5->method('isCurrent')->willReturn(true);
    $list5->method('getLastEntryTime')->willReturn(new \DateTime("2017-05-01"));

    $lists = $this->createMock(Collection::class);
    $lists->expects(static::once())->method('toArray')->willReturn([$list1, $list2, $list3, $list4, $list5]);

    //finish mock for input
    $ranking->expects(static::once())->method('getLists')->willReturn($lists);

    //create time service, entity comparer and ranking service mock
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TreeStructureEntityInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    $entityComparer = $this->createMock(EntityComparerInterface::class);
    $entityComparer->method('compareEntities')->willReturnCallback(
      function (TreeStructureEntityInterface $entity1, TreeStructureEntityInterface $entity2) {
        return $entity1->getEndTime() <=> $entity2->getEndTime();
      });
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)]);

    //create entities mocks
    $entity1 = $this->createStubWithId(TreeStructureEntityInterface::class, "e1");
    $entity1->method('getEndTime')->willReturn(new \DateTime("2017-03-01"));

    $entity2 = $this->createStubWithId(TreeStructureEntityInterface::class, "e2");
    $entity2->method('getEndTime')->willReturn(new \DateTime("2017-02-01 00:00:01"));

    $entity3 = $this->createStubWithId(TreeStructureEntityInterface::class, "e3");
    $entity3->method('getEndTime')->willReturn(new \DateTime("2017-05-01"));

    $entity4 = $this->createStubWithId(TreeStructureEntityInterface::class, "e4");
    $entity4->method('getEndTime')->willReturn(new \DateTime("2017-03-02"));

    $parent = $this->createStubWithId(TreeStructureEntityInterface::class, "e4");
    $parent->method('getEndTime')->willReturn(new \DateTime("2017-12-02"));
    $entity4->method('getParent')->willReturn($parent);

    //create query mock for getEntities
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn([$entity1, $entity2, $entity3, $entity4]);
    //create query builder mock for getEntities
    $queryBuilder = $this->createMock(QueryBuilder::class);
    $queryBuilder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2017-02-01"))->willReturn($queryBuilder);

    /** @var RankingSystem $service */
    /** @var \App\Entity\RankingSystem $ranking */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystem::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystem::cloneInto
   * @uses   \App\Entity\RankingSystemList
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEntities
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testUpdateRankingFromNoCurrent()
  {
    //create mock for input
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class, 'r1');

    //create mocks for ranking lists
    $list = $this->createMock(RankingSystemList::class);
    $list->method('isCurrent')->willReturn(false);
    $list->method('getLastEntryTime')->willReturn(new \DateTime("2017-01-01"));

    $lists = $this->createMock(Collection::class);
    $lists->expects(static::once())->method('toArray')->willReturn([$list]);
    $lists->expects(static::once())->method('set')->with('new')->willReturnSelf();

    //finish mock for input
    $ranking->expects(static::exactly(2))->method('getLists')->willReturn($lists);

    //create service mock
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $entityManager->expects(static::once())->method('persist')->willReturnCallback(
      function (RankingSystemList $entity) {
        self::assertInstanceOf(RankingSystemList::class, $entity);
        self::assertTrue($entity->isCurrent());
        static::getProperty(get_class($entity), 'id')->setValue($entity, 'new');
      });
    $service = $this->getMockForAbstractClass(RankingSystem::class, [$entityManager,
      $this->createMock(TimeServiceInterface::class), $this->createMock(EntityComparerInterface::class)]);

    //create query mock for getEntities
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn([]);
    //create query builder mock for getEntities
    $queryBuilder = $this->createMock(QueryBuilder::class);
    $queryBuilder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2017-01-01"))->willReturn($queryBuilder);

    /** @var RankingSystem $service */
    /** @var \App\Entity\RankingSystem $ranking */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystem::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystem::cloneInto
   * @uses   \App\Entity\RankingSystemList
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEntities
   */
  public function testUpdateRankingFromNoReusable()
  {
    //create mock for input
    $ranking = $this->createStubWithId(\App\Entity\RankingSystem::class);

    //create mocks for ranking lists
    $list = $this->createMock(RankingSystemList::class);
    $list->method('isCurrent')->willReturn(true);
    $list->method('getLastEntryTime')->willReturn(new \DateTime("2017-06-01"));

    $lists = $this->createMock(Collection::class);
    $lists->expects(static::once())->method('toArray')->willReturn([$list]);

    //finish mock for input
    $ranking->expects(static::once())->method('getLists')->willReturn($lists);

    //create service mock
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class), $this->createMock(TimeServiceInterface::class),
        $this->createMock(EntityComparerInterface::class)]);

    //create query mock for getEntities
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn([]);
    //create query builder mock for getEntities
    $queryBuilder = $this->createMock(QueryBuilder::class);
    $queryBuilder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2000-01-01"))->willReturn($queryBuilder);

    /** @var RankingSystem $service */
    /** @var \App\Entity\RankingSystem $ranking */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
  }
//</editor-fold desc="Public Methods">
}