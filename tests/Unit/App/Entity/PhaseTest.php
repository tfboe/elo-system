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
use App\Exceptions\ValueNotSet;
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
  public function testCompetition()
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
  }

  public function testId()
  {
    $phase = $this->phase();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($phase);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $phase->getId());
  }

  public function testIdException()
  {
    $phase = $this->phase();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Phase::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getId();
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

  public function testName()
  {
    $phase = $this->phase();
    $phase->setName("Name");
    self::assertEquals("Name", $phase->getName());
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

  public function testMatches()
  {
    $phase = $this->phase();
    $match = new Match();
    $match->setMatchNumber(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getMatches()->set($match->getMatchNumber(), $match);
    self::assertEquals(1, $phase->getMatches()->count());
    self::assertEquals($match, $phase->getMatches()[1]);
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