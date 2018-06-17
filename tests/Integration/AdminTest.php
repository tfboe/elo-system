<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/17/18
 * Time: 8:16 PM
 */

namespace Tests\Integration;


use App\Entity\Player;
use Tfboe\FmLib\Entity\PlayerInterface;

class AdminTest extends AuthenticatedTestCase
{
//<editor-fold desc="Public Methods">
  public function testMergePlayers()
  {
    /** @var Player[] $players */
    $players = $this->createPlayers(2);
    $this->jsonAuth("POST", "/admin/mergePlayers", [
      'player1' => $players[0]->getId(),
      'player2' => $players[1]->getId()
    ])->seeStatusCode(200);
    self::assertEquals($players[0]->getId(), $players[1]->getMergedInto()->getId());
    self::assertCount(1, $players[0]->getMergedPlayers());
    self::assertEquals($players[1]->getId(), $players[0]->getMergedPlayers()->first()->getId());
  }

  public function testMergePlayersWithFlatten()
  {
    /** @var Player[] $players */
    $players = $this->createPlayers(2);
    $mergedPlayer = entity($this->resolveEntity(PlayerInterface::class))->create(['mergedInto' => $players[1]]);
    $players[1]->getMergedPlayers()->set($mergedPlayer->getId(), $mergedPlayer);
    self::assertCount(1, $players[1]->getMergedPlayers());
    $this->jsonAuth("POST", "/admin/mergePlayers", [
      'player1' => $players[0]->getId(),
      'player2' => $players[1]->getId()
    ])->seeStatusCode(200);
    self::assertEquals($players[0]->getId(), $players[1]->getMergedInto()->getId());
    self::assertEmpty($players[1]->getMergedPlayers());
    self::assertCount(2, $players[0]->getMergedPlayers());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * @param array|mixed[] $attributes
   */
  protected function addAdditionalNewUserAttributes(array &$attributes)
  {
    parent::addAdditionalNewUserAttributes($attributes);
    $attributes['admin'] = true;
  }
//</editor-fold desc="Protected Methods">
}