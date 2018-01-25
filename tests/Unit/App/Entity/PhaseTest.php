<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Tfboe\FmLib\Entity;

use Tfboe\FmLib\Entity\Competition;
use Tfboe\FmLib\Entity\Match;
use Tfboe\FmLib\Entity\Phase;
use Tfboe\FmLib\Entity\QualificationSystem;
use Tfboe\FmLib\Entity\Ranking;
use Tfboe\FmLib\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Tfboe\FmLib\Entity
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class PhaseTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Phase::setCompetition
   * @covers \Tfboe\FmLib\Entity\Phase::getCompetition
   * @covers \Tfboe\FmLib\Entity\Phase::getParent
   * @uses   \Tfboe\FmLib\Entity\Phase::__construct
   * @uses   \Tfboe\FmLib\Entity\Phase::getPhaseNumber
   * @uses   \Tfboe\FmLib\Entity\Phase::setPhaseNumber
   * @uses   \Tfboe\FmLib\Entity\Competition
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testCompetitionAndParent()
  {
    $phase = $this->phase();
    $competition = new Competition();
    $phase->setPhaseNumber(1);
    $phase->setCompetition($competition);
    self::assertEquals($competition, $phase->getCompetition());
    self::assertEquals(1, $phase->getCompetition()->getPhases()->count());
    self::assertEquals($phase, $phase->getCompetition()->getPhases()[$phase->getPhaseNumber()]);
    self::assertEquals($phase->getCompetition(), $phase->getParent());

    $competition2 = new Competition();

    $phase->setCompetition($competition2);
    self::assertEquals($competition2, $phase->getCompetition());
    self::assertEquals(1, $phase->getCompetition()->getPhases()->count());
    self::assertEquals(0, $competition->getPhases()->count());
    self::assertEquals($phase, $phase->getCompetition()->getPhases()[$phase->getPhaseNumber()]);
    self::assertEquals($phase->getCompetition(), $phase->getParent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Phase::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\NameEntity::getName
   * @uses   \Tfboe\FmLib\Entity\Phase::getPostQualifications
   * @uses   \Tfboe\FmLib\Entity\Phase::getPreQualifications
   * @uses   \Tfboe\FmLib\Entity\Phase::getRankings
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
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
   * @covers \Tfboe\FmLib\Entity\Phase::getLevel
   * @uses   \Tfboe\FmLib\Entity\Phase::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testLevel()
  {
    self::assertEquals(Level::PHASE, $this->phase()->getLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Phase::getMatches
   * @covers \Tfboe\FmLib\Entity\Phase::getChildren
   * @uses   \Tfboe\FmLib\Entity\Phase::__construct
   * @uses   \Tfboe\FmLib\Entity\Match
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testMatchesAndChildren()
  {
    $phase = $this->phase();
    $match = new Match();
    $match->setMatchNumber(1);
    self::assertEquals($phase->getMatches(), $phase->getChildren());
    $phase->getMatches()->set($match->getMatchNumber(), $match);
    self::assertEquals(1, $phase->getMatches()->count());
    self::assertEquals($match, $phase->getMatches()[1]);
    self::assertEquals($phase->getMatches(), $phase->getChildren());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Phase::getPostQualifications
   * @uses   \Tfboe\FmLib\Entity\Phase::__construct
   * @uses   \Tfboe\FmLib\Entity\QualificationSystem
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
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
   * @covers \Tfboe\FmLib\Entity\Phase::setPhaseNumber
   * @covers \Tfboe\FmLib\Entity\Phase::getPhaseNumber
   * @covers \Tfboe\FmLib\Entity\Phase::getLocalIdentifier
   * @uses   \Tfboe\FmLib\Entity\Phase::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testPhaseNumberAndLocalIdentifier()
  {
    $phase = $this->phase();
    $phase->setPhaseNumber(5);
    self::assertEquals(5, $phase->getPhaseNumber());
    self::assertEquals($phase->getPhaseNumber(), $phase->getLocalIdentifier());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Phase::getPreQualifications
   * @uses   \Tfboe\FmLib\Entity\Phase::__construct
   * @uses   \Tfboe\FmLib\Entity\QualificationSystem
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
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
   * @covers \Tfboe\FmLib\Entity\Phase::getRankings
   * @uses   \Tfboe\FmLib\Entity\Phase::__construct
   * @uses   \Tfboe\FmLib\Entity\Ranking
   * @uses   \Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testRankings()
  {
    $phase = $this->phase();
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
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