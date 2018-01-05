<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Competition;
use App\Entity\Phase;
use App\Entity\RankingSystem;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Exceptions\ValueNotSet;
use App\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class CompetitionTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $competition = $this->competition();
    self::assertInstanceOf(Competition::class, $competition);
    self::assertInstanceOf(Collection::class, $competition->getTeams());
    self::assertInstanceOf(Collection::class, $competition->getPhases());
    self::assertInstanceOf(Collection::class, $competition->getRankingSystems());
    self::assertEquals(0, $competition->getTeams()->count());
    self::assertEquals(0, $competition->getPhases()->count());
    self::assertEquals(0, $competition->getRankingSystems()->count());
  }

  public function testTeams()
  {
    $competition = $this->competition();
    $team = new Team();
    $team->setStartNumber(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->getTeams()->set($team->getStartNumber(), $team);
    self::assertEquals(1, $competition->getTeams()->count());
    self::assertEquals($team, $competition->getTeams()[1]);
  }

  public function testPhasesAndChildren()
  {
    $competition = $this->competition();
    $phase = new Phase();
    $phase->setPhaseNumber(1);
    self::assertEquals($competition->getPhases(), $competition->getChildren());
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->getPhases()->set($phase->getPhaseNumber(), $phase);
    self::assertEquals(1, $competition->getPhases()->count());
    self::assertEquals($phase, $competition->getPhases()[1]);
    self::assertEquals($competition->getPhases(), $competition->getChildren());
  }

  public function testTournamentAndParent()
  {
    $competition = $this->competition();
    $tournament = new Tournament();
    $competition->setName('test competition');

    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->setTournament($tournament);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($tournament, $competition->getTournament());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($competition->getTournament(), $competition->getParent());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $competition->getTournament()->getCompetitions()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($competition, $competition->getTournament()->getCompetitions()[$competition->getName()]);

    $tournament2 = new Tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->setTournament($tournament2);

    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($tournament2, $competition->getTournament());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($competition->getTournament(), $competition->getParent());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $competition->getTournament()->getCompetitions()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(0, $tournament->getCompetitions()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($competition, $competition->getTournament()->getCompetitions()[$competition->getName()]);
  }

  public function testTournamentException()
  {
    $competition = $this->competition();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property tournament of the class " . Competition::class . " must be set before" .
      " it can be accessed. Please set the property immediately after you call the constructor(Empty Constructor " .
      "Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $competition->getTournament();
  }

  public function testRankingSystems()
  {
    $e = $this->competition();
    $system = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($system);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getRankingSystems()->set($system->getId(), $system);
    self::assertEquals(1, $e->getRankingSystems()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($system, $e->getRankingSystems()[$system->getId()]);
  }

  public function testLevel()
  {
    self::assertEquals(Level::COMPETITION, $this->competition()->getLevel());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Competition a new competition
   */
  private function competition(): Competition
  {
    return new Competition();
  }
//</editor-fold desc="Private Methods">
}