<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Player;
use App\Exceptions\ValueNotSet;
use Tests\Helpers\TestCase;

class PlayerTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testBirthday()
  {
    $player = $this->player();
    $player->setBirthday(new \DateTime('1992-02-02 02:02:02'));
    self::assertEquals(new \DateTime('1992-02-02 02:02:02'), $player->getBirthday());
  }

  public function testBirthdayException()
  {
    $player = $this->player();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property birthday of the class App\Entity\Player must be set before it can be" .
      " accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $player->getBirthday();
  }

  public function testFirstName()
  {
    $player = $this->player();
    $player->setFirstName("First");
    self::assertEquals("First", $player->getFirstName());
  }

  public function testFirstNameException()
  {
    $player = $this->player();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property firstName of the class App\Entity\Player must be set before it can be" .
      " accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $player->getFirstName();
  }

  public function testLastName()
  {
    $player = $this->player();
    $player->setLastName("Last");
    self::assertEquals("Last", $player->getLastName());
  }

  public function testLastNameException()
  {
    $player = $this->player();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property lastName of the class App\Entity\Player must be set before it can be" .
      " accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $player->getLastName();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  private function player(): Player
  {
    return new Player();
  }
//</editor-fold desc="Private Methods">
}