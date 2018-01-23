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
use App\Entity\Helpers\TournamentHierarchyEntity;
use App\Entity\Helpers\TournamentHierarchyInterface;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\Player;
use App\Entity\RankingSystem;
use App\Entity\RankingSystemChange;
use App\Entity\RankingSystemList;
use App\Entity\RankingSystemListEntry;
use App\Entity\Tournament;
use App\Exceptions\PreconditionFailedException;
use App\Helpers\Level;
use App\Service\RankingSystem\EntityComparerInterface;
use App\Service\RankingSystem\RankingSystemService;
use App\Service\RankingSystem\TimeServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Helpers\UnitTestCase;


/**
 * Class RankingSystemServiceTest
 * @package Tests\Unit\App\Service\RankingSystemService
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class RankingSystemServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::__construct
   */
  public function testConstruct()
  {
    $entityManager = $this->createMock(EntityManagerInterface::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $entityComparer = $this->createMock(EntityComparerInterface::class);
    $system = $this->getMockForAbstractClass(RankingSystemService::class,
      [$entityManager, $timeService, $entityComparer]);
    self::assertInstanceOf(RankingSystemService::class, $system);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entityManager, self::getProperty(get_class($system), 'entityManager')->getValue($system));
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($timeService, self::getProperty(get_class($system), 'timeService')->getValue($system));
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entityComparer, self::getProperty(get_class($system), 'entityComparer')->getValue($system));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getAverage
   */
  public function testGetAverage()
  {
    /** @var $service RankingSystemService */
    $service = $this->getMockForAbstractClass(RankingSystemService::class, [], '', false);

    $entry1 = $this->createMock(RankingSystemListEntry::class);
    $entry1->method('getPoints')->willReturn(1.0);
    $entry2 = $this->createMock(RankingSystemListEntry::class);
    $entry2->method('getPoints')->willReturn(2.0);

    self::assertEquals(1.5, static::callProtectedMethod($service, 'getAverage', [[$entry1, $entry2]]));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getEarliestInfluence
   * @covers \App\Service\RankingSystem\RankingSystemService::getEarliestEntityInfluence
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Game
   * @uses   \App\Entity\Helpers\NameEntity
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Match
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testGetEarliestInfluenceGameLevel()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TournamentHierarchyInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    /** @var RankingSystem $ranking */
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)]);
    $service->method("getLevel")->willReturn(Level::GAME);
    /** @var RankingSystemService $service */
    $tournament = new Tournament();
    $competition = new Competition();
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    $phase->setPhaseNumber(1)->setCompetition($competition);
    $match = new Match();
    $match->setMatchNumber(1)->setPhase($phase);
    self::assertNull($service->getEarliestInfluence($ranking, $tournament));

    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertNull($service->getEarliestInfluence($ranking, $tournament));

    $game = new Game();
    $game->setGameNumber(1)->setMatch($match);
    $gameEndTime = new \DateTime("2017-06-01 00:00:00");
    $game->setEndTime($gameEndTime);
    self::assertEquals($gameEndTime, $service->getEarliestInfluence($ranking, $tournament));

    $game2 = new Game();
    $game2->setGameNumber(2)->setMatch($match);
    $game2EndTime = new \DateTime("2017-05-01 00:00:00");
    $game2->setEndTime($game2EndTime);
    self::assertEquals($game2EndTime, $service->getEarliestInfluence($ranking, $tournament));

    $game3 = new Game();
    $game3->setGameNumber(3)->setMatch($match);
    $game3EndTime = new \DateTime("2017-07-01 00:00:00");
    $game3->setEndTime($game3EndTime);
    self::assertEquals($game2EndTime, $service->getEarliestInfluence($ranking, $tournament));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getEarliestInfluence
   * @covers \App\Service\RankingSystem\RankingSystemService::getEarliestEntityInfluence
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Game
   * @uses   \App\Entity\Helpers\NameEntity
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Match
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
   */
  public function testGetEarliestInfluenceGameLevelWithDifferentImpactLevels()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TournamentHierarchyInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    /** @var RankingSystem $ranking */
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)]);
    $service->method("getLevel")->willReturn(Level::GAME);
    /** @var RankingSystemService $service */
    $tournament = new Tournament();
    $competition = new Competition();
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    $phase->setPhaseNumber(1)->setCompetition($competition);
    $match = new Match();
    $match->setMatchNumber(1)->setPhase($phase);
    $game = new Game();
    $game->setGameNumber(1)->setMatch($match);
    $endTime1 = new \DateTime("2017-12-01 00:00:00");
    $game->setEndTime($endTime1);
    $game->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime1, $service->getEarliestInfluence($ranking, $tournament));

    $game2 = new Game();
    $game2->setGameNumber(2)->setMatch($match);
    $endTime2 = new \DateTime("2017-11-01 00:00:00");
    $game2->setEndTime($endTime2);
    self::assertEquals($endTime1, $service->getEarliestInfluence($ranking, $tournament));

    $match->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime2, $service->getEarliestInfluence($ranking, $tournament));

    $match2 = new Match();
    $match2->setMatchNumber(2)->setPhase($phase);
    $game3 = new Game();
    $game3->setGameNumber(1)->setMatch($match2);
    $endTime3 = new \DateTime("2017-10-01 00:00:00");
    $game3->setEndTime($endTime3);
    self::assertEquals($endTime2, $service->getEarliestInfluence($ranking, $tournament));

    $phase->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime3, $service->getEarliestInfluence($ranking, $tournament));

    $phase2 = new Phase();
    $phase2->setPhaseNumber(2)->setCompetition($competition);
    $match3 = new Match();
    $match3->setMatchNumber(1)->setPhase($phase2);
    $game4 = new Game();
    $game4->setGameNumber(1)->setMatch($match3);
    $endTime4 = new \DateTime("2017-09-01 00:00:00");
    $game4->setEndTime($endTime4);
    self::assertEquals($endTime3, $service->getEarliestInfluence($ranking, $tournament));

    $competition->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime4, $service->getEarliestInfluence($ranking, $tournament));

    $competition2 = new Competition();
    $competition2->setName("TestCompetition2")->setTournament($tournament);
    $phase3 = new Phase();
    $phase3->setPhaseNumber(1)->setCompetition($competition2);
    $match4 = new Match();
    $match4->setMatchNumber(1)->setPhase($phase3);
    $game5 = new Game();
    $game5->setGameNumber(1)->setMatch($match4);
    $endTime5 = new \DateTime("2017-01-01 00:00:00");
    $game5->setEndTime($endTime5);
    self::assertEquals($endTime4, $service->getEarliestInfluence($ranking, $tournament));

    $game6 = new Game();
    $game6->setGameNumber(2)->setMatch($match4);
    $endTime6 = new \DateTime("2017-10-01 00:00:00");
    $game6->setEndTime($endTime6);
    $game6->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime4, $service->getEarliestInfluence($ranking, $tournament));

    $game7 = new Game();
    $game7->setGameNumber(3)->setMatch($match4);
    $endTime7 = new \DateTime("2017-08-01 00:00:00");
    $game7->setEndTime($endTime7);
    $game7->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($endTime7, $service->getEarliestInfluence($ranking, $tournament));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getEarliestInfluence
   * @covers \App\Service\RankingSystem\RankingSystemService::getEarliestEntityInfluence
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testGetEarliestInfluenceTournamentLevel()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TournamentHierarchyInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    /** @var RankingSystem $ranking */
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)]);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    /** @var RankingSystemService $service */
    $tournament = new Tournament();
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $endTime = new \DateTime("2017-03-01 00:00:00");
    $tournament->setEndTime($endTime);
    self::assertEquals($endTime, $service->getEarliestInfluence($ranking, $tournament));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getAverage
   */
  public function testGetEmptyAverage()
  {
    /** @var $service RankingSystemService */
    $service = $this->getMockForAbstractClass(RankingSystemService::class, [], '', false);

    self::assertEquals(0.0, static::callProtectedMethod($service, 'getAverage', [[]]));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getEntities
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   */
  public function testGetEntities()
  {
    //create mock for input
    $ranking = $this->createMock(RankingSystem::class);

    //create service mock
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
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

    /** @var $service RankingSystemService */
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entityList, static::getMethod(get_class($service), 'getEntities')
      ->invokeArgs($service, [$ranking, new \DateTime("2017-01-01")]));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getEntriesOfPlayers
   * @uses   \App\Service\RankingSystem\RankingSystemService::getOrCreateRankingSystemListEntry
   */
  public function testGetEntriesOfPlayers()
  {

    /** @var $service RankingSystemService */
    $service = $this->getMockForAbstractClass(RankingSystemService::class, [], '', false);

    $entry1 = $this->createMock(RankingSystemListEntry::class);
    $entry2 = $this->createMock(RankingSystemListEntry::class);
    $entry3 = $this->createMock(RankingSystemListEntry::class);

    $entries = new ArrayCollection([1 => $entry1, 2 => $entry2, 3 => $entry3]);
    $list = $this->createStub(RankingSystemList::class, ['getEntries' => $entries]);

    $player1 = $this->createStub(Player::class, ['getPlayerId' => 1]);
    $player3 = $this->createStub(Player::class, ['getPlayerId' => 3]);

    $returnedEntries = static::callProtectedMethod($service, 'getEntriesOfPlayers',
      [new ArrayCollection([$player1, $player3]), $list]);
    self::assertEquals([$entry1, $entry3], $returnedEntries);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getOrCreateChange
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystemChange
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Entity\Helpers\SubClassData::setProperty
   */
  public function testGetOrCreateChangeCreateNewOne()
  {
    $persisted = null;

    [$entity, $ranking, $player] = $this->createEntities();
    [$service, $entityManager] = $this->prepareCreateChange();
    $entityManager->expects(self::once())->method('persist')->willReturnCallback(
      function (RankingSystemChange $change) use (&$persisted, $entity, $ranking, $player) {
        $persisted = $change;
        self::assertInstanceOf(RankingSystemChange::class, $change);
        self::assertEquals($entity, $change->getHierarchyEntity());
        self::assertEquals($ranking, $change->getRankingSystem());
        self::assertEquals($player, $change->getPlayer());
      });
    $service->method('getAdditionalFields')->willReturn(['additional' => 0.0]);

    $change = static::callProtectedMethod($service, 'getOrCreateChange', [$entity, $ranking, $player]);
    self::assertEquals($persisted, $change);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getOrCreateChange
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystemChange
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   */
  public function testGetOrCreateChangeCreateTwice()
  {
    $repository = $this->createMock(ObjectRepository::class);
    $repository->expects(self::once())->method('findBy')->willReturn([]);

    [$entity, $ranking, $player] = $this->createEntities();
    [$service, $entityManager] = $this->prepareCreateChange($repository);
    $entityManager->expects(self::once())->method('persist');

    $change = static::callProtectedMethod($service, 'getOrCreateChange', [$entity, $ranking, $player]);
    $change2 = static::callProtectedMethod($service, 'getOrCreateChange', [$entity, $ranking, $player]);
    self::assertEquals($change, $change2);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getOrCreateChange
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystemChange
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   */
  public function testGetOrCreateChangeFindInRepo()
  {
    [$entity, $ranking, $player] = $this->createEntities();
    $change = $this->createStub(RankingSystemChange::class, ['getRankingSystem' => $ranking, 'getPlayer' => $player]);

    $repository = $this->createStub(ObjectRepository::class, ['findBy' => [$change]]);


    [$service, $entityManager] = $this->prepareCreateChange($repository);
    $entityManager->expects(self::never())->method('persist');

    $foundChange = static::callProtectedMethod($service, 'getOrCreateChange', [$entity, $ranking, $player]);
    self::assertEquals($change, $foundChange);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getOrCreateChange
   * @covers \App\Service\RankingSystem\RankingSystemService::deleteOldChanges
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystemChange::__construct
   * @uses   \App\Entity\RankingSystemChange
   * @uses   \App\Entity\RankingSystemList
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::cloneInto
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEntities
   * @uses   \App\Service\RankingSystem\RankingSystemService::recomputeBasedOn
   * @uses   \App\Service\RankingSystem\RankingSystemService::updateRankingFrom
   */
  public function testGetOrCreateGetDeletedChange()
  {
    [$entity, $ranking, $player] = $this->createEntities();
    $change = $this->createStub(RankingSystemChange::class,
      ['getRankingSystem' => $ranking, 'getPlayer' => $player, 'getHierarchyEntity' => $entity]);

    $entityManager = $this->getEntityManagerMockForQuery([$change], null, ['persist', 'remove', 'getRepository']);
    $entityManager->expects(self::once())->method('persist');
    $service = $this->prepareUpdateRankingFrom($ranking, $entityManager);
    /** @noinspection PhpUnhandledExceptionInspection */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
    $repository = $this->createStub(ObjectRepository::class, ['findBy' => [$change]]);
    $entityManager->method('getRepository')->willReturn($repository);
    $foundChange = static::callProtectedMethod($service, 'getOrCreateChange', [$entity, $ranking, $player]);
    self::assertNotEquals($change, $foundChange);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getOrCreateChange
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystemChange::__construct
   * @uses   \App\Entity\RankingSystemChange
   * @uses   \App\Entity\RankingSystemList
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::cloneInto
   * @uses   \App\Service\RankingSystem\RankingSystemService::deleteOldChanges
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEntities
   * @uses   \App\Service\RankingSystem\RankingSystemService::recomputeBasedOn
   * @uses   \App\Service\RankingSystem\RankingSystemService::updateRankingFrom
   */
  public function testGetOrCreateGetDeletedChangeTwice()
  {
    [$entity, $ranking, $player] = $this->createEntities();
    $change = $this->createStub(RankingSystemChange::class,
      ['getRankingSystem' => $ranking, 'getPlayer' => $player, 'getHierarchyEntity' => $entity]);

    $entityManager = $this->getEntityManagerMockForQuery([$change], null, ['persist', 'remove', 'getRepository']);
    $entityManager->expects(self::once())->method('persist');
    $service = $this->prepareUpdateRankingFrom($ranking, $entityManager);
    /** @noinspection PhpUnhandledExceptionInspection */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
    $repository = $this->createStub(ObjectRepository::class, ['findBy' => [$change]]);
    $entityManager->method('getRepository')->willReturn($repository);
    $foundChange = static::callProtectedMethod($service, 'getOrCreateChange', [$entity, $ranking, $player]);
    self::assertNotEquals($change, $foundChange);
    $foundChange2 = static::callProtectedMethod($service, 'getOrCreateChange', [$entity, $ranking, $player]);
    self::assertEquals($foundChange, $foundChange2);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getOrCreateRankingSystemListEntry
   * @covers \App\Service\RankingSystem\RankingSystemService::startPoints
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystemListEntry
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   */
  public function testGetOrCreateRankingSystemListEntryExistingEntry()
  {
    $player = $this->createStubWithId(Player::class, 1, 'getPlayerId');
    $entries = new ArrayCollection([]);
    $list = $this->createStub(RankingSystemList::class, ['getEntries' => $entries]);
    $entry = $this->createStub(RankingSystemListEntry::class,
      ['getPlayer' => $player, 'getRankingSystemList' => $list]);
    $entries->set(1, $entry);

    /** @var RankingSystemService $service */
    $service = $this->getMockForAbstractClass(RankingSystemService::class, [], '', false);
    $foundEntry = static::callProtectedMethod($service, 'getOrCreateRankingSystemListEntry', [$list, $player]);
    self::assertEquals($entry, $foundEntry);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::getOrCreateRankingSystemListEntry
   * @covers \App\Service\RankingSystem\RankingSystemService::startPoints
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystemListEntry
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Entity\Helpers\SubClassData::setProperty
   */
  public function testGetOrCreateRankingSystemListEntryNewEntry()
  {
    $player = $this->createStubWithId(Player::class, 1, 'getPlayerId');
    $entries = new ArrayCollection([]);
    $list = $this->createStub(RankingSystemList::class, ['getEntries' => $entries]);

    /** @var RankingSystemListEntry $createdEntry */
    $createdEntry = null;
    $entityManager = $this->createMock(EntityManager::class);
    $entityManager->expects(self::once())->method('persist')->willReturnCallback(
      function (RankingSystemListEntry $entry) use (&$createdEntry, $player, $list) {
        $createdEntry = $entry;
      });


    $service = $this->getMockForAbstractClass(RankingSystemService::class, [$entityManager,
      $this->createMock(TimeServiceInterface::class), $this->createMock(EntityComparerInterface::class)]);
    $service->method('getAdditionalFields')->willReturn(['additional' => 0.0]);
    /** @var RankingSystemService $service */

    $entry = static::callProtectedMethod($service, 'getOrCreateRankingSystemListEntry', [$list, $player]);
    self::assertEquals($createdEntry, $entry);
    self::assertInstanceOf(RankingSystemListEntry::class, $entry);
    self::assertEquals($player, $entry->getPlayer());
    self::assertEquals($list, $entry->getRankingSystemList());
    self::assertEquals(1, $entries->count());
    self::assertEquals($entry, $entries[1]);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentOldEarliestIsEarlier()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TournamentHierarchyInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    /** @var RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-02-01 00:00:00");
    $tournament->setUpdatedAt($endedAt);
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $oldInfluence = new \DateTime("2017-01-01 00:00:00");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01 00:00:00"));

    /** @var RankingSystemService $service */
    $service->updateRankingForTournament($ranking, $tournament, $oldInfluence);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentOldEarliestIsNotNullAndTournamentNotRanked()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    /** @var RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($endedAt);
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$this->createMock(EntityManagerInterface::class),
        $this->createMock(TimeServiceInterface::class),
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $oldInfluence = new \DateTime("2017-02-01 00:00:00");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-02-01 00:00:00"));

    /** @var RankingSystemService $service */
    $service->updateRankingForTournament($ranking, $tournament, $oldInfluence);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentOldEarliestIsNull()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    /** @var RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-01-01 00:00:00");
    $tournament->setEndTime($endedAt);
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TournamentHierarchyInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01 00:00:00"));

    /** @var RankingSystemService $service */
    $service->updateRankingForTournament($ranking, $tournament, null);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentOldEarliestIsNullAndTournamentNotRanked()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    /** @var RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($endedAt);
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$this->createMock(EntityManagerInterface::class), $this->createMock(TimeServiceInterface::class),
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $service->expects(self::never())
      ->method('updateRankingFrom');

    /** @var RankingSystemService $service */
    $service->updateRankingForTournament($ranking, $tournament, null);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEarliestInfluence
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::getRankingSystems
   */
  public function testUpdateRankingForTournamentTournamentIsEarlier()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    /** @var RankingSystem $ranking */
    $tournament = new Tournament();
    $endedAt = new \DateTime("2017-01-01");
    $tournament->setEndTime($endedAt);
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TournamentHierarchyInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$this->createMock(EntityManagerInterface::class),
        $timeService,
        $this->createMock(EntityComparerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $oldInfluence = new \DateTime("2017-02-01");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01"));

    /** @var RankingSystemService $service */
    $service->updateRankingForTournament($ranking, $tournament, $oldInfluence);
  }

  //TODO split this up in multiple unit tests!!!

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystemService::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystemService::cloneInto
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEntities
   * @uses   \App\Service\RankingSystem\RankingSystemService::deleteOldChanges
   * @uses   \App\Entity\RankingSystemListEntry
   * @uses   \App\Entity\Helpers\SubClassData::cloneSubClassDataFrom
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Service\RankingSystem\RankingSystemService::getOrCreateRankingSystemListEntry
   * @uses   \App\Entity\Helpers\SubClassData::getProperty
   * @uses   \App\Entity\Helpers\SubClassData::setProperty
   * @uses   \App\Entity\RankingSystemChange
   */
  public function testUpdateRankingFrom()
  {
    //create mock for input
    $ranking = $this->createStubWithId(RankingSystem::class);

    //create mocks for ranking lists
    $list1 = $this->createMock(RankingSystemList::class);
    $list1->method('isCurrent')->willReturn(false);
    $list1->method('getLastEntryTime')->willReturn(new \DateTime("2017-01-01"));

    $entry1 = $this->createEmptyEntry();
    $entry2 = $this->createEmptyEntry();
    $entry3 = $this->createEmptyEntry();

    $list2 = $this->createMock(RankingSystemList::class);
    $list2->method('isCurrent')->willReturn(false);
    $list2->method('getLastEntryTime')->willReturn(new \DateTime("2017-02-01"));
    $list2->method('getEntries')->willReturn(new ArrayCollection([1 => $entry1, 3 => $entry3]));

    $list3 = $this->createMock(RankingSystemList::class);
    $list3->method('isCurrent')->willReturn(false);
    $list3->method('getLastEntryTime')->willReturn(new \DateTime("2017-03-01"));
    $list3->method('getEntries')->willReturn(new ArrayCollection([1 => $entry1, 2 => $entry2]));

    $list4 = $this->createMock(RankingSystemList::class);
    $list4->method('isCurrent')->willReturn(false);
    $list4->method('getLastEntryTime')->willReturn(new \DateTime("2017-04-01"));
    $list4->method('getEntries')->willReturn(new ArrayCollection());

    $list5 = $this->createMock(RankingSystemList::class);
    $list5->method('isCurrent')->willReturn(true);
    $list5->method('getLastEntryTime')->willReturn(new \DateTime("2017-05-01"));
    $list5->method('getEntries')->willReturn(new ArrayCollection());

    $lists = $this->createMock(Collection::class);
    $lists->expects(static::once())->method('toArray')->willReturn([$list1, $list2, $list3, $list4, $list5]);

    //finish mock for input
    $ranking->expects(static::once())->method('getLists')->willReturn($lists);

    //create time service, entity comparer and ranking service mock
    $timeService = $this->createMock(TimeServiceInterface::class);
    $timeService->expects(self::atLeastOnce())->method('clearTimes')->id('clearTimes');
    $timeService->method('getTime')->willReturnCallback(function (TournamentHierarchyInterface $entity) {
      return $entity->getEndTime();
    })->after('clearTimes');
    $entityComparer = $this->createMock(EntityComparerInterface::class);
    $entityComparer->method('compareEntities')->willReturnCallback(
      function (TournamentHierarchyInterface $entity1, TournamentHierarchyInterface $entity2) {
        return $entity1->getEndTime() <=> $entity2->getEndTime();
      });
    $entityManager = $this->getEntityManagerMockForQuery([],
      'SELECT c FROM App\Entity\RankingSystemChange c WHERE c.hierarchyEntity IN(:entities)', ['persist',
        'remove']);
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$entityManager,
        $timeService,
        $this->createMock(EntityComparerInterface::class)]);

    //create entities mocks
    $entity1 = $this->createStubWithId(TournamentHierarchyEntity::class, "e1");
    $entity1->method('getEndTime')->willReturn(new \DateTime("2017-03-01"));

    $entity2 = $this->createStubWithId(TournamentHierarchyEntity::class, "e2");
    $entity2->method('getEndTime')->willReturn(new \DateTime("2017-02-01 00:00:01"));

    $entity3 = $this->createStubWithId(TournamentHierarchyEntity::class, "e3");
    $entity3->method('getEndTime')->willReturn(new \DateTime("2017-05-02"));

    $entity4 = $this->createStubWithId(TournamentHierarchyEntity::class, "e4");
    $entity4->method('getEndTime')->willReturn(new \DateTime("2017-03-02"));

    $parent = $this->createStubWithId(TournamentHierarchyEntity::class, "e4");
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
    $changes = [
      $this->createEmptyChange(),
      $this->createEmptyChange(),
    ];
    $service->method('getChanges')->willReturn($changes);
    $service->method('getAdditionalFields')->willReturn(['additional' => 0.0]);

    /** @var RankingSystemService $service */
    /** @var RankingSystem $ranking */
    /** @noinspection PhpUnhandledExceptionInspection */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystemService::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystemService::cloneInto
   * @uses   \App\Entity\RankingSystemList
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEntities
   * @uses   \App\Service\RankingSystem\RankingSystemService::deleteOldChanges
   */
  public function testUpdateRankingFromCalledTwice()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    $service = $this->prepareUpdateRankingFrom($ranking);

    /** @var RankingSystem $ranking */

    /** @noinspection PhpUnhandledExceptionInspection */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));

    $this->expectException(PreconditionFailedException::class);

    /** @noinspection PhpUnhandledExceptionInspection */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystemService::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystemService::cloneInto
   * @uses   \App\Entity\RankingSystemList
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEntities
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   * @uses   \App\Service\RankingSystem\RankingSystemService::deleteOldChanges
   */
  public function testUpdateRankingFromNoCurrent()
  {
    //create mock for input
    $ranking = $this->createStubWithId(RankingSystem::class, 'r1');

    //create mocks for ranking lists
    $list = $this->createMock(RankingSystemList::class);
    $list->method('isCurrent')->willReturn(false);
    $list->method('getLastEntryTime')->willReturn(new \DateTime("2017-01-01"));
    $list->method('getEntries')->willReturn(new ArrayCollection());

    $lists = $this->createMock(Collection::class);
    $lists->expects(static::once())->method('toArray')->willReturn([$list]);
    $lists->expects(static::once())->method('set')->with('new')->willReturnSelf();

    //finish mock for input
    $ranking->expects(static::exactly(2))->method('getLists')->willReturn($lists);

    //create service mock
    $entityManager = $this->getEntityManagerMockForQuery([],
      'SELECT c FROM App\Entity\RankingSystemChange c WHERE c.hierarchyEntity IN(:entities)', ['persist']);
    $entityManager->expects(static::once())->method('persist')->willReturnCallback(
      function (RankingSystemList $entity) {
        self::assertInstanceOf(RankingSystemList::class, $entity);
        self::assertTrue($entity->isCurrent());
        static::getProperty(get_class($entity), 'id')->setValue($entity, 'new');
      });
    $service = $this->getMockForAbstractClass(RankingSystemService::class, [$entityManager,
      $this->createMock(TimeServiceInterface::class), $this->createMock(EntityComparerInterface::class)]);

    //create query mock for getEntities
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn([]);
    //create query builder mock for getEntities
    $queryBuilder = $this->createMock(QueryBuilder::class);
    $queryBuilder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2017-01-01"))->willReturn($queryBuilder);

    /** @var RankingSystemService $service */
    /** @var RankingSystem $ranking */
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystemService::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystemService::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystemService::cloneInto
   * @uses   \App\Entity\RankingSystemList
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::getEntities
   * @uses   \App\Service\RankingSystem\RankingSystemService::deleteOldChanges
   */
  public function testUpdateRankingFromNoReusable()
  {
    $ranking = $this->createStubWithId(RankingSystem::class);
    $service = $this->prepareUpdateRankingFrom($ranking);

    /** @var RankingSystem $ranking */

    /** @var RankingSystemService $service */
    /** @var RankingSystem $ranking */
    /** @noinspection PhpUnhandledExceptionInspection */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Creates an empty RankingSystemChange
   * @return MockObject|RankingSystemChange
   */
  private function createEmptyChange(): MockObject
  {
    $change = $this->getMockForAbstractClass(RankingSystemChange::class, [['additional']], '', true, true, true,
      ['getPlayer', 'getPointsChange']);
    return $change;
  }

  /**
   * Creates an empty RankingSystemListEntry
   * @return MockObject|RankingSystemListEntry
   */
  private function createEmptyEntry(): MockObject
  {
    $entry = $this->getMockForAbstractClass(RankingSystemListEntry::class, [['additional']], '', true, true, true,
      ['getPlayer', 'getPoints']);
    return $entry;
  }

  /**
   * Creates different entities used for create change
   * @return array, a hierarchy entity, a ranking system and a player
   */
  private function createEntities()
  {
    $entity = $this->createStubWithId(TournamentHierarchyEntity::class, 'h1');
    $ranking = $this->createStubWithId(RankingSystem::class, 'r1');
    $player = $this->createStubWithId(Player::class, 1, 'getPlayerId');
    return [$entity, $ranking, $player];
  }

  /**
   * Prepares a ranking system service for creating a change
   * @param ObjectRepository|null $repository the used repository, if not set a stub which finds the empty list is used
   * @return array, the service entity and its corresponding entity manager
   */
  private function prepareCreateChange(?ObjectRepository $repository = null)
  {
    if ($repository === null) {
      $repository = $this->createStub(ObjectRepository::class, ['findBy' => []]);
    }
    $entityManager = $this->createStub(EntityManager::class, ['getRepository' => $repository]);
    /** @var $service RankingSystemService */
    $service = $this->getMockForAbstractClass(RankingSystemService::class, [
      $entityManager, $this->createMock(TimeServiceInterface::class),
      $this->createMock(EntityComparerInterface::class)
    ]);
    return [$service, $entityManager];
  }

  /**
   * prepares a new ranking system service for update ranking from
   * @param MockObject $ranking the ranking entity
   * @param EntityManagerInterface|null $entityManager the entity manager or null if a mock should be used
   * @return RankingSystemService
   */
  private function prepareUpdateRankingFrom(MockObject $ranking, ?EntityManagerInterface $entityManager = null)
  {
    if ($entityManager === null) {
      $entityManager = $this->getEntityManagerMockForQuery([]);
    }
    $service = $this->getMockForAbstractClass(RankingSystemService::class,
      [$entityManager, $this->createMock(TimeServiceInterface::class),
        $this->createMock(EntityComparerInterface::class)]);

    //create mocks for current lists
    $list = $this->createMock(RankingSystemList::class);
    $list->method('isCurrent')->willReturn(true);
    $list->method('getLastEntryTime')->willReturn(new \DateTime("2017-06-01"));
    $list->method('getEntries')->willReturn(new ArrayCollection());

    $lists = $this->createMock(Collection::class);
    $lists->expects(static::once())->method('toArray')->willReturn([$list]);

    //finish mock for input
    $ranking->method('getLists')->willReturn($lists);

    //create query mock for getEntities
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn([]);
    //create query builder mock for getEntities
    $queryBuilder = $this->createMock(QueryBuilder::class);
    $queryBuilder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2000-01-01"))->willReturn($queryBuilder);
    /** @var $service RankingSystemService */

    return $service;
  }
//</editor-fold desc="Private Methods">
}