<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 1:11 PM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Group;
use App\Entity\Phase;
use App\Exceptions\ValueNotSet;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class TournamentTest
 * @package Tests\Unit\App\Entity
 */
class GroupTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $group = $this->group();
    self::assertInstanceOf(Group::class, $group);
    self::assertEquals('', $group->getName());
  }

  public function testId()
  {
    $group = $this->group();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($group);
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $group->getId());
  }

  public function testIdException()
  {
    $group = $this->group();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Group::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    $group->getId();
  }

  public function testName()
  {
    $group = $this->group();
    $group->setName("Name");
    self::assertEquals("Name", $group->getName());
  }

  public function testPhase()
  {
    $group = $this->group();
    $phase = new Phase();
    $group->setGroupNumber(1);
    $group->setPhase($phase);
    self::assertEquals($phase, $group->getPhase());
    self::assertEquals(1, $group->getPhase()->getGroups()->count());
    self::assertEquals($group, $group->getPhase()->getGroups()[$group->getGroupNumber()]);
  }

  public function testPhaseException()
  {
    $group = $this->group();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property phase of the class " . Group::class . " must be set before" .
      " it can be accessed. Please set the property immediately after you call the constructor(Empty Constructor " .
      "Pattern).");
    $group->getPhase();
  }

  public function testPhaseNumber()
  {
    $group = $this->group();
    $group->setGroupNumber(5);
    self::assertEquals(5, $group->getGroupNumber());
  }

  public function testPhaseNumberException()
  {
    $group = $this->group();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property groupNumber of the class " . Group::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    $group->getGroupNumber();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return Group a new group
   */
  private function group(): Group
  {
    return new Group();
  }
//</editor-fold desc="Private Methods">
}