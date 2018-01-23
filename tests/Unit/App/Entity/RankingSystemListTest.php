<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Unit\App\Entity;


use App\Entity\Player;
use App\Entity\RankingSystem;
use App\Entity\RankingSystemList;
use App\Entity\RankingSystemListEntry;
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
    $entity = $this->instance();
    self::assertEquals(new \DateTime("2000-01-01"), $entity->getLastEntryTime());
    self::assertFalse($entity->isCurrent());
  }

  /**
   * @covers \App\Entity\RankingSystemList::setCurrent
   * @covers \App\Entity\RankingSystemList::isCurrent
   * @uses   \App\Entity\RankingSystemList::__construct
   */
  public function testCurrent()
  {
    $entity = $this->instance();
    $entity->setCurrent(true);
    self::assertTrue($entity->isCurrent());
  }

  /**
   * @covers \App\Entity\RankingSystemList::setLastEntryTime
   * @covers \App\Entity\RankingSystemList::getLastEntryTime
   * @uses   \App\Entity\RankingSystemList::__construct
   */
  public function testLastEntry()
  {
    $instance = $this->instance();
    $instance->setLastEntryTime(new \DateTime("2017-01-01"));
    self::assertEquals(new \DateTime("2017-01-01"), $instance->getLastEntryTime());
  }

  /**
   * @covers \App\Entity\RankingSystemList::getEntries
   * @uses   \App\Entity\RankingSystemList::__construct
   */
  public function testLists()
  {
    $entity = $this->instance();
    $entity2 = $this->createMock(RankingSystemListEntry::class);
    $player = $this->createStubWithId(Player::class, 5, 'getPlayerId');
    /** @var Player $player */
    $entity2->method('getPlayer')->willReturn($player);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->getEntries()->set($player->getPlayerId(), $entity2);
    self::assertEquals(1, $entity->getEntries()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entity2, $entity->getEntries()[$player->getPlayerId()]);
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
    $instance = $this->instance();
    /** @noinspection PhpUnhandledExceptionInspection */
    self::getProperty(get_class($instance), 'id')->setValue($instance, 'list-id');
    $rankingSystem = new RankingSystem([]);
    /** @noinspection PhpUndefinedMethodInspection */

    /** @noinspection PhpUnhandledExceptionInspection */
    $instance->setRankingSystem($rankingSystem);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($rankingSystem, $instance->getRankingSystem());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $instance->getRankingSystem()->getLists()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($instance, $instance->getRankingSystem()->getLists()[$instance->getId()]);

    $rankingSystem2 = new RankingSystem([]);
    /** @noinspection PhpUnhandledExceptionInspection */
    $instance->setRankingSystem($rankingSystem2);

    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($rankingSystem2, $instance->getRankingSystem());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(1, $instance->getRankingSystem()->getLists()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(0, $rankingSystem->getLists()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($instance, $instance->getRankingSystem()->getLists()[$instance->getId()]);
  }

  /**
   * @covers \App\Entity\RankingSystemList::getRankingSystem
   * @uses   \App\Entity\RankingSystemList::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testRankingSystemException()
  {
    $instance = $this->instance();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property rankingSystem of the class " . RankingSystemList::class . " must be " .
      "set before it can be accessed. Please set the property immediately after you call the constructor(Empty " .
      "Constructor Pattern).");
    /** @noinspection PhpUnhandledExceptionInspection */
    $instance->getRankingSystem();
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