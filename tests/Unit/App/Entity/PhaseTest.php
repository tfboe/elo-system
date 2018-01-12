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
use App\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PhaseTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Phase::setCompetition
   * @covers \App\Entity\Phase::getCompetition
   * @covers \App\Entity\Phase::getParent
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Entity\Phase::getPhaseNumber
   * @uses   \App\Entity\Phase::setPhaseNumber
   * @uses   \App\Entity\Competition
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
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

  /**
   * @covers \App\Entity\Phase::getCompetition
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
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

  /**
   * @covers \App\Entity\Phase::__construct
   * @uses   \App\Entity\Helpers\NameEntity::getName
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase::getPostQualifications
   * @uses   \App\Entity\Phase::getPreQualifications
   * @uses   \App\Entity\Phase::getRankings
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testConstructor()
  {
    $phase = $this->phase();
    self::assertInstanceOf(Phase::class, $phase);
    self::assertEquals('', $phase->getName());
    self::assertInstanceOf(Collection::class, $phase->getPostQualifications());
    self::assertEquals(0, $phase->getPostQualifications()->count());
    self::assertInstanceOf(Collection::class, $phase->getPreQualifications());
    self::assertEquals(0, $phase->getPreQualifications()->count());
    self::assertInstanceOf(Collection::class, $phase->getRankings());
    self::assertEquals(0, $phase->getRankings()->count());
  }

  /**
   * @covers \App\Entity\Phase::getLevel
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::PHASE, $this->phase()->getLevel());
  }

  /**
   * @covers \App\Entity\Phase::getLocalIdentifier
   * @covers \App\Entity\Phase::getPhaseNumber
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLocalIdentifierException()
  {
    $phase = $this->phase();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property phaseNumber of the class " . Phase::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $phase->getLocalIdentifier();
  }

  /**
   * @covers \App\Entity\Phase::getMatches
   * @covers \App\Entity\Phase::getChildren
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Entity\Match
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
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

  /**
   * @covers \App\Entity\Phase::getPostQualifications
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Entity\QualificationSystem
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testNextQualificationSystems()
  {
    $phase = $this->phase();
    $qualificationSystem = new QualificationSystem();
    $qualificationSystem->setPreviousPhase($phase);
    self::assertEquals(1, $phase->getPostQualifications()->count());
    self::assertEquals($qualificationSystem, $phase->getPostQualifications()[0]);
  }

  /**
   * @covers \App\Entity\Phase::setPhaseNumber
   * @covers \App\Entity\Phase::getPhaseNumber
   * @covers \App\Entity\Phase::getLocalIdentifier
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testPhaseNumberAndLocalIdentifier()
  {
    $phase = $this->phase();
    $phase->setPhaseNumber(5);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(5, $phase->getPhaseNumber());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase->getPhaseNumber(), $phase->getLocalIdentifier());
  }

  /**
   * @covers \App\Entity\Phase::getPhaseNumber
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Exceptions\ValueNotSet::__construct
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
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

  /**
   * @covers \App\Entity\Phase::getPreQualifications
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Entity\QualificationSystem
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testPreviousQualificationSystems()
  {
    $phase = $this->phase();
    $qualificationSystem = new QualificationSystem();
    $qualificationSystem->setNextPhase($phase);
    self::assertEquals(1, $phase->getPreQualifications()->count());
    self::assertEquals($qualificationSystem, $phase->getPreQualifications()[0]);
  }

  /**
   * @covers \App\Entity\Phase::getRankings
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase::__construct
   * @uses   \App\Entity\Ranking
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
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