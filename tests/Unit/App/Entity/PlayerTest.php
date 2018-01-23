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
   */
  public function testBirthday()
  {
    $player = $this->player();
    $player->setBirthday(new \DateTime('1992-02-02 02:02:02'));
    self::assertEquals(new \DateTime('1992-02-02 02:02:02'), $player->getBirthday());
  }

  /**
   * @covers \App\Entity\Player::setFirstName
   * @covers \App\Entity\Player::getFirstName
   */
  public function testFirstName()
  {
    $player = $this->player();
    $player->setFirstName("First");
    self::assertEquals("First", $player->getFirstName());
  }

  /**
   * @covers \App\Entity\Player::getPlayerId
   */
  public function testId()
  {
    $player = $this->player();
    /** @noinspection PhpUnhandledExceptionInspection */
    $idProperty = self::getProperty(Player::class, 'playerId');
    $idProperty->setValue($player, 0);
    self::assertEquals(0, $player->getPlayerId());
  }

  /**
   * @covers \App\Entity\Player::setLastName
   * @covers \App\Entity\Player::getLastName
   */
  public function testLastName()
  {
    $player = $this->player();
    $player->setLastName("Last");
    self::assertEquals("Last", $player->getLastName());
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