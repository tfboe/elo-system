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
use App\Entity\Phase;
use App\Exceptions\ValueNotSet;
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
    $phase->setCompetition($competition);
    self::assertEquals($competition, $phase->getCompetition());
    self::assertEquals(1, $phase->getCompetition()->getPhases()->count());
    self::assertEquals($phase, $phase->getCompetition()->getPhases()[$phase->getPhaseNumber()]);
  }

  public function testCompetitionException()
  {
    $phase = $this->phase();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property competition of the class " . Phase::class . " must be set before" .
      " it can be accessed. Please set the property immediately after you call the constructor(Empty Constructor " .
      "Pattern).");
    $phase->getCompetition();
  }

  public function testConstructor()
  {
    $phase = $this->phase();
    self::assertInstanceOf(Phase::class, $phase);
  }

  public function testId()
  {
    $phase = $this->phase();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($phase);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $phase->getId());
  }

  public function testIdException()
  {
    $phase = $this->phase();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Phase::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $phase->getId();
  }

  public function testName()
  {
    $phase = $this->phase();
    $phase->setName("Name");
    self::assertEquals("Name", $phase->getName());
  }

  public function testNameException()
  {
    $phase = $this->phase();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property name of the class " . Phase::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    $phase->getName();
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