<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Game;
use Tfboe\FmLib\Entity\Match;
use Tfboe\FmLib\Entity\Player;
use Tfboe\FmLib\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;


/**
 * Class GameTest
 * @package Tests\Tfboe\FmLib\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class GameTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Game::getChildren
   * @uses   \Tfboe\FmLib\Entity\Game::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testChildren()
  {
    self::assertEmpty($this->game()->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Game::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Game::getPlayersA
   * @uses   \Tfboe\FmLib\Entity\Game::getPlayersB
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
   * @covers \Tfboe\FmLib\Entity\Game::setGameNumber
   * @covers \Tfboe\FmLib\Entity\Game::getGameNumber
   * @covers \Tfboe\FmLib\Entity\Game::getLocalIdentifier
   * @uses   \Tfboe\FmLib\Entity\Game::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
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
   * @covers \Tfboe\FmLib\Entity\Game::getLevel
   * @uses   \Tfboe\FmLib\Entity\Game::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::GAME, $this->game()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Game::setMatch
   * @covers \Tfboe\FmLib\Entity\Game::getMatch
   * @covers \Tfboe\FmLib\Entity\Game::getParent
   * @uses   \Tfboe\FmLib\Entity\Game::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \Tfboe\FmLib\Entity\Game::getGameNumber
   * @uses   \Tfboe\FmLib\Entity\Game::setGameNumber
   * @uses   \Tfboe\FmLib\Entity\Match
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
   * @covers \Tfboe\FmLib\Entity\Game::getPlayersA
   * @uses   \Tfboe\FmLib\Entity\Game::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
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
   * @covers \Tfboe\FmLib\Entity\Game::getPlayersB
   * @uses   \Tfboe\FmLib\Entity\Game::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
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