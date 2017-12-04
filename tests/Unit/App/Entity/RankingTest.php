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
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class RankingTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $ranking = $this->ranking();
    self::assertInstanceOf(Ranking::class, $ranking);
    self::assertEquals('', $ranking->getName());
    self::assertInstanceOf(Collection::class, $ranking->getTeams());
    self::assertEquals(0, $ranking->getTeams()->count());
  }

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

  public function testId()
  {
    $ranking = $this->ranking();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($ranking);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $ranking->getId());
  }

  public function testIdException()
  {
    $ranking = $this->ranking();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Ranking::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $ranking->getId();
  }

  public function testName()
  {
    $ranking = $this->ranking();
    $ranking->setName("Name");
    self::assertEquals("Name", $ranking->getName());
  }

  public function testRank()
  {
    $ranking = $this->ranking();
    $ranking->setRank(5);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(5, $ranking->getRank());
  }

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

  public function testUniqueRank()
  {
    $ranking = $this->ranking();
    $ranking->setUniqueRank(5);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(5, $ranking->getUniqueRank());
  }

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