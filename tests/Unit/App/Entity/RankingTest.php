<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Phase;
use App\Entity\Ranking;
use App\Entity\Team;
use App\Exceptions\ValueNotSet;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class RankingTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Ranking::__construct
   * @uses   \App\Entity\Helpers\NameEntity::getName
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Ranking::getTeams
   */
  public function testConstructor()
  {
    $ranking = $this->ranking();
    self::assertInstanceOf(Ranking::class, $ranking);
    self::assertEquals('', $ranking->getName());
    self::assertInstanceOf(Collection::class, $ranking->getTeams());
    self::assertEquals(0, $ranking->getTeams()->count());
  }

  /**
   * @covers \App\Entity\Ranking::setPhase
   * @covers \App\Entity\Ranking::getPhase
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Ranking::__construct
   * @uses   \App\Entity\Ranking::getUniqueRank
   * @uses   \App\Entity\Ranking::setUniqueRank
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testPhase()
  {
    $ranking = $this->ranking();
    $phase = new Phase();
    $ranking->setUniqueRank(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $ranking->setPhase($phase);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase, $ranking->getPhase());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $ranking->getPhase()->getRankings()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($ranking, $ranking->getPhase()->getRankings()[$ranking->getUniqueRank()]);

    $phase2 = new Phase();

    /** @noinspection PhpUnhandledExceptionInspection */
    $ranking->setPhase($phase2);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase2, $ranking->getPhase());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $ranking->getPhase()->getRankings()->count());
    self::assertEquals(0, $phase->getRankings()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($ranking, $ranking->getPhase()->getRankings()[$ranking->getUniqueRank()]);
  }

  /**
   * @covers \App\Entity\Ranking::getPhase
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Ranking::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testPhaseException()
  {
    $ranking = $this->ranking();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property phase of the class " . Ranking::class . " must be set before" .
      " it can be accessed. Please set the property immediately after you call the constructor(Empty Constructor " .
      "Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $ranking->getPhase();
  }

  /**
   * @covers \App\Entity\Ranking::setRank
   * @covers \App\Entity\Ranking::getRank
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Ranking::__construct
   */
  public function testRank()
  {
    $ranking = $this->ranking();
    $ranking->setRank(5);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(5, $ranking->getRank());
  }

  /**
   * @covers \App\Entity\Ranking::getRank
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Ranking::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testRankException()
  {
    $ranking = $this->ranking();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property rank of the class " . Ranking::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $ranking->getRank();
  }

  /**
   * @covers \App\Entity\Ranking::getTeams
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Ranking::__construct
   * @uses   \App\Entity\Team
   */
  public function testTeams()
  {
    $ranking = $this->ranking();
    $team = new Team();
    $team->setStartNumber(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $ranking->getTeams()->set($team->getStartNumber(), $team);
    self::assertEquals(1, $ranking->getTeams()->count());
    self::assertEquals($team, $ranking->getTeams()[1]);
  }

  /**
   * @covers \App\Entity\Ranking::setUniqueRank
   * @covers \App\Entity\Ranking::getUniqueRank
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Ranking::__construct
   */
  public function testUniqueRank()
  {
    $ranking = $this->ranking();
    $ranking->setUniqueRank(5);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(5, $ranking->getUniqueRank());
  }

  /**
   * @covers \App\Entity\Ranking::getUniqueRank
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Ranking::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testUniqueRankException()
  {
    $ranking = $this->ranking();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property uniqueRank of the class " . Ranking::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $ranking->getUniqueRank();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Ranking a new ranking
   */
  private function ranking(): Ranking
  {
    return new Ranking();
  }
//</editor-fold desc="Private Methods">
}