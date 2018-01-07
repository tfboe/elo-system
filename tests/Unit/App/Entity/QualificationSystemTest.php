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
use App\Entity\QualificationSystem;
use App\Exceptions\ValueNotSet;
use Tests\Helpers\UnitTestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class QualificationSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\QualificationSystem::setNextPhase
   * @covers \App\Entity\QualificationSystem::getNextPhase
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase
   */
  public function testNextPhase()
  {
    $system = $this->system();
    $phase = new Phase();

    /** @noinspection PhpUnhandledExceptionInspection */
    $system->setNextPhase($phase);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase, $system->getNextPhase());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $system->getNextPhase()->getPreviousQualificationSystems()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($system, $system->getNextPhase()->getPreviousQualificationSystems()[0]);

    $phase2 = new Phase();

    /** @noinspection PhpUnhandledExceptionInspection */
    $system->setNextPhase($phase2);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase2, $system->getNextPhase());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $system->getNextPhase()->getPreviousQualificationSystems()->count());
    self::assertEquals(0, $phase->getPreviousQualificationSystems()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($system, $system->getNextPhase()->getPreviousQualificationSystems()[0]);
  }

  /**
   * @covers \App\Entity\QualificationSystem::getNextPhase
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testNextPhaseException()
  {
    $system = $this->system();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property nextPhase of the class " . QualificationSystem::class . " must be" .
      " set before it can be accessed. Please set the property immediately after you call the constructor(Empty " .
      "Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $system->getNextPhase();
  }

  /**
   * @covers \App\Entity\QualificationSystem::setPreviousPhase
   * @covers \App\Entity\QualificationSystem::getPreviousPhase
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\Phase
   */
  public function testPreviousPhase()
  {
    $system = $this->system();
    $phase = new Phase();

    /** @noinspection PhpUnhandledExceptionInspection */
    $system->setPreviousPhase($phase);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase, $system->getPreviousPhase());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $system->getPreviousPhase()->getNextQualificationSystems()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($system, $system->getPreviousPhase()->getNextQualificationSystems()[0]);

    $phase2 = new Phase();

    /** @noinspection PhpUnhandledExceptionInspection */
    $system->setPreviousPhase($phase2);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase2, $system->getPreviousPhase());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $system->getPreviousPhase()->getNextQualificationSystems()->count());
    self::assertEquals(0, $phase->getNextQualificationSystems()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($system, $system->getPreviousPhase()->getNextQualificationSystems()[0]);
  }

  /**
   * @covers \App\Entity\QualificationSystem::getPreviousPhase
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testPreviousPhaseException()
  {
    $system = $this->system();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property previousPhase of the class " . QualificationSystem::class . " must be" .
      " set before it can be accessed. Please set the property immediately after you call the constructor(Empty " .
      "Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $system->getPreviousPhase();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return QualificationSystem a new qualification system
   */
  private function system(): QualificationSystem
  {
    return new QualificationSystem();
  }
//</editor-fold desc="Private Methods">
}