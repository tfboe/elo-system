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
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\Tournament;
use App\Helpers\Level;
use App\Service\RankingSystem\RankingSystem;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;


/**
 * Class RankingSystemTest
 * @package Tests\Unit\App\Service\RankingSystem
 */
class RankingSystemTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testGetEarliestInfluenceGameLevel()
  {
    $ranking = new \App\Entity\RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking);
    $service = $this->getMockForAbstractClass(RankingSystem::class);
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

  public function testGetEarliestInfluenceGameLevelWithDifferentImpactLevels()
  {
    $ranking = new \App\Entity\RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking);
    $service = $this->getMockForAbstractClass(RankingSystem::class);
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

  public function testGetEarliestInfluenceTournamentLevel()
  {
    $ranking = new \App\Entity\RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking);
    $service = $this->getMockForAbstractClass(RankingSystem::class);
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
    $service = $this->getMockForAbstractClass(RankingSystem::class);
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
    $service = $this->getMockForAbstractClass(RankingSystem::class);
    self::assertEquals($ended_at, self::callProtectedMethod($service, "getTime", [$phase]));

    $started_at = new \DateTime("2017-02-01");
    $phase->setStartTime($started_at);
    self::assertEquals($started_at, self::callProtectedMethod($service, "getTime", [$phase]));

    $ended_at = new \DateTime("2017-03-01");
    $phase->setEndTime($ended_at);
    self::assertEquals($ended_at, self::callProtectedMethod($service, "getTime", [$phase]));
  }

  public function testGetTimeTournament()
  {
    $tournament = new Tournament();
    $updated_at = new \DateTime("2017-04-01");
    $tournament->setUpdatedAt($updated_at);
    /** @var RankingSystem $service */
    $service = $this->getMockForAbstractClass(RankingSystem::class);
    self::assertEquals($updated_at, self::callProtectedMethod($service, "getTime", [$tournament]));

    $started_at = new \DateTime("2017-02-01");
    $tournament->setStartTime($started_at);
    self::assertEquals($started_at, self::callProtectedMethod($service, "getTime", [$tournament]));

    $ended_at = new \DateTime("2017-03-01");
    $tournament->setEndTime($ended_at);
    self::assertEquals($ended_at, self::callProtectedMethod($service, "getTime", [$tournament]));
  }

  public function testUpdateRankingForTournamentOldEarliestIsEarlier()
  {
    $ranking = new \App\Entity\RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking);
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-02-01 00:00:00");
    $tournament->setUpdatedAt($ended_at);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $service = $this->getMockForAbstractClass(RankingSystem::class);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $old_influence = new \DateTime("2017-01-01 00:00:00");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01 00:00:00"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, $old_influence);
  }

  public function testUpdateRankingForTournamentOldEarliestIsNotNullAndTournamentNotRanked()
  {
    $ranking = new \App\Entity\RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking);
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($ended_at);
    $service = $this->getMockForAbstractClass(RankingSystem::class);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $old_influence = new \DateTime("2017-02-01 00:00:00");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-02-01 00:00:00"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, $old_influence);
  }

  public function testUpdateRankingForTournamentOldEarliestIsNull()
  {
    $ranking = new \App\Entity\RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking);
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($ended_at);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $service = $this->getMockForAbstractClass(RankingSystem::class);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01 00:00:00"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, null);
  }

  public function testUpdateRankingForTournamentOldEarliestIsNullAndTournamentNotRanked()
  {
    $ranking = new \App\Entity\RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking);
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-01-01 00:00:00");
    $tournament->setUpdatedAt($ended_at);
    $service = $this->getMockForAbstractClass(RankingSystem::class);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $service->expects(self::never())
      ->method('updateRankingFrom');

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, null);
  }

  public function testUpdateRankingForTournamentTournamentIsEarlier()
  {
    $ranking = new \App\Entity\RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking);
    $tournament = new Tournament();
    $ended_at = new \DateTime("2017-01-01");
    $tournament->setUpdatedAt($ended_at);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($ranking->getId(), $ranking);
    $service = $this->getMockForAbstractClass(RankingSystem::class);
    $service->method("getLevel")->willReturn(Level::TOURNAMENT);
    $old_influence = new \DateTime("2017-02-01");
    $service->expects(static::once())
      ->method('updateRankingFrom')
      ->with($ranking, new \DateTime("2017-01-01"));

    /** @var RankingSystem $service */
    $service->updateRankingForTournament($ranking, $tournament, $old_influence);
  }
//</editor-fold desc="Public Methods">
}