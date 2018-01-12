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
use App\Entity\Phase;
use App\Entity\Ranking;
use App\Exceptions\ValueNotSet;
use App\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class MatchTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Match::getGames
   * @uses   \App\Entity\Match::getRankingsA
   * @uses   \App\Entity\Match::getRankingsB
   */
  public function testConstructor()
  {
    $match = $this->match();
    self::assertInstanceOf(Match::class, $match);
    self::assertInstanceOf(Collection::class, $match->getRankingsA());
    self::assertInstanceOf(Collection::class, $match->getRankingsB());
    self::assertInstanceOf(Collection::class, $match->getGames());
    self::assertEquals(0, $match->getRankingsA()->count());
    self::assertEquals(0, $match->getRankingsB()->count());
    self::assertEquals(0, $match->getGames()->count());
  }

  /**
   * @covers \App\Entity\Match::getGames
   * @covers \App\Entity\Match::getChildren
   * @uses   \App\Entity\Game
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testGamesAndChildren()
  {
    $match = $this->match();
    $game = new Game();
    $game->setGameNumber(1);
    self::assertEquals($match->getGames(), $match->getChildren());
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getGames()->set($game->getGameNumber(), $game);
    self::assertEquals(1, $match->getGames()->count());
    self::assertEquals($game, $match->getGames()[1]);
    self::assertEquals($match->getGames(), $match->getChildren());
  }

  /**
   * @covers \App\Entity\Match::getLevel
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::MATCH, $this->match()->getLevel());
  }

  /**
   * @covers \App\Entity\Match::getLocalIdentifier
   * @uses   \App\Entity\Match::getMatchNumber
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLocalIdentifierException()
  {
    $match = $this->match();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property matchNumber of the class " . Match::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getLocalIdentifier();
  }

  /**
   * @covers \App\Entity\Match::setMatchNumber
   * @covers \App\Entity\Match::getMatchNumber
   * @covers \App\Entity\Match::getLocalIdentifier
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testMatchNumberAndLocalIdentifier()
  {
    $match = $this->match();
    $matchNumber = 1;
    $match->setMatchNumber($matchNumber);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($matchNumber, $match->getMatchNumber());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($match->getMatchNumber(), $match->getLocalIdentifier());
  }

  /**
   * @covers \App\Entity\Match::getMatchNumber
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testMatchNumberException()
  {
    $match = $this->match();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property matchNumber of the class " . Match::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getMatchNumber();
  }

  /**
   * @covers \App\Entity\Match::setPhase
   * @covers \App\Entity\Match::getPhase
   * @covers \App\Entity\Match::getParent
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Match::getMatchNumber
   * @uses   \App\Entity\Match::setMatchNumber
   * @uses   \App\Entity\Phase
   */
  public function testPhaseAndParent()
  {
    $match = $this->match();
    $phase = new Phase();
    $match->setMatchNumber(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->setPhase($phase);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase, $match->getPhase());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $match->getPhase()->getMatches()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($match, $match->getPhase()->getMatches()[$match->getMatchNumber()]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($match->getPhase(), $match->getParent());

    $phase2 = new Phase();

    /** @noinspection PhpUnhandledExceptionInspection */
    $match->setPhase($phase2);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase2, $match->getPhase());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $match->getPhase()->getMatches()->count());
    self::assertEquals(0, $phase->getMatches()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($match, $match->getPhase()->getMatches()[$match->getMatchNumber()]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($match->getPhase(), $match->getParent());
  }

  /**
   * @covers \App\Entity\Match::getPhase
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testPhaseException()
  {
    $match = $this->match();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property phase of the class " . Match::class . " must be set before" .
      " it can be accessed. Please set the property immediately after you call the constructor(Empty Constructor " .
      "Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getPhase();
  }

  /**
   * @covers \App\Entity\Match::getRankingsA
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Ranking
   */
  public function testRankingsA()
  {
    $match = $this->match();
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getRankingsA()->set($ranking->getUniqueRank(), $ranking);
    self::assertEquals(1, $match->getRankingsA()->count());
    self::assertEquals($ranking, $match->getRankingsA()[1]);
  }

  /**
   * @covers \App\Entity\Match::getRankingsB
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Ranking
   */
  public function testRankingsB()
  {
    $match = $this->match();
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getRankingsB()->set($ranking->getUniqueRank(), $ranking);
    self::assertEquals(1, $match->getRankingsB()->count());
    self::assertEquals($ranking, $match->getRankingsB()[1]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Match a new match
   */
  private function match(): Match
  {
    return new Match();
  }
//</editor-fold desc="Private Methods">
}