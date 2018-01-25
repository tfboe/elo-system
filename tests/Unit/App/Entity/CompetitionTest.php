<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Competition;
use Tfboe\FmLib\Entity\Phase;
use Tfboe\FmLib\Entity\Team;
use Tfboe\FmLib\Entity\Tournament;
use Tfboe\FmLib\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Tfboe\FmLib\Entity
 */
class CompetitionTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Competition::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Competition::getPhases
   * @uses   \Tfboe\FmLib\Entity\Competition::getTeams
   */
  public function testConstructor()
  {
    $competition = $this->competition();
    self::assertInstanceOf(Competition::class, $competition);
    self::assertInstanceOf(Collection::class, $competition->getTeams());
    self::assertInstanceOf(Collection::class, $competition->getPhases());
    self::assertEquals(0, $competition->getTeams()->count());
    self::assertEquals(0, $competition->getPhases()->count());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Competition::getLocalIdentifier
   * @uses   \Tfboe\FmLib\Entity\Competition::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::setName
   */
  public function testGetLocalIdentifier()
  {
    $entity = $this->competition();
    $entity->setName("Name");
    self::assertEquals($entity->getName(), $entity->getLocalIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Competition::getLevel()
   * @uses   \Tfboe\FmLib\Entity\Competition::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::COMPETITION, $this->competition()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Competition::getPhases
   * @covers \Tfboe\FmLib\Entity\Competition::getChildren
   * @uses   \Tfboe\FmLib\Entity\Competition::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Phase
   */
  public function testPhasesAndChildren()
  {
    $competition = $this->competition();
    $phase = new Phase();
    $phase->setPhaseNumber(1);
    self::assertEquals($competition->getPhases(), $competition->getChildren());
    $competition->getPhases()->set($phase->getPhaseNumber(), $phase);
    self::assertEquals(1, $competition->getPhases()->count());
    self::assertEquals($phase, $competition->getPhases()[1]);
    self::assertEquals($competition->getPhases(), $competition->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Competition::getTeams
   * @uses   \Tfboe\FmLib\Entity\Competition::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Team
   */
  public function testTeams()
  {
    $competition = $this->competition();
    $team = new Team();
    $team->setStartNumber(1);
    $competition->getTeams()->set($team->getStartNumber(), $team);
    self::assertEquals(1, $competition->getTeams()->count());
    self::assertEquals($team, $competition->getTeams()[1]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Competition::setTournament()
   * @covers \Tfboe\FmLib\Entity\Competition::getTournament()
   * @covers \Tfboe\FmLib\Entity\Competition::getParent()
   * @uses   \Tfboe\FmLib\Entity\Competition::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::setName
   * @uses   \Tfboe\FmLib\Entity\Tournament
   */
  public function testTournamentAndParent()
  {
    $competition = $this->competition();
    $tournament = new Tournament();
    $competition->setName('test competition');

    $competition->setTournament($tournament);
    self::assertEquals($tournament, $competition->getTournament());
    self::assertEquals($competition->getTournament(), $competition->getParent());
    self::assertEquals(1, $competition->getTournament()->getCompetitions()->count());
    self::assertEquals($competition, $competition->getTournament()->getCompetitions()[$competition->getName()]);

    $tournament2 = new Tournament();
    $competition->setTournament($tournament2);

    self::assertEquals($tournament2, $competition->getTournament());
    self::assertEquals($competition->getTournament(), $competition->getParent());
    self::assertEquals(1, $competition->getTournament()->getCompetitions()->count());
    self::assertEquals(0, $tournament->getCompetitions()->count());
    self::assertEquals($competition, $competition->getTournament()->getCompetitions()[$competition->getName()]);
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