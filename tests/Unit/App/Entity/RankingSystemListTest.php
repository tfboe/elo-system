<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Unit\App\Entity;


use App\Entity\RankingSystem;
use App\Entity\RankingSystemList;
use App\Exceptions\ValueNotSet;
use Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class RankingSystemListTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\RankingSystemList::__construct
   * @uses   \App\Entity\RankingSystemList::getLastEntryTime
   * @uses   \App\Entity\RankingSystemList::isCurrent
   */
  public function testConstructor()
  {
    $e = $this->instance();
    self::assertEquals(new \DateTime("2000-01-01"), $e->getLastEntryTime());
    self::assertFalse($e->isCurrent());
  }

  /**
   * @covers \App\Entity\RankingSystemList::setCurrent
   * @covers \App\Entity\RankingSystemList::isCurrent
   * @uses   \App\Entity\RankingSystemList::__construct
   */
  public function testCurrent()
  {
    $e = $this->instance();
    $e->setCurrent(true);
    self::assertTrue($e->isCurrent());
  }

  /**
   * @covers \App\Entity\RankingSystemList::setLastEntryTime
   * @covers \App\Entity\RankingSystemList::getLastEntryTime
   * @uses   \App\Entity\RankingSystemList::__construct
   */
  public function testLastEntry()
  {
    $e = $this->instance();
    $e->setLastEntryTime(new \DateTime("2017-01-01"));
    self::assertEquals(new \DateTime("2017-01-01"), $e->getLastEntryTime());
  }

  /**
   * @covers \App\Entity\RankingSystemList::setRankingSystem
   * @covers \App\Entity\RankingSystemList::getRankingSystem
   * @uses   \App\Entity\RankingSystemList::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystem
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   */
  public function testRankingSystem()
  {
    $e = $this->instance();
    self::getProperty(get_class($e), 'id')->setValue($e, 'list-id');
    $ranking_system = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */

    /** @noinspection PhpUnhandledExceptionInspection */
    $e->setRankingSystem($ranking_system);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($ranking_system, $e->getRankingSystem());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $e->getRankingSystem()->getLists()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e, $e->getRankingSystem()->getLists()[$e->getId()]);

    $ranking_system2 = new RankingSystem([]);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->setRankingSystem($ranking_system2);

    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($ranking_system2, $e->getRankingSystem());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $e->getRankingSystem()->getLists()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(0, $ranking_system->getLists()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e, $e->getRankingSystem()->getLists()[$e->getId()]);
  }

  /**
   * @covers \App\Entity\RankingSystemList::getRankingSystem
   * @uses   \App\Entity\RankingSystemList::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testRankingSystemException()
  {
    $e = $this->instance();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property rankingSystem of the class " . RankingSystemList::class . " must be " .
      "set before it can be accessed. Please set the property immediately after you call the constructor(Empty " .
      "Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getRankingSystem();
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return RankingSystemList
   */
  private function instance(): RankingSystemList
  {
    return new RankingSystemList();
  }
//</editor-fold desc="Private Methods">
}