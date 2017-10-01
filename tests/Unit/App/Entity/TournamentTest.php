<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Tournament;
use App\Entity\User;
use App\Exceptions\ValueNotSet;
use Tests\Helpers\TestCase;

class TournamentTest extends TestCase
{
//<editor-fold desc="Public Methods">
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
    self::assertEquals("", $tournament->getTournamentListId());
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
  private function tournament(): Tournament
  {
    return new Tournament();
  }
//</editor-fold desc="Private Methods">
}