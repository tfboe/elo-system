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
use App\Entity\Team;
use App\Entity\Tournament;
use App\Exceptions\ValueNotSet;
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
    self::assertEquals(0, $competition->getTeams()->count());
    self::assertEquals(0, $competition->getPhases()->count());
  }

  public function testId()
  {
    $competition = $this->competition();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($competition);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $competition->getId());
  }

  public function testIdException()
  {
    $competition = $this->competition();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Competition::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $competition->getId();
  }

  public function testName()
  {
    $competition = $this->competition();
    $competition->setName("Name");
    self::assertEquals("Name", $competition->getName());
  }

  public function testNameException()
  {
    $competition = $this->competition();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property name of the class " . Competition::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    $competition->getName();
  }

  public function testTeams()
  {
    $competition = $this->competition();
    $team = new Team();
    $team->setStartNumber(1);
    $competition->getTeams()->set($team->getStartNumber(), $team);
    self::assertEquals(1, $competition->getTeams()->count());
    self::assertEquals($team, $competition->getTeams()[1]);
  }

  public function testPhases()
  {
    $competition = $this->competition();
    $phase = new Phase();
    $phase->setPhaseNumber(1);
    $competition->getPhases()->set($phase->getPhaseNumber(), $phase);
    self::assertEquals(1, $competition->getPhases()->count());
    self::assertEquals($phase, $competition->getPhases()[1]);
  }

  public function testTournament()
  {
    $competition = $this->competition();
    $tournament = new Tournament();
    $competition->setName('test competition');
    $competition->setTournament($tournament);
    self::assertEquals($tournament, $competition->getTournament());
    self::assertEquals(1, $competition->getTournament()->getCompetitions()->count());
    self::assertEquals($competition, $competition->getTournament()->getCompetitions()[$competition->getName()]);
  }

  public function testTournamentException()
  {
    $competition = $this->competition();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property tournament of the class " . Competition::class . " must be set before" .
      " it can be accessed. Please set the property immediately after you call the constructor(Empty Constructor " .
      "Pattern).");
    $competition->getTournament();
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