<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Tfboe\FmLib\Entity;


use Tfboe\FmLib\Entity\Player;
use Tfboe\FmLib\Entity\RankingSystem;
use Tfboe\FmLib\Entity\RankingSystemList;
use Tfboe\FmLib\Entity\RankingSystemListEntry;
use Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityTest
 * @package Tests\Tfboe\FmLib\Entity\Helpers
 */
class RankingSystemListTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemList::__construct
   * @uses   \Tfboe\FmLib\Entity\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\RankingSystemList::isCurrent
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertEquals(new \DateTime("2000-01-01"), $entity->getLastEntryTime());
    self::assertFalse($entity->isCurrent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemList::setCurrent
   * @covers \Tfboe\FmLib\Entity\RankingSystemList::isCurrent
   * @uses   \Tfboe\FmLib\Entity\RankingSystemList::__construct
   */
  public function testCurrent()
  {
    $entity = $this->instance();
    $entity->setCurrent(true);
    self::assertTrue($entity->isCurrent());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemList::setLastEntryTime
   * @covers \Tfboe\FmLib\Entity\RankingSystemList::getLastEntryTime
   * @uses   \Tfboe\FmLib\Entity\RankingSystemList::__construct
   */
  public function testLastEntry()
  {
    $instance = $this->instance();
    $instance->setLastEntryTime(new \DateTime("2017-01-01"));
    self::assertEquals(new \DateTime("2017-01-01"), $instance->getLastEntryTime());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemList::getEntries
   * @uses   \Tfboe\FmLib\Entity\RankingSystemList::__construct
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
   * @covers \Tfboe\FmLib\Entity\RankingSystemList::setRankingSystem
   * @covers \Tfboe\FmLib\Entity\RankingSystemList::getRankingSystem
   * @uses   \Tfboe\FmLib\Entity\RankingSystemList::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystem
   * @uses   \Tfboe\FmLib\Entity\Helpers\UUIDEntity::getId
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