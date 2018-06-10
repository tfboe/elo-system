<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/22/17
 * Time: 8:48 PM
 */

namespace Tests\Integration;

use App\Entity\Player;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\ApplicationGetter;
use Tfboe\FmLib\TestHelpers\AuthenticatedTestCase;

/**
 * Class TournamentTest
 * @package Tests\Integration
 */
class PlayerTest extends AuthenticatedTestCase
{
  use ApplicationGetter;

//<editor-fold desc="Public Methods">
  public function testAddAlreadyExistingPlayers()
  {
    /** @var Player $player1 */
    $player1 = entity(Player::class)->create();

    $playerArray1 = ['firstName' => $player1->getFirstName(), 'lastName' => $player1->getLastName(),
      'birthday' => $player1->getBirthday()->format('Y-m-d')];
    $playerArray2 = ['firstName' => $this->faker->firstName, 'lastName' => $this->faker->lastName,
      'birthday' => $this->faker->date()];

    $this->jsonAuth('POST', '/addPlayers', [$playerArray2, $playerArray1])
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Some players do already exist", "players" => [
        $this->getResultArray($player1)
      ], "name" => "PlayerAlreadyExistsException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Player::class);

    /** @var Player[] $players */
    $players = $repo->findAll();
    self::assertEquals(1, count($players));
    $player = $players[0];
    self::assertEquals($player, $player1);
  }

  public function testAddMultiplePlayers()
  {
    $playerArray1 = ['firstName' => $this->faker->firstName, 'lastName' => $this->faker->lastName,
      'birthday' => $this->faker->date()];
    $playerArray2 = ['firstName' => $this->faker->firstName, 'lastName' => $this->faker->lastName,
      'birthday' => $this->faker->date()];
    $this->jsonAuth('POST', '/addPlayers', [$playerArray1, $playerArray2])->assertResponseOk();
    $this->seeJsonStructure([['firstName', 'lastName', 'birthday', 'id']]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Player::class);

    /** @var Player[] $players */
    $players = $repo->findAll();
    self::assertEquals(2, count($players));
    foreach ($players as $player) {
      self::assertInternalType('int', $player->getId());
    }
  }

  public function testAddNoPlayers()
  {
    $this->jsonAuth('POST', '/addPlayers', [])->assertResponseOk();
    $this->seeJsonEquals([]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Player::class);

    /** @var Player[] $players */
    $players = $repo->findAll();
    self::assertEquals(0, count($players));
  }

  public function testAddPlayer()
  {
    $playerArray = ['firstName' => $this->faker->firstName, 'lastName' => $this->faker->lastName,
      'birthday' => $this->faker->date()];
    $this->jsonAuth('POST', '/addPlayers', [$playerArray])->assertResponseOk();
    $this->seeJsonStructure([['firstName', 'lastName', 'birthday', 'id']]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Player::class);

    /** @var Player[] $players */
    $players = $repo->findAll();
    self::assertEquals(1, count($players));
    $player = $players[0];
    self::assertEquals($playerArray['firstName'], $player->getFirstName());
    self::assertEquals($playerArray['lastName'], $player->getLastName());
    self::assertEquals(new \DateTime($playerArray['birthday']), $player->getBirthday());
    self::assertInternalType('int', $player->getId());
  }

  public function testAddPlayerMultipleTimes()
  {
    $playerArray = ['firstName' => $this->faker->firstName, 'lastName' => $this->faker->lastName,
      'birthday' => $this->faker->date()];
    $playerArray2 = ['firstName' => $this->faker->firstName, 'lastName' => $this->faker->lastName,
      'birthday' => $this->faker->date()];
    $playerArray3 = ['firstName' => $playerArray['firstName'], 'lastName' => $this->faker->lastName,
      'birthday' => $this->faker->date()];
    $playerArray4 = ['firstName' => $playerArray['firstName'], 'lastName' => $playerArray['lastName'],
      'birthday' => $this->faker->date()];

    $this->jsonAuth('POST', '/addPlayers', [$playerArray, $playerArray2, $playerArray3, $playerArray4,
      $playerArray])->assertResponseOk();
    $this->seeJsonStructure([['firstName', 'lastName', 'birthday', 'id']]);


    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Player::class);

    /** @var Player[] $players */
    $players = $repo->findAll();
    self::assertEquals(4, count($players));
    foreach ($players as $player) {
      self::assertInternalType('int', $player->getId());
    }
  }

  public function testSearchExistentPlayer()
  {
    /** @var Player $player */
    $player = entity(Player::class)->create();
    $search = ['firstName' => $player->getFirstName(), 'lastName' => $player->getLastName(),
      'birthday' => $player->getBirthday()->format('Y-m-d')];
    $this->jsonAuth('GET', '/searchPlayers', [$search])->assertResponseOk();
    $this->seeJsonEquals([["found" => [$this->getResultArray($player)], "search" => $search]]);
  }

  public function testSearchExistentPlayerWithoutBirthday()
  {
    /** @var Player $player */
    $player = entity(Player::class)->create();
    $search = ['firstName' => $player->getFirstName(), 'lastName' => $player->getLastName()];
    $this->jsonAuth('GET', '/searchPlayers', [$search])->assertResponseOk();
    $this->seeJsonEquals([["found" => [$this->getResultArray($player)], "search" => $search]]);
  }

  public function testSearchNonExistentPlayer()
  {
    $search = ['firstName' => 'James', 'lastName' => 'Smith', 'birthday' => '1974-04-28'];
    $this->jsonAuth('GET', '/searchPlayers', [$search])->assertResponseOk();
    $this->seeJsonEquals([["found" => [], "search" => $search]]);
  }

  public function testSearchPlayerCombination()
  {
    /** @var Player $player1 */
    $player1 = entity(Player::class)->create();

    /** @var Player $player1SameName */

    $player1SameName = entity(Player::class)->create(['firstName' => $player1->getFirstName(),
      'lastName' => $player1->getLastName()]);

    /** @var Player $player2 */
    $player2 = entity(Player::class)->create();

    $search1 = ['firstName' => $player1->getFirstName(), 'lastName' => $player1->getLastName()];
    $search2 = $search1;
    $search2['birthday'] = $player1->getBirthday()->format('Y-m-d');
    $search3 = ['firstName' => $player2->getFirstName(), 'lastName' => "wrong last name"];
    $search4 = ['firstName' => "wrong first name", 'lastName' => $player2->getLastName()];
    $search5 = ['firstName' => strtoupper($player2->getFirstName()),
      'lastName' => strtoupper($player2->getLastName())];

    $this->jsonAuth('GET', '/searchPlayers', [$search1, $search2, $search3, $search4, $search5])
      ->assertResponseOk();
    $this->seeJsonEquals([
      ["found" => [$this->getResultArray($player1), $this->getResultArray($player1SameName)], "search" => $search1],
      ["found" => [$this->getResultArray($player1)], "search" => $search2],
      ["found" => [], "search" => $search3],
      ["found" => [], "search" => $search4],
      ["found" => [$this->getResultArray($player2)], "search" => $search5]]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Creates a result array which is to expect from the searchPlayers method for a given player
   * @param Player $player the player for who to create the results array
   * @return mixed[] the result array as it should be returned for a found player from the searchPlayers method
   */
  private function getResultArray(Player $player)
  {
    return ['firstName' => $player->getFirstName(), 'lastName' => $player->getLastName(),
      'birthday' => $player->getBirthday()->format('Y-m-d'), 'id' => $player->getId()];
  }
//</editor-fold desc="Private Methods">
}