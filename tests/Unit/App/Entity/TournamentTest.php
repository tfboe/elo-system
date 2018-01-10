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
use App\Entity\RankingSystem;
use App\Entity\Tournament;
use App\Entity\User;
use App\Exceptions\ValueNotSet;
use App\Helpers\Level;
use Doctrine\Common\Collections\ArrayCollection;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class TournamentTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Tournament::getCompetitions
   * @covers \App\Entity\Tournament::getChildren
   * @uses   \App\Entity\Competition::__construct
   * @uses   \App\Entity\Helpers\NameEntity::getName
   * @uses   \App\Entity\Helpers\NameEntity::setName
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Tournament::__construct
   */
  public function testCompetitionsAndChildren()
  {
    $tournament = $this->tournament();
    $competition = new Competition();
    $competition->setName('comp name');
    self::assertEquals($tournament->getCompetitions(), $tournament->getChildren());
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getCompetitions()->set($competition->getName(), $competition);
    self::assertEquals(1, $tournament->getCompetitions()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($competition, $tournament->getCompetitions()[$competition->getName()]);
    self::assertEquals($tournament->getCompetitions(), $tournament->getChildren());
  }

  /**
   * @covers \App\Entity\Tournament::__construct
   * @uses   \App\Entity\Tournament::getCompetitions
   * @uses   \App\Entity\Tournament::getRankingSystems
   * @uses   \App\Entity\Tournament::getTournamentListId
   */
  public function testConstructor()
  {
    $tournament = $this->tournament();
    self::assertInstanceOf(Tournament::class, $tournament);
    self::assertInstanceOf(ArrayCollection::class, $tournament->getCompetitions());
    self::assertInstanceOf(ArrayCollection::class, $tournament->getRankingSystems());
    self::assertEquals(0, $tournament->getCompetitions()->count());
    self::assertEquals("", $tournament->getTournamentListId());
    self::assertEquals(0, $tournament->getRankingSystems()->count());
  }

  /**
   * @covers \App\Entity\Tournament::setCreator
   * @covers \App\Entity\Tournament::getCreator
   * @uses   \App\Entity\Tournament::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\User::__construct
   */
  public function testCreator()
  {
    $tournament = $this->tournament();
    $creator = new User();
    $tournament->setCreator($creator);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($creator, $tournament->getCreator());
  }

  /**
   * @covers \App\Entity\Tournament::getCreator
   * @uses   \App\Entity\Tournament::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testCreatorException()
  {
    $tournament = $this->tournament();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property creator of the class " . Tournament::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor" .
      "(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getCreator();
  }

  /**
   * @covers \App\Entity\Tournament::getLocalIdentifier
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Tournament::__construct
   */
  public function testGetLocalIdentifier()
  {
    $tournament = $this->tournament();
    self::getProperty(get_class($tournament), 'id')->setValue($tournament, 'user-id');
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($tournament->getId(), $tournament->getLocalIdentifier());
  }

  /**
   * @covers \App\Entity\Tournament::getLocalIdentifier
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Tournament::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testGetLocalIdentifierException()
  {
    $tournament = $this->tournament();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . get_class($tournament) . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the " .
      "constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getLocalIdentifier();
  }

  /**
   * @covers \App\Entity\Tournament::getLevel
   * @uses   \App\Entity\Tournament::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::TOURNAMENT, $this->tournament()->getLevel());
  }

  /**
   * @covers \App\Entity\Tournament::getParent
   * @uses   \App\Entity\Tournament::__construct
   */
  public function testParent()
  {
    self::assertNull($this->tournament()->getParent());
  }

  /**
   * @covers \App\Entity\Tournament::getRankingSystems()
   * @uses   \App\Entity\Tournament::__construct
   */
  public function testRankingSystems()
  {
    $tournament = $this->tournament();
    /** @var $system RankingSystem */
    $system = $this->createStubWithId(RankingSystem::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getRankingSystems()->set($system->getId(), $system);
    self::assertEquals(1, $tournament->getRankingSystems()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($system, $tournament->getRankingSystems()[$system->getId()]);
  }

  /**
   * @covers \App\Entity\Tournament::setTournamentListId
   * @covers \App\Entity\Tournament::getTournamentListId
   * @uses   \App\Entity\Tournament::__construct
   */
  public function testTournamentListId()
  {
    $tournament = $this->tournament();
    $tournament->setTournamentListId("Changed");
    self::assertEquals("Changed", $tournament->getTournamentListId());
  }

  /**
   * @covers \App\Entity\Tournament::setUserIdentifier
   * @covers \App\Entity\Tournament::getUserIdentifier
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Tournament::__construct
   */
  public function testUserIdentifier()
  {
    $tournament = $this->tournament();
    $tournament->setUserIdentifier("UserIdentifier");
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals("UserIdentifier", $tournament->getUserIdentifier());
  }

  /**
   * @covers \App\Entity\Tournament::getUserIdentifier
   * @uses   \App\Entity\Tournament::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testUserIdentifierException()
  {
    $tournament = $this->tournament();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property userIdentifier of the class " . Tournament::class . " must be set " .
      "before it can be accessed. Please set the property immediately after you call the constructor" .
      "(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $tournament->getUserIdentifier();
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