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
use App\Entity\Player;
use App\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;


/**
 * Class GameTest
 * @package Tests\Unit\App\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GameTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Game::getChildren
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testChildren()
  {
    self::assertEmpty($this->game()->getChildren());
  }

  /**
   * @covers \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Game::getPlayersA
   * @uses   \App\Entity\Game::getPlayersB
   */
  public function testConstructor()
  {
    $game = $this->game();
    self::assertInstanceOf(Game::class, $game);
    self::assertInstanceOf(Collection::class, $game->getPlayersA());
    self::assertInstanceOf(Collection::class, $game->getPlayersB());
    self::assertEquals(0, $game->getPlayersA()->count());
    self::assertEquals(0, $game->getPlayersB()->count());
  }

  /**
   * @covers \App\Entity\Game::setGameNumber
   * @covers \App\Entity\Game::getGameNumber
   * @covers \App\Entity\Game::getLocalIdentifier
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testGameNumberAndLocalIdentifier()
  {
    $game = $this->game();
    $gameNumber = 1;
    $game->setGameNumber($gameNumber);
    self::assertEquals($gameNumber, $game->getGameNumber());
    self::assertEquals($game->getGameNumber(), $game->getLocalIdentifier());
  }

  /**
   * @covers \App\Entity\Game::getLevel
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::GAME, $this->game()->getLevel());
  }

  /**
   * @covers \App\Entity\Game::setMatch
   * @covers \App\Entity\Game::getMatch
   * @covers \App\Entity\Game::getParent
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Game::getGameNumber
   * @uses   \App\Entity\Game::setGameNumber
   * @uses   \App\Entity\Match
   */
  public function testMatchAndParent()
  {
    $game = $this->game();
    $match = new Match();
    $game->setGameNumber(1);

    $game->setMatch($match);
    self::assertEquals($match, $game->getMatch());
    self::assertEquals(1, $game->getMatch()->getGames()->count());
    self::assertEquals($game, $game->getMatch()->getGames()[$game->getGameNumber()]);
    self::assertEquals($game->getMatch(), $game->getParent());

    $match2 = new Match();

    $game->setMatch($match2);
    self::assertEquals($match2, $game->getMatch());
    self::assertEquals(1, $game->getMatch()->getGames()->count());
    self::assertEquals(0, $match->getGames()->count());
    self::assertEquals($game, $game->getMatch()->getGames()[$game->getGameNumber()]);
    self::assertEquals($game->getMatch(), $game->getParent());
  }

  /**
   * @covers \App\Entity\Game::getPlayersA
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testPlayersA()
  {
    $game = $this->game();
    /** @var Player $player */
    $player = $this->createStubWithId(Player::class, 1, 'getPlayerId');
    $game->getPlayersA()->set($player->getPlayerId(), $player);
    self::assertEquals(1, $game->getPlayersA()->count());
    self::assertEquals($player, $game->getPlayersA()[$player->getPlayerId()]);
  }

  /**
   * @covers \App\Entity\Game::getPlayersB
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testPlayersB()
  {
    $game = $this->game();
    /** @var Player $player */
    $player = $this->createStubWithId(Player::class, 1, 'getPlayerId');
    $game->getPlayersB()->set($player->getPlayerId(), $player);
    self::assertEquals(1, $game->getPlayersB()->count());
    self::assertEquals($player, $game->getPlayersB()[$player->getPlayerId()]);
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