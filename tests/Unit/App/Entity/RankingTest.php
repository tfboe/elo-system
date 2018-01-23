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
    $ranking->setPhase($phase);
    self::assertEquals($phase, $ranking->getPhase());
    self::assertEquals(1, $ranking->getPhase()->getRankings()->count());
    self::assertEquals($ranking, $ranking->getPhase()->getRankings()[$ranking->getUniqueRank()]);

    $phase2 = new Phase();

    $ranking->setPhase($phase2);
    self::assertEquals($phase2, $ranking->getPhase());
    self::assertEquals(1, $ranking->getPhase()->getRankings()->count());
    self::assertEquals(0, $phase->getRankings()->count());
    self::assertEquals($ranking, $ranking->getPhase()->getRankings()[$ranking->getUniqueRank()]);
  }

  /**
   * @covers \App\Entity\Ranking::setRank
   * @covers \App\Entity\Ranking::getRank
   * @uses   \App\Entity\Ranking::__construct
   */
  public function testRank()
  {
    $ranking = $this->ranking();
    $ranking->setRank(5);
    self::assertEquals(5, $ranking->getRank());
  }

  /**
   * @covers \App\Entity\Ranking::getTeams
   * @uses   \App\Entity\Ranking::__construct
   * @uses   \App\Entity\Team
   */
  public function testTeams()
  {
    $ranking = $this->ranking();
    $team = new Team();
    $team->setStartNumber(1);
    $ranking->getTeams()->set($team->getStartNumber(), $team);
    self::assertEquals(1, $ranking->getTeams()->count());
    self::assertEquals($team, $ranking->getTeams()[1]);
  }

  /**
   * @covers \App\Entity\Ranking::setUniqueRank
   * @covers \App\Entity\Ranking::getUniqueRank
   * @uses   \App\Entity\Ranking::__construct
   */
  public function testUniqueRank()
  {
    $ranking = $this->ranking();
    $ranking->setUniqueRank(5);
    self::assertEquals(5, $ranking->getUniqueRank());
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