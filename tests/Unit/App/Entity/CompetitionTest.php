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
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class CompetitionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Competition::__construct
   * @uses   \App\Entity\Competition::getPhases
   * @uses   \App\Entity\Competition::getRankingSystems
   * @uses   \App\Entity\Competition::getTeams
   */
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

  /**
   * @covers \App\Entity\Competition::getTeams
   * @uses   \App\Entity\Competition::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Team
   */
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

  /**
   * @covers \App\Entity\Competition::getPhases
   * @covers \App\Entity\Competition::getChildren
   * @uses   \App\Entity\Competition::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase
   */
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

  /**
   * @covers \App\Entity\Competition::getLocalIdentifier
   * @uses   \App\Entity\Competition::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Helpers\NameEntity::getName
   * @uses   \App\Entity\Helpers\NameEntity::setName
   */
  public function testGetLocalIdentifier()
  {
    $e = $this->competition();
    $e->setName("Name");
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e->getName(), $e->getLocalIdentifier());
  }

  /**
   * @covers \App\Entity\Competition::getLocalIdentifier
   * @uses   \App\Entity\Competition::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Helpers\NameEntity::getName
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testGetLocalIdentifierException()
  {
    $e = $this->competition();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property name of the class " . get_class($e) . " must be set before it can" .
      " be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getLocalIdentifier();
  }

  /**
   * @covers \App\Entity\Competition::setTournament()
   * @covers \App\Entity\Competition::getTournament()
   * @covers \App\Entity\Competition::getParent()
   * @uses   \App\Entity\Competition::__construct
   * @uses   \App\Entity\Helpers\NameEntity::getName
   * @uses   \App\Entity\Helpers\NameEntity::setName
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Tournament
   */
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

  /**
   * @covers \App\Entity\Competition::getTournament()
   * @uses   \App\Entity\Competition::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
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

  /**
   * @covers \App\Entity\Competition::getRankingSystems()
   * @uses   \App\Entity\Competition::__construct
   */
  public function testRankingSystems()
  {
    $e = $this->competition();
    /** @var $system RankingSystem */
    $system = $this->createMockWithId(RankingSystem::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getRankingSystems()->set($system->getId(), $system);
    self::assertEquals(1, $e->getRankingSystems()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($system, $e->getRankingSystems()[$system->getId()]);
  }

  /**
   * @covers \App\Entity\Competition::getLevel()
   * @uses   \App\Entity\Competition::__construct
   */
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