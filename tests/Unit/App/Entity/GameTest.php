<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Game;
use App\Entity\Match;
use App\Entity\RankingSystem;
use App\Exceptions\ValueNotSet;
use App\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;
use Tests\Helpers\TestPlayer;


/**
 * Class GameTest
 * @package Tests\Unit\App\Entity
 */
class GameTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $game = $this->game();
    self::assertInstanceOf(Game::class, $game);
    self::assertInstanceOf(Collection::class, $game->getPlayersA());
    self::assertInstanceOf(Collection::class, $game->getPlayersB());
    self::assertInstanceOf(Collection::class, $game->getRankingSystems());
    self::assertEquals(0, $game->getPlayersA()->count());
    self::assertEquals(0, $game->getPlayersB()->count());
    self::assertEquals(0, $game->getRankingSystems()->count());
  }

  public function testGameNumber()
  {
    $game = $this->game();
    $game_number = 1;
    $game->setGameNumber($game_number);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($game_number, $game->getGameNumber());
  }

  public function testGameNumberException()
  {
    $game = $this->game();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property gameNumber of the class " . Game::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getGameNumber();
  }

  public function testMatchAndParent()
  {
    $game = $this->game();
    $match = new Match();
    $game->setGameNumber(1);

    /** @noinspection PhpUnhandledExceptionInspection */
    $game->setMatch($match);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($match, $game->getMatch());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $game->getMatch()->getGames()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($game, $game->getMatch()->getGames()[$game->getGameNumber()]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($game->getMatch(), $game->getParent());

    $match2 = new Match();

    /** @noinspection PhpUnhandledExceptionInspection */
    $game->setMatch($match2);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($match2, $game->getMatch());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $game->getMatch()->getGames()->count());
    self::assertEquals(0, $match->getGames()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($game, $game->getMatch()->getGames()[$game->getGameNumber()]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($game->getMatch(), $game->getParent());
  }

  public function testMatchException()
  {
    $game = $this->game();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property match of the class " . Game::class . " must be set before" .
      " it can be accessed. Please set the property immediately after you call the constructor(Empty Constructor " .
      "Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getMatch();
  }

  public function testPlayersA()
  {
    $game = $this->game();
    $player = new TestPlayer();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getPlayersA()->set($player->getId(), $player);
    self::assertEquals(1, $game->getPlayersA()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($player, $game->getPlayersA()[$player->getId()]);
  }

  public function testPlayersB()
  {
    $game = $this->game();
    $player = new TestPlayer();
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getPlayersB()->set($player->getId(), $player);
    self::assertEquals(1, $game->getPlayersB()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($player, $game->getPlayersB()[$player->getId()]);
  }

  public function testRankingSystems()
  {
    $e = $this->game();
    $system = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($system);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getRankingSystems()->set($system->getId(), $system);
    self::assertEquals(1, $e->getRankingSystems()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($system, $e->getRankingSystems()[$system->getId()]);
  }

  public function testLevel()
  {
    self::assertEquals(Level::GAME, $this->game()->getLevel());
  }

  public function testChildren()
  {
    self::assertEmpty($this->game()->getChildren());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Game a new game
   */
  private function game(): Game
  {
    return new Game();
  }
//</editor-fold desc="Private Methods">
}