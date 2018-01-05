<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Competition;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\QualificationSystem;
use App\Entity\Ranking;
use App\Entity\RankingSystem;
use App\Exceptions\ValueNotSet;
use App\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class PhaseTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testCompetitionAndParent()
  {
    $phase = $this->phase();
    $competition = new Competition();
    $phase->setPhaseNumber(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->setCompetition($competition);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($competition, $phase->getCompetition());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $phase->getCompetition()->getPhases()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase, $phase->getCompetition()->getPhases()[$phase->getPhaseNumber()]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase->getCompetition(), $phase->getParent());

    $competition2 = new Competition();

    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->setCompetition($competition2);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($competition2, $phase->getCompetition());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $phase->getCompetition()->getPhases()->count());
    self::assertEquals(0, $competition->getPhases()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase, $phase->getCompetition()->getPhases()[$phase->getPhaseNumber()]);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase->getCompetition(), $phase->getParent());
  }

  public function testCompetitionException()
  {
    $phase = $this->phase();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property competition of the class " . Phase::class . " must be set before" .
      " it can be accessed. Please set the property immediately after you call the constructor(Empty Constructor " .
      "Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getCompetition();
  }

  public function testConstructor()
  {
    $phase = $this->phase();
    self::assertInstanceOf(Phase::class, $phase);
    self::assertEquals('', $phase->getName());
    self::assertInstanceOf(Collection::class, $phase->getNextQualificationSystems());
    self::assertEquals(0, $phase->getNextQualificationSystems()->count());
    self::assertInstanceOf(Collection::class, $phase->getPreviousQualificationSystems());
    self::assertEquals(0, $phase->getPreviousQualificationSystems()->count());
    self::assertInstanceOf(Collection::class, $phase->getRankings());
    self::assertEquals(0, $phase->getRankings()->count());
    self::assertInstanceOf(Collection::class, $phase->getRankingSystems());
    self::assertEquals(0, $phase->getRankingSystems()->count());
  }

  public function testPhaseNumber()
  {
    $phase = $this->phase();
    $phase->setPhaseNumber(5);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(5, $phase->getPhaseNumber());
  }

  public function testPhaseNumberException()
  {
    $phase = $this->phase();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property phaseNumber of the class " . Phase::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getPhaseNumber();
  }

  public function testPreviousQualificationSystems()
  {
    $phase = $this->phase();
    $qualification_system = new QualificationSystem();
    $qualification_system->setNextPhase($phase);
    self::assertEquals(1, $phase->getPreviousQualificationSystems()->count());
    self::assertEquals($qualification_system, $phase->getPreviousQualificationSystems()[0]);
  }

  public function testNextQualificationSystems()
  {
    $phase = $this->phase();
    $qualification_system = new QualificationSystem();
    $qualification_system->setPreviousPhase($phase);
    self::assertEquals(1, $phase->getNextQualificationSystems()->count());
    self::assertEquals($qualification_system, $phase->getNextQualificationSystems()[0]);
  }

  public function testRankings()
  {
    $phase = $this->phase();
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getRankings()->set($ranking->getUniqueRank(), $ranking);
    self::assertEquals(1, $phase->getRankings()->count());
    self::assertEquals($ranking, $phase->getRankings()[1]);
  }

  public function testMatchesAndChildren()
  {
    $phase = $this->phase();
    $match = new Match();
    $match->setMatchNumber(1);
    self::assertEquals($phase->getMatches(), $phase->getChildren());
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getMatches()->set($match->getMatchNumber(), $match);
    self::assertEquals(1, $phase->getMatches()->count());
    self::assertEquals($match, $phase->getMatches()[1]);
    self::assertEquals($phase->getMatches(), $phase->getChildren());
  }

  public function testRankingSystems()
  {
    $e = $this->phase();
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
    self::assertEquals(Level::PHASE, $this->phase()->getLevel());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Phase a new phase
   */
  private function phase(): Phase
  {
    return new Phase();
  }
//</editor-fold desc="Private Methods">
}