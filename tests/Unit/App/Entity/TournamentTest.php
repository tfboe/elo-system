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
use Tfboe\FmLib\Entity\Tournament;
use Tfboe\FmLib\Entity\User;
use Tfboe\FmLib\Helpers\Level;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Tfboe\FmLib\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class TournamentTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Tournament::getCompetitions
   * @covers \Tfboe\FmLib\Entity\Tournament::getChildren
   * @uses   \Tfboe\FmLib\Entity\Competition::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::setName
   * @uses   \Tfboe\FmLib\Entity\Tournament::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testCompetitionsAndChildren()
  {
    $tournament = $this->tournament();
    $competition = new Competition();
    $competition->setName('comp name');
    self::assertEquals($tournament->getCompetitions(), $tournament->getChildren());
    $tournament->getCompetitions()->set($competition->getName(), $competition);
    self::assertEquals(1, $tournament->getCompetitions()->count());
    self::assertEquals($competition, $tournament->getCompetitions()[$competition->getName()]);
    self::assertEquals($tournament->getCompetitions(), $tournament->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Tournament::__construct
   * @uses   \Tfboe\FmLib\Entity\Tournament::getCompetitions
   * @uses   \Tfboe\FmLib\Entity\Tournament::getTournamentListId
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testConstructor()
  {
    $tournament = $this->tournament();
    self::assertInstanceOf(Tournament::class, $tournament);
    self::assertInstanceOf(ArrayCollection::class, $tournament->getCompetitions());
    self::assertEquals(0, $tournament->getCompetitions()->count());
    self::assertEquals("", $tournament->getTournamentListId());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Tournament::setCreator
   * @covers \Tfboe\FmLib\Entity\Tournament::getCreator
   * @uses   \Tfboe\FmLib\Entity\Tournament::__construct
   * @uses   \Tfboe\FmLib\Entity\User::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testCreator()
  {
    $tournament = $this->tournament();
    $creator = new User();
    $tournament->setCreator($creator);
    self::assertEquals($creator, $tournament->getCreator());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Tournament::getLocalIdentifier
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
   * @uses   \Tfboe\FmLib\Entity\Tournament::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testGetLocalIdentifier()
  {
    $tournament = $this->tournament();
    /** @noinspection PhpUnhandledExceptionInspection */
    self::getProperty(get_class($tournament), 'id')->setValue($tournament, 'user-id');
    self::assertEquals($tournament->getId(), $tournament->getLocalIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Tournament::getLevel
   * @uses   \Tfboe\FmLib\Entity\Tournament::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::TOURNAMENT, $this->tournament()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Tournament::getParent
   * @uses   \Tfboe\FmLib\Entity\Tournament::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testParent()
  {
    self::assertNull($this->tournament()->getParent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Tournament::setTournamentListId
   * @covers \Tfboe\FmLib\Entity\Tournament::getTournamentListId
   * @uses   \Tfboe\FmLib\Entity\Tournament::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testTournamentListId()
  {
    $tournament = $this->tournament();
    $tournament->setTournamentListId("Changed");
    self::assertEquals("Changed", $tournament->getTournamentListId());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Tournament::setUserIdentifier
   * @covers \Tfboe\FmLib\Entity\Tournament::getUserIdentifier
   * @uses   \Tfboe\FmLib\Entity\Tournament::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testUserIdentifier()
  {
    $tournament = $this->tournament();
    $tournament->setUserIdentifier("UserIdentifier");
    self::assertEquals("UserIdentifier", $tournament->getUserIdentifier());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Tournament a new tournament
   */
  private function tournament(): Tournament
  {
    return new Tournament();
  }
//</editor-fold desc="Private Methods">
}