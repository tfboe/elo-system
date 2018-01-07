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
use Tests\Helpers\UnitTestCase;

/**
 * Class PlayerTest
 * @package Tests\Unit\App\Entity
 */
class PlayerTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Player::setBirthday
   * @covers \App\Entity\Player::getBirthday
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testBirthday()
  {
    $player = $this->player();
    $player->setBirthday(new \DateTime('1992-02-02 02:02:02'));
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(new \DateTime('1992-02-02 02:02:02'), $player->getBirthday());
  }

  /**
   * @covers \App\Entity\Player::getBirthday
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testBirthdayException()
  {
    $player = $this->player();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property birthday of the class App\Entity\Player must be set before it can be" .
      " accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $player->getBirthday();
  }

  /**
   * @covers \App\Entity\Player::setFirstName
   * @covers \App\Entity\Player::getFirstName
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testFirstName()
  {
    $player = $this->player();
    $player->setFirstName("First");
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals("First", $player->getFirstName());
  }

  /**
   * @covers \App\Entity\Player::getFirstName
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testFirstNameException()
  {
    $player = $this->player();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property firstName of the class App\Entity\Player must be set before it can be" .
      " accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $player->getFirstName();
  }

  /**
   * @covers \App\Entity\Player::getId
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testId()
  {
    $player = $this->player();
    $id_property = self::getProperty(Player::class, 'id');
    $id_property->setValue($player, 0);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(0, $player->getId());
  }

  /**
   * @covers \App\Entity\Player::getId
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testIdException()
  {
    $player = $this->player();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Player::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $player->getId();
  }

  /**
   * @covers \App\Entity\Player::setLastName
   * @covers \App\Entity\Player::getLastName
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testLastName()
  {
    $player = $this->player();
    $player->setLastName("Last");
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals("Last", $player->getLastName());
  }

  /**
   * @covers \App\Entity\Player::getLastName
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testLastNameException()
  {
    $player = $this->player();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property lastName of the class App\Entity\Player must be set before it can be" .
      " accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $player->getLastName();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Player a new player
   */
  private function player(): Player
  {
    return new Player();
  }
//</editor-fold desc="Private Methods">
}