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
    $entity->getEntries()->set($player->getPlayerId(), $entity2);
    self::assertEquals(1, $entity->getEntries()->count());
    self::assertEquals($entity2, $entity->getEntries()[$player->getPlayerId()]);
  }

  /**
   * @covers \App\Entity\RankingSystemList::setRankingSystem
   * @covers \App\Entity\RankingSystemList::getRankingSystem
   * @uses   \App\Entity\RankingSystemList::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem
   * @uses   \App\Entity\Helpers\UUIDEntity::getId
   */
  public function testRankingSystem()
  {
    $instance = $this->instance();
    /** @noinspection PhpUnhandledExceptionInspection */
    self::getProperty(get_class($instance), 'id')->setValue($instance, 'list-id');
    $rankingSystem = new RankingSystem([]);

    $instance->setRankingSystem($rankingSystem);
    self::assertEquals($rankingSystem, $instance->getRankingSystem());
    self::assertEquals(1, $instance->getRankingSystem()->getLists()->count());
    self::assertEquals($instance, $instance->getRankingSystem()->getLists()[$instance->getId()]);

    $rankingSystem2 = new RankingSystem([]);
    $instance->setRankingSystem($rankingSystem2);

    self::assertEquals($rankingSystem2, $instance->getRankingSystem());
    self::assertEquals(1, $instance->getRankingSystem()->getLists()->count());
    self::assertEquals(0, $rankingSystem->getLists()->count());
    self::assertEquals($instance, $instance->getRankingSystem()->getLists()[$instance->getId()]);
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