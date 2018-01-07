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
use App\Service\RankingSystem\RankingSystem;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Tests\Helpers\UnitTestCase;


/**
 * Class RankingSystemTest
 * @package Tests\Unit\App\Service\RankingSystem
 */
class RankingSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testConstruct()
  {
    $em = $this->createMock(EntityManagerInterface::class);
    $system = $this->getMockForAbstractClass(RankingSystem::class, [$em]);
    self::assertInstanceOf(RankingSystem::class, $system);
    self::assertEquals($em, self::getProperty(get_class($system), 'em')->getValue($system));
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
   * @uses   \App\Service\RankingSystem\RankingSystem::getTime
   */
  public function testGetEarliestInfluenceGameLevel()
  {
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)]);
    $service->method("getLevel")->willReturn(Level::GAME);
    /** @var RankingSystem $service */
    $tournament = new Tournament();
    $end_time = new \DateTime("2017-03-01 00:00:00");
    $tournament->setEndTime($end_time);
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
    self::assertEquals($end_time, $service->getEarliestInfluence($ranking, $tournament));

    $phase_end_time = new \DateTime("2017-04-01 00:00:00");
    $phase->setEndTime($phase_end_time);
    self::assertEquals($phase_end_time, $service->getEarliestInfluence($ranking, $tournament));

    $game_end_time = new \DateTime("2017-06-01 00:00:00");
    $game->setEndTime($game_end_time);
    self::assertEquals($game_end_time, $service->getEarliestInfluence($ranking, $tournament));

    $game2 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game2->setGameNumber(2)->setMatch($match);
    $game2_end_time = new \DateTime("2017-05-01 00:00:00");
    $game2->setEndTime($game2_end_time);
    self::assertEquals($game2_end_time, $service->getEarliestInfluence($ranking, $tournament));

    $game3 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game3->setGameNumber(3)->setMatch($match);
    $game3_end_time = new \DateTime("2017-07-01 00:00:00");
    $game3->setEndTime($game3_end_time);
    self::assertEquals($game2_end_time, $service->getEarliestInfluence($ranking, $tournament));
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
   * @uses   \App\Service\RankingSystem\RankingSystem::getTime
   */
  public function testGetEarliestInfluenceGameLevelWithDifferentImpactLevels()
  {
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)]);
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
    $end_time_1 = new \DateTime("2017-12-01 00:00:00");
    $game->setEndTime($end_time_1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($end_time_1, $service->getEarliestInfluence($ranking, $tournament));

    $game2 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game2->setGameNumber(2)->setMatch($match);
    $end_time_2 = new \DateTime("2017-11-01 00:00:00");
    $game2->setEndTime($end_time_2);
    self::assertEquals($end_time_1, $service->getEarliestInfluence($ranking, $tournament));

    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($end_time_2, $service->getEarliestInfluence($ranking, $tournament));

    $match2 = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    $match2->setMatchNumber(2)->setPhase($phase);
    $game3 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game3->setGameNumber(1)->setMatch($match2);
    $end_time_3 = new \DateTime("2017-10-01 00:00:00");
    $game3->setEndTime($end_time_3);
    self::assertEquals($end_time_2, $service->getEarliestInfluence($ranking, $tournament));

    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($end_time_3, $service->getEarliestInfluence($ranking, $tournament));

    $phase2 = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase2->setPhaseNumber(2)->setCompetition($competition);
    $match3 = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    $match3->setMatchNumber(1)->setPhase($phase2);
    $game4 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game4->setGameNumber(1)->setMatch($match3);
    $end_time_4 = new \DateTime("2017-09-01 00:00:00");
    $game4->setEndTime($end_time_4);
    self::assertEquals($end_time_3, $service->getEarliestInfluence($ranking, $tournament));

    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($end_time_4, $service->getEarliestInfluence($ranking, $tournament));

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
    $end_time_5 = new \DateTime("2017-01-01 00:00:00");
    $game5->setEndTime($end_time_5);
    self::assertEquals($end_time_4, $service->getEarliestInfluence($ranking, $tournament));

    $game6 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game6->setGameNumber(2)->setMatch($match4);
    $end_time_6 = new \DateTime("2017-10-01 00:00:00");
    $game6->setEndTime($end_time_6);
    /** @noinspection PhpUnhandledExceptionInspection */
    $game6->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($end_time_4, $service->getEarliestInfluence($ranking, $tournament));

    $game7 = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game7->setGameNumber(3)->setMatch($match4);
    $end_time_7 = new \DateTime("2017-08-01 00:00:00");
    $game7->setEndTime($end_time_7);
    /** @noinspection PhpUnhandledExceptionInspection */
    $game7->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($end_time_7, $service->getEarliestInfluence($ranking, $tournament));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @covers \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getTime
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   */
  public function testGetEarliestInfluenceTournamentLevel()
  {
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)]);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    /** @var RankingSystem $service */
    $tournament = new Tournament();
    $updated_at = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($updated_at);
    self::assertNull($service->getEarliestInfluence($ranking, $tournament));
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    self::assertEquals($updated_at, $service->getEarliestInfluence($ranking, $tournament));
    $start_time = new \DateTime("2017-02-01 00:00:00");
    $tournament->setStartTime($start_time);
    self::assertEquals($start_time, $service->getEarliestInfluence($ranking, $tournament));
    $end_time = new \DateTime("2017-03-01 00:00:00");
    $tournament->setEndTime($end_time);
    self::assertEquals($end_time, $service->getEarliestInfluence($ranking, $tournament));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::getTime
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Game
   * @uses   \App\Entity\Match
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Tournament
   * @uses   \App\Entity\Helpers\NameEntity
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testGetTimeGame()
  {
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-05-01");
    $tournament->setEndTime($ended_at);
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
    /** @var RankingSystem $service */
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)]);
    self::assertEquals($ended_at, self::callProtectedMethod($service, "getTime", [$game]));

    $phase_ended_at = new \DateTime("2017-04-01");
    $phase->setStartTime($phase_ended_at);
    self::assertEquals($phase_ended_at, self::callProtectedMethod($service, "getTime", [$game]));

    $game_started_at = new \DateTime("2017-02-01");
    $phase->setEndTime($game_started_at);
    self::assertEquals($game_started_at, self::callProtectedMethod($service, "getTime", [$game]));

    $game_ended_at = new \DateTime("2017-03-01");
    $phase->setEndTime($game_ended_at);
    self::assertEquals($game_ended_at, self::callProtectedMethod($service, "getTime", [$game]));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::getTime
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Tournament
   * @uses   \App\Entity\Helpers\NameEntity
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testGetTimePhase()
  {
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-04-01");
    $tournament->setEndTime($ended_at);
    $competition = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->setPhaseNumber(1)->setCompetition($competition);
    /** @var RankingSystem $service */
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)]);
    self::assertEquals($ended_at, self::callProtectedMethod($service, "getTime", [$phase]));

    $started_at = new \DateTime("2017-02-01");
    $phase->setStartTime($started_at);
    self::assertEquals($started_at, self::callProtectedMethod($service, "getTime", [$phase]));

    $ended_at = new \DateTime("2017-03-01");
    $phase->setEndTime($ended_at);
    self::assertEquals($ended_at, self::callProtectedMethod($service, "getTime", [$phase]));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::getTime
   * @uses   \App\Entity\Tournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testGetTimeTournament()
  {
    $tournament = new Tournament();
    $updated_at = new \DateTime("2017-04-01");
    $tournament->setUpdatedAt($updated_at);
    /** @var RankingSystem $service */
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)]);
    self::assertEquals($updated_at, self::callProtectedMethod($service, "getTime", [$tournament]));

    $started_at = new \DateTime("2017-02-01");
    $tournament->setStartTime($started_at);
    self::assertEquals($started_at, self::callProtectedMethod($service, "getTime", [$tournament]));

    $ended_at = new \DateTime("2017-03-01");
    $tournament->setEndTime($ended_at);
    self::assertEquals($ended_at, self::callProtectedMethod($service, "getTime", [$tournament]));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getTime
   */
  public function testUpdateRankingForTournamentOldEarliestIsEarlier()
  {
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-02-01 00:00:00");
    $tournament->setUpdatedAt($ended_at);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $old_influence = new \DateTime("2017-01-01 00:00:00");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01 00:00:00"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, $old_influence);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   */
  public function testUpdateRankingForTournamentOldEarliestIsNotNullAndTournamentNotRanked()
  {
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($ended_at);
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $old_influence = new \DateTime("2017-02-01 00:00:00");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-02-01 00:00:00"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, $old_influence);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingForTournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Tournament
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestEntityInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getEarliestInfluence
   * @uses   \App\Service\RankingSystem\RankingSystem::getTime
   */
  public function testUpdateRankingForTournamentOldEarliestIsNull()
  {
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($ended_at);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)], '', true, true, true, ['updateRankingFrom']);
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
   */
  public function testUpdateRankingForTournamentOldEarliestIsNullAndTournamentNotRanked()
  {
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($ended_at);
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)], '', true, true, true, ['updateRankingFrom']);
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
   * @uses   \App\Service\RankingSystem\RankingSystem::getTime
   */
  public function testUpdateRankingForTournamentTournamentIsEarlier()
  {
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);
    /** @var \App\Entity\RankingSystem $ranking */
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-01-01");
    $tournament->setUpdatedAt($ended_at);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)], '', true, true, true, ['updateRankingFrom']);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $old_influence = new \DateTime("2017-02-01");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, $old_influence);
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystem::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystem::cloneInto
   * @covers \App\Service\RankingSystem\RankingSystem::sortEntities
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEntities
   * @uses   \App\Service\RankingSystem\RankingSystem::compareEntities
   * @uses   \App\Service\RankingSystem\RankingSystem::getTime
   */
  public function testUpdateRankingFrom()
  {
    //create mock for input
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);

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

    //create service mock
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)]);

    //create entities mocks
    $entity1 = $this->createMockWithId(TreeStructureEntityInterface::class, "e1");
    $entity1->method('getEndTime')->willReturn(new \DateTime("2017-03-01"));

    $entity2 = $this->createMockWithId(TreeStructureEntityInterface::class, "e2");
    $entity2->method('getEndTime')->willReturn(new \DateTime("2017-02-01 00:00:01"));

    $entity3 = $this->createMockWithId(TreeStructureEntityInterface::class, "e3");
    $entity3->method('getEndTime')->willReturn(new \DateTime("2017-05-01"));

    $entity4 = $this->createMockWithId(TreeStructureEntityInterface::class, "e4");
    $entity4->method('getEndTime')->willReturn(new \DateTime("2017-03-02"));

    $parent = $this->createMockWithId(TreeStructureEntityInterface::class, "e4");
    $parent->method('getEndTime')->willReturn(new \DateTime("2017-12-02"));
    $entity4->method('getParent')->willReturn($parent);

    //create query mock for getEntities
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn([$entity1, $entity2, $entity3, $entity4]);
    //create query builder mock for getEntities
    $query_builder = $this->createMock(QueryBuilder::class);
    $query_builder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2017-02-01"))->willReturn($query_builder);

    /** @var RankingSystem $service */
    /** @var \App\Entity\RankingSystem $ranking */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystem::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystem::cloneInto
   * @covers \App\Service\RankingSystem\RankingSystem::sortEntities
   * @uses   \App\Entity\RankingSystemList
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEntities
   */
  public function testUpdateRankingFromNoReusable()
  {
    //create mock for input
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class);

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
      [$this->createMock(EntityManagerInterface::class)]);

    //create query mock for getEntities
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn([]);
    //create query builder mock for getEntities
    $query_builder = $this->createMock(QueryBuilder::class);
    $query_builder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2000-01-01"))->willReturn($query_builder);

    /** @var RankingSystem $service */
    /** @var \App\Entity\RankingSystem $ranking */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::updateRankingFrom
   * @covers \App\Service\RankingSystem\RankingSystem::recomputeBasedOn
   * @covers \App\Service\RankingSystem\RankingSystem::cloneInto
   * @covers \App\Service\RankingSystem\RankingSystem::sortEntities
   * @uses   \App\Entity\RankingSystemList
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   * @uses   \App\Service\RankingSystem\RankingSystem::getEntities
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testUpdateRankingFromNoCurrent()
  {
    //create mock for input
    $ranking = $this->createMockWithId(\App\Entity\RankingSystem::class, 'r1');

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
    $em = $this->createMock(EntityManagerInterface::class);
    $em->expects(static::once())->method('persist')->willReturnCallback(function (RankingSystemList $entity) {
      self::assertInstanceOf(RankingSystemList::class, $entity);
      self::assertTrue($entity->isCurrent());
      static::getProperty(get_class($entity), 'id')->setValue($entity, 'new');
    });
    $service = $this->getMockForAbstractClass(RankingSystem::class, [$em]);

    //create query mock for getEntities
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn([]);
    //create query builder mock for getEntities
    $query_builder = $this->createMock(QueryBuilder::class);
    $query_builder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2017-01-01"))->willReturn($query_builder);

    /** @var RankingSystem $service */
    /** @var \App\Entity\RankingSystem $ranking */
    $service->updateRankingFrom($ranking, new \DateTime('2017-02-28'));
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
      [$this->createMock(EntityManagerInterface::class)]);

    //create mock for queryBuilder
    $entity_list = ['e1', 'e2'];
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn($entity_list);
    //create query builder mock for getEntities
    $query_builder = $this->createMock(QueryBuilder::class);
    $query_builder->expects(static::once())->method('getQuery')->willReturn($query);
    $service->expects(static::once())->method('getEntitiesQueryBuilder')
      ->with($ranking, new \DateTime("2017-01-01"))->willReturn($query_builder);

    /** @var $service RankingSystem */
    self::assertEquals($entity_list, static::getMethod(get_class($service), 'getEntities')
      ->invokeArgs($service, [$ranking, new \DateTime("2017-01-01")]));
  }

  /**
   * @covers \App\Service\RankingSystem\RankingSystem::compareEntities
   * @covers \App\Service\RankingSystem\RankingSystem::getPredecessors
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testCompareEntities()
  {
    //create service mock
    $service = $this->getMockForAbstractClass(RankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)]);

    //create entities
    $e1 = $this->createMockWithId(TreeStructureEntityInterface::class, 'e1');
    $e2 = $this->createMockWithId(TreeStructureEntityInterface::class, 'e2');

    $method = static::getMethod(get_class($service), 'compareEntities');
    self::assertEquals(-1, $method->invokeArgs($service, [$e1, $e2,
      ['e1' => new \DateTime("2017-01-01"), 'e2' => new \DateTime("2017-01-02")]]));

    self::assertEquals(1, $method->invokeArgs($service, [$e1, $e2,
      ['e1' => new \DateTime("2017-02-01"), 'e2' => new \DateTime("2017-01-02")]]));

    $e1->method('getStartTime')->willReturn(new \DateTime("2017-03-01"));
    $e2->method('getStartTime')->willReturn(new \DateTime("2017-04-01"));
    self::assertEquals(-1, $method->invokeArgs($service, [$e1, $e2,
      ['e1' => new \DateTime("2017-01-01"), 'e2' => new \DateTime("2017-01-01")]]));

    self::assertEquals(1, $method->invokeArgs($service, [$e2, $e1,
      ['e1' => new \DateTime("2017-01-01"), 'e2' => new \DateTime("2017-01-01")]]));

    $e3 = $this->createMockWithId(TreeStructureEntityInterface::class, 'e3');
    $e3->method('getStartTime')->willReturn(new \DateTime("2017-03-01"));
    $p1 = $this->createMockWithId(TreeStructureEntityInterface::class, 'p1');
    $p2 = $this->createMockWithId(TreeStructureEntityInterface::class, 'p2');
    $e1->method('getParent')->willReturn($p1);
    $e3->method('getParent')->willReturn($p2);
    self::assertEquals(1, $method->invokeArgs($service, [$e3, $e1,
      [
        'e1' => new \DateTime("2017-01-03"),
        'e3' => new \DateTime("2017-01-03"),
        'p1' => new \DateTime("2017-01-01"),
        'p2' => new \DateTime("2017-01-02"),
      ]]));

    $e1->method('getLocalIdentifier')->willReturn(1);
    $e3->method('getLocalIdentifier')->willReturn(2);
    $p1->method('getLocalIdentifier')->willReturn(3);
    $p2->method('getLocalIdentifier')->willReturn(3);
    self::assertEquals(1, $method->invokeArgs($service, [$e3, $e1,
      [
        'e1' => new \DateTime("2017-01-03"),
        'e3' => new \DateTime("2017-01-03"),
        'p1' => new \DateTime("2017-01-02"),
        'p2' => new \DateTime("2017-01-02"),
      ]]));

    $gp1 = $this->createMockWithId(TreeStructureEntityInterface::class, 'gp1');
    $gp2 = $this->createMockWithId(TreeStructureEntityInterface::class, 'gp2');
    $p1->method('getParent')->willReturn($gp1);
    $p2->method('getParent')->willReturn($gp2);
    $gp1->method('getLocalIdentifier')->willReturn(2);
    $gp2->method('getLocalIdentifier')->willReturn(1);
    self::assertEquals(-1, $method->invokeArgs($service, [$e3, $e1,
      [
        'e1' => new \DateTime("2017-01-03"),
        'e3' => new \DateTime("2017-01-03"),
        'p1' => new \DateTime("2017-01-02"),
        'p2' => new \DateTime("2017-01-02"),
        'gp1' => new \DateTime("2017-01-01"),
        'gp2' => new \DateTime("2017-01-01"),
      ]]));

    self::assertEquals(0, $method->invokeArgs($service, [$e1, $e1,
      [
        'e1' => new \DateTime("2017-01-03"),
        'p1' => new \DateTime("2017-01-02"),
        'p2' => new \DateTime("2017-01-02"),
      ]]));
  }
//</editor-fold desc="Public Methods">
}