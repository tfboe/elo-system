<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/17/18
 * Time: 8:16 PM
 */

namespace Tests\Integration;


use App\Entity\Competition;
use App\Entity\Player;
use App\Entity\Team;
use App\Entity\Tournament;
use LaravelDoctrine\ORM\Facades\EntityManager;
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

  public function testUnsuccessfullMerge()
  {
    /** @var Tournament $tournament */
    $tournament = entity(Tournament::class)->create([
      'userIdentifier' => 't1',
      'creator' => $this->user,
      'startTime' => new \DateTime('2017-12-30 15:00', new \DateTimeZone('Europe/Vienna')),
      'finished' => true
    ]);
    /** @var Competition[] $competitions */
    $competition = entity(Competition::class)->create(['name' => 'Test Competition']);
    $competition->setTournament($tournament);

    /** @var Team[] $teams */
    $teams = $this->createTeams(2);
    $teams[0]->setCompetition($competition);
    $teams[1]->setCompetition($competition);

    EntityManager::flush();

    /** @var Player[] $players */
    $this->jsonAuth("POST", "/admin/mergePlayers", [
      'player1' => $teams[0]->getMemberships()->first()->getPlayer()->getId(),
      'player2' => $teams[1]->getMemberships()->first()->getPlayer()->getId()
    ])->seeStatusCode(200);
    self::assertEquals("Player 1 and player 2 both attended the tournament " . $tournament->getName() .
      "(30.12.2017 15:00, id='1b68126e-7661-499d-a66a-7c98b9fb933e')", json_decode($this->response->content()));
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