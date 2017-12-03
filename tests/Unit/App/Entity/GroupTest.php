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
use App\Entity\Ranking;
use App\Exceptions\ValueNotSet;
use Doctrine\Common\Collections\Collection;
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
    self::assertInstanceOf(Collection::class, $group->getRankings());
    self::assertEquals(0, $group->getRankings()->count());
  }

  public function testId()
  {
    $group = $this->group();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($group);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $group->getId());
  }

  public function testIdException()
  {
    $group = $this->group();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property id of the class " . Group::class . " must be set before it can " .
      "be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
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
    /** @noinspection PhpUnhandledExceptionInspection */
    $group->setPhase($phase);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($phase, $group->getPhase());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $group->getPhase()->getGroups()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($group, $group->getPhase()->getGroups()[$group->getGroupNumber()]);
  }

  public function testPhaseException()
  {
    $group = $this->group();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property phase of the class " . Group::class . " must be set before" .
      " it can be accessed. Please set the property immediately after you call the constructor(Empty Constructor " .
      "Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $group->getPhase();
  }

  public function testPhaseNumber()
  {
    $group = $this->group();
    $group->setGroupNumber(5);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(5, $group->getGroupNumber());
  }

  public function testPhaseNumberException()
  {
    $group = $this->group();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property groupNumber of the class " . Group::class . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $group->getGroupNumber();
  }

  public function testRankings()
  {
    $group = $this->group();
    $ranking = new Ranking();
    $ranking->setUniqueRank(1);
    /** @noinspection PhpUnhandledExceptionInspection */
    $group->getRankings()->set($ranking->getUniqueRank(), $ranking);
    self::assertEquals(1, $group->getRankings()->count());
    self::assertEquals($ranking, $group->getRankings()[1]);
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