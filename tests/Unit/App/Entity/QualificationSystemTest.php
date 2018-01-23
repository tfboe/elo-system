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
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testNextPhase()
  {
    $system = $this->system();
    $phase = new Phase();

    $system->setNextPhase($phase);
    self::assertEquals($phase, $system->getNextPhase());
    self::assertEquals(1, $system->getNextPhase()->getPreQualifications()->count());
    self::assertEquals($system, $system->getNextPhase()->getPreQualifications()[0]);

    $phase2 = new Phase();

    $system->setNextPhase($phase2);
    self::assertEquals($phase2, $system->getNextPhase());
    self::assertEquals(1, $system->getNextPhase()->getPreQualifications()->count());
    self::assertEquals(0, $phase->getPreQualifications()->count());
    self::assertEquals($system, $system->getNextPhase()->getPreQualifications()[0]);
  }

  /**
   * @covers \App\Entity\QualificationSystem::setPreviousPhase
   * @covers \App\Entity\QualificationSystem::getPreviousPhase
   * @uses   \App\Entity\Phase
   * @uses   \App\Entity\Helpers\TournamentHierarchyEntity::__construct
   */
  public function testPreviousPhase()
  {
    $system = $this->system();
    $phase = new Phase();

    $system->setPreviousPhase($phase);
    self::assertEquals($phase, $system->getPreviousPhase());
    self::assertEquals(1, $system->getPreviousPhase()->getPostQualifications()->count());
    self::assertEquals($system, $system->getPreviousPhase()->getPostQualifications()[0]);

    $phase2 = new Phase();

    $system->setPreviousPhase($phase2);
    self::assertEquals($phase2, $system->getPreviousPhase());
    self::assertEquals(1, $system->getPreviousPhase()->getPostQualifications()->count());
    self::assertEquals(0, $phase->getPostQualifications()->count());
    self::assertEquals($system, $system->getPreviousPhase()->getPostQualifications()[0]);
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