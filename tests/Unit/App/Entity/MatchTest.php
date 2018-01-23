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
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testGamesAndChildren()
  {
    $match = $this->match();
    $game = new Game();
    $game->setGameNumber(1);
    self::assertEquals($match->getGames(), $match->getChildren());
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
   * @covers \App\Entity\Match::setMatchNumber
   * @covers \App\Entity\Match::getMatchNumber
   * @covers \App\Entity\Match::getLocalIdentifier
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testMatchNumberAndLocalIdentifier()
  {
    $match = $this->match();
    $matchNumber = 1;
    $match->setMatchNumber($matchNumber);
    self::assertEquals($matchNumber, $match->getMatchNumber());
    self::assertEquals($match->getMatchNumber(), $match->getLocalIdentifier());
  }

  /**
   * @covers \App\Entity\Match::setPhase
   * @covers \App\Entity\Match::getPhase
   * @covers \App\Entity\Match::getParent
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
    $match->setPhase($phase);
    self::assertEquals($phase, $match->getPhase());
    self::assertEquals(1, $match->getPhase()->getMatches()->count());
    self::assertEquals($match, $match->getPhase()->getMatches()[$match->getMatchNumber()]);
    self::assertEquals($match->getPhase(), $match->getParent());

    $phase2 = new Phase();

    $match->setPhase($phase2);
    self::assertEquals($phase2, $match->getPhase());
    self::assertEquals(1, $match->getPhase()->getMatches()->count());
    self::assertEquals(0, $phase->getMatches()->count());
    self::assertEquals($match, $match->getPhase()->getMatches()[$match->getMatchNumber()]);
    self::assertEquals($match->getPhase(), $match->getParent());
  }

  /**
   * @covers \App\Entity\Match::getRankingsA
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Ranking
   */
  public function testRankingsA()
  {
    $match = $this->match();
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
    $match->getRankingsA()->set($ranking->getUniqueRank(), $ranking);
    self::assertEquals(1, $match->getRankingsA()->count());
    self::assertEquals($ranking, $match->getRankingsA()[1]);
  }

  /**
   * @covers \App\Entity\Match::getRankingsB
   * @uses   \App\Entity\Match::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   * @uses   \App\Entity\Ranking
   */
  public function testRankingsB()
  {
    $match = $this->match();
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
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