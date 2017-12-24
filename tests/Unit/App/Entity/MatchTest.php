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
use Doctrine\Common\Collections\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class MatchTest extends TestCase
{
//<editor-fold desc="Public Methods">
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
    self::assertNull($match->getStartTime());
    self::assertNull($match->getEndTime());
  }

  public function testEndTime()
  {
    $match = $this->match();
    $time = new \DateTime('2017-12-31 16:00', new \DateTimeZone('Europe/Vienna'));
    $match->setEndTime($time);
    self::assertEquals($time, $match->getEndTime());
  }

  public function testId()
  {
    $match = $this->match();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($match);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $match->getId());
  }

  public function testIdException()
  {
    $match = $this->match();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Match::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getId();
  }

  public function testMatchNumber()
  {
    $match = $this->match();
    $match_number = 1;
    $match->setMatchNumber($match_number);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($match_number, $match->getMatchNumber());
  }

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

  public function testPhase()
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
  }

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

  public function testStartTime()
  {
    $match = $this->match();
    $time = new \DateTime('2017-12-31 16:00', new \DateTimeZone('Europe/Vienna'));
    $match->setStartTime($time);
    self::assertEquals($time, $match->getStartTime());
  }

  public function testGames()
  {
    $match = $this->match();
    $game = new Game();
    $game->setGameNumber(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $match->getGames()->set($game->getGameNumber(), $game);
    self::assertEquals(1, $match->getGames()->count());
    self::assertEquals($game, $match->getGames()[1]);
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