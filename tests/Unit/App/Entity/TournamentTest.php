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
use App\Entity\Tournament;
use App\Entity\User;
use App\Exceptions\ValueNotSet;
use Doctrine\Common\Collections\ArrayCollection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class TournamentTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testCompetitions()
  {
    $tournament = $this->tournament();
    $competition = new Competition();
    $competition->setName('comp name');
    $tournament->getCompetitions()->set($competition->getName(), $competition);
    self::assertEquals(1, $tournament->getCompetitions()->count());
    self::assertEquals($competition, $tournament->getCompetitions()[$competition->getName()]);
  }

  public function testConstructor()
  {
    $tournament = $this->tournament();
    self::assertInstanceOf(Tournament::class, $tournament);
    self::assertInstanceOf(ArrayCollection::class, $tournament->getCompetitions());
    self::assertEquals(0, $tournament->getCompetitions()->count());
    self::assertEquals("", $tournament->getTournamentListId());
  }

  public function testCreator()
  {
    $tournament = $this->tournament();
    $creator = new User();
    $tournament->setCreator($creator);
    self::assertEquals($creator, $tournament->getCreator());
  }

  public function testCreatorException()
  {
    $tournament = $this->tournament();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property creator of the class " . Tournament::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor" .
      "(Empty Constructor Pattern).");
    $tournament->getCreator();
  }

  public function testId()
  {
    $tournament = $this->tournament();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($tournament);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $tournament->getId());
  }

  public function testIdException()
  {
    $tournament = $this->tournament();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Tournament::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $tournament->getId();
  }

  public function testName()
  {
    $tournament = $this->tournament();
    $tournament->setName("Name");
    self::assertEquals("Name", $tournament->getName());
  }

  public function testNameException()
  {
    $tournament = $this->tournament();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property name of the class " . Tournament::class . " must be set before it can" .
      " be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $tournament->getName();
  }

  public function testTournamentListId()
  {
    $tournament = $this->tournament();
    $tournament->setTournamentListId("Changed");
    self::assertEquals("Changed", $tournament->getTournamentListId());
  }

  public function testUserIdentifier()
  {
    $tournament = $this->tournament();
    $tournament->setUserIdentifier("UserIdentifier");
    self::assertEquals("UserIdentifier", $tournament->getUserIdentifier());
  }

  public function testUserIdentifierException()
  {
    $tournament = $this->tournament();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property userIdentifier of the class " . Tournament::class . " must be set " .
      "before it can be accessed. Please set the property immediately after you call the constructor" .
      "(Empty Constructor Pattern).");
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