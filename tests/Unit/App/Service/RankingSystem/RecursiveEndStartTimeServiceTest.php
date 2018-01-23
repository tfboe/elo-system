<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/9/18
 * Time: 1:56 PM
 */

namespace Tests\Unit\App\Service\RankingSystem;


use App\Entity\Competition;
use App\Entity\Game;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\Tournament;
use App\Service\RankingSystem\RecursiveEndStartTimeService;
use Tests\Helpers\UnitTestCase;


/**
 * Class RecursiveEndStartTimeServiceTest
 * @package Tests\Unit\App\Service\RankingSystemListService
 */
class RecursiveEndStartTimeServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Service\RankingSystem\RecursiveEndStartTimeService::clearTimes
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   * @uses   \App\Entity\Tournament::__construct
   * @uses   \App\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testClearTimes()
  {
    $tournament = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Tournament::class, 'id')->setValue($tournament, 't1');
    $service = new RecursiveEndStartTimeService();
    $endedAt = new \DateTime("2017-03-01");
    $tournament->setEndTime($endedAt);
    self::assertEquals($endedAt, $service->getTime($tournament));

    $newEndedAt = new \DateTime("2017-06-01");
    $tournament->setEndTime($newEndedAt);
    self::assertEquals($endedAt, $service->getTime($tournament));

    $service->clearTimes();
    self::assertEquals($newEndedAt, $service->getTime($tournament));
  }

  /**
   * @covers \App\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Game
   * @uses   \App\Entity\Match
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Tournament
   * @uses   \App\Entity\Helpers\NameEntity
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   * @uses   \App\Service\RankingSystem\RecursiveEndStartTimeService::clearTimes
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testGetTimeGame()
  {
    $tournament = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Tournament::class, 'id')->setValue($tournament, 't1');
    $endedAt = new \DateTime("2017-05-01");
    $tournament->setEndTime($endedAt);
    $competition = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Competition::class, 'id')->setValue($competition, 'c1');
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Phase::class, 'id')->setValue($phase, 'p1');
    $phase->setPhaseNumber(1)->setCompetition($competition);
    $match = new Match();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Match::class, 'id')->setValue($match, 'm1');
    $match->setMatchNumber(1)->setPhase($phase);
    $game = new Game();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Game::class, 'id')->setValue($game, 'g1');
    $game->setGameNumber(1)->setMatch($match);
    $service = new RecursiveEndStartTimeService();
    self::assertEquals($endedAt, $service->getTime($game));

    $service->clearTimes();
    $phaseEndedAt = new \DateTime("2017-04-01");
    $phase->setStartTime($phaseEndedAt);
    self::assertEquals($phaseEndedAt, $service->getTime($game));

    $service->clearTimes();
    $gameStartedAt = new \DateTime("2017-02-01");
    $phase->setEndTime($gameStartedAt);
    self::assertEquals($gameStartedAt, $service->getTime($game));

    $service->clearTimes();
    $gameEndedAt = new \DateTime("2017-03-01");
    $phase->setEndTime($gameEndedAt);
    self::assertEquals($gameEndedAt, $service->getTime($game));
  }

  /**
   * @covers \App\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Tournament
   * @uses   \App\Entity\Helpers\NameEntity
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   * @uses   \App\Service\RankingSystem\RecursiveEndStartTimeService::clearTimes
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testGetTimePhase()
  {
    $tournament = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Tournament::class, 'id')->setValue($tournament, 't1');
    $endedAt = new \DateTime("2017-04-01");
    $tournament->setEndTime($endedAt);
    $competition = new Competition();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Competition::class, 'id')->setValue($competition, 'c1');
    $competition->setName("TestCompetition")->setTournament($tournament);
    $phase = new Phase();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Phase::class, 'id')->setValue($phase, 'p1');
    $phase->setPhaseNumber(1)->setCompetition($competition);
    $service = new RecursiveEndStartTimeService();
    self::assertEquals($endedAt, $service->getTime($phase));

    $service->clearTimes();
    $startedAt = new \DateTime("2017-02-01");
    $phase->setStartTime($startedAt);
    self::assertEquals($startedAt, $service->getTime($phase));

    $service->clearTimes();
    $endedAt = new \DateTime("2017-03-01");
    $phase->setEndTime($endedAt);
    self::assertEquals($endedAt, $service->getTime($phase));
  }

  /**
   * @covers \App\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses   \App\Entity\Tournament
   * @uses   \App\Entity\Helpers\TimeEntity
   * @uses   \App\Entity\Helpers\TimestampableEntity
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   * @uses   \App\Service\RankingSystem\RecursiveEndStartTimeService::clearTimes
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testGetTimeTournament()
  {
    $tournament = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    static::getProperty(Tournament::class, 'id')->setValue($tournament, 't1');
    $updatedAt = new \DateTime("2017-04-01");
    $tournament->setUpdatedAt($updatedAt);
    $service = new RecursiveEndStartTimeService();
    self::assertEquals($updatedAt, $service->getTime($tournament));

    $service->clearTimes();
    $startedAt = new \DateTime("2017-02-01");
    $tournament->setStartTime($startedAt);
    self::assertEquals($startedAt, $service->getTime($tournament));

    $service->clearTimes();
    $endedAt = new \DateTime("2017-03-01");
    $tournament->setEndTime($endedAt);
    self::assertEquals($endedAt, $service->getTime($tournament));
  }
//</editor-fold desc="Public Methods">
}