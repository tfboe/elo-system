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
use App\Entity\RankingSystem;
use App\Exceptions\ValueNotSet;
use App\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;


/**
 * Class GameTest
 * @package Tests\Unit\App\Entity
 */
class GameTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Game::__construct
   * @uses   \App\Entity\Game::getPlayersA
   * @uses   \App\Entity\Game::getPlayersB
   * @uses   \App\Entity\Game::getRankingSystems
   */
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

  /**
   * @covers \App\Entity\Game::setGameNumber
   * @covers \App\Entity\Game::getGameNumber
   * @covers \App\Entity\Game::getLocalIdentifier
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testGameNumberAndLocalIdentifier()
  {
    $game = $this->game();
    $game_number = 1;
    $game->setGameNumber($game_number);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($game_number, $game->getGameNumber());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($game->getGameNumber(), $game->getLocalIdentifier());
  }

  /**
   * @covers \App\Entity\Game::getGameNumber
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
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

  /**
   * @covers \App\Entity\Game::getLocalIdentifier
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Game::getGameNumber
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testLocalIdentifierException()
  {
    $game = $this->game();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property gameNumber of the class " . Game::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getLocalIdentifier();
  }

  /**
   * @covers \App\Entity\Game::setMatch
   * @covers \App\Entity\Game::getMatch
   * @covers \App\Entity\Game::getParent
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Game::getGameNumber
   * @uses   \App\Entity\Game::setGameNumber
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match
   */
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

  /**
   * @covers \App\Entity\Game::getMatch
   * @uses   \App\Entity\Game::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
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

  /**
   * @covers \App\Entity\Game::getPlayersA
   * @uses   \App\Entity\Game::__construct
   */
  public function testPlayersA()
  {
    $game = $this->game();
    /** @var Player $player */
    $player = $this->createMockWithId(Player::class, 1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getPlayersA()->set($player->getId(), $player);
    self::assertEquals(1, $game->getPlayersA()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($player, $game->getPlayersA()[$player->getId()]);
  }

  /**
   * @covers \App\Entity\Game::getPlayersB
   * @uses   \App\Entity\Game::__construct
   */
  public function testPlayersB()
  {
    $game = $this->game();
    /** @var Player $player */
    $player = $this->createMockWithId(Player::class, 1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $game->getPlayersB()->set($player->getId(), $player);
    self::assertEquals(1, $game->getPlayersB()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($player, $game->getPlayersB()[$player->getId()]);
  }

  /**
   * @covers \App\Entity\Game::getRankingSystems
   * @uses   \App\Entity\Game::__construct
   */
  public function testRankingSystems()
  {
    $e = $this->game();
    /** @var $system RankingSystem */
    $system = $this->createMockWithId(RankingSystem::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getRankingSystems()->set($system->getId(), $system);
    self::assertEquals(1, $e->getRankingSystems()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($system, $e->getRankingSystems()[$system->getId()]);
  }

  /**
   * @covers \App\Entity\Game::getLevel
   * @uses   \App\Entity\Game::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::GAME, $this->game()->getLevel());
  }

  /**
   * @covers \App\Entity\Game::getChildren
   * @uses   \App\Entity\Game::__construct
   */
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