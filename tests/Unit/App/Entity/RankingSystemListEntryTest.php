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
use Tfboe\FmLib\Entity\RankingSystemList;
use Tfboe\FmLib\Entity\RankingSystemListEntry;
use Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityChangeTest
 * @package Tests\Tfboe\FmLib\Entity\Helpers
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RankingSystemListEntryTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertInstanceOf(RankingSystemListEntry::class, $entity);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::hasProperty
   */
  public function testConstructorWithAdditionalKeys()
  {
    $entity = new RankingSystemListEntry(["key"]);
    self::assertTrue($entity->hasProperty("key"));
    self::assertFalse($entity->hasProperty("other"));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::setNumberRankedEntities
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::getNumberRankedEntities
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystemListEntry::__construct
   */
  public function testNumberOfRankedEntities()
  {
    $entity = $this->instance();
    $points = 5;
    $entity->setNumberRankedEntities($points);
    self::assertEquals($points, $entity->getNumberRankedEntities());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::getPlayer
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::setPlayer
   * @uses   \Tfboe\FmLib\Entity\RankingSystemListEntry::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testPlayer()
  {
    $entity = $this->instance();
    /** @var Player $player */
    $player = $this->createMock(Player::class);
    $entity->setPlayer($player);
    self::assertEquals($player, $entity->getPlayer());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::setPoints
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::getPoints
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystemListEntry::__construct
   */
  public function testPoints()
  {
    $entity = $this->instance();
    $points = 24.333;
    $entity->setPoints($points);
    self::assertEquals($points, $entity->getPoints());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::setRankingSystemList
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::getRankingSystemList
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystemListEntry::__construct
   * @uses   \Tfboe\FmLib\Entity\RankingSystemListEntry::getPlayer
   * @uses   \Tfboe\FmLib\Entity\RankingSystemListEntry::setPlayer
   * @uses   \Tfboe\FmLib\Entity\RankingSystemList
   */
  public function testRankingSystemList()
  {
    $entity = $this->instance();
    $rankingSystemList = new RankingSystemList();
    $player = $this->createMock(Player::class);
    $player->method('getPlayerId')->willReturn(5);
    /** @var Player $player */
    $entity->setPlayer($player);

    $entity->setRankingSystemList($rankingSystemList);
    self::assertEquals($rankingSystemList, $entity->getRankingSystemList());
    self::assertEquals(1, $rankingSystemList->getEntries()->count());
    self::assertEquals($entity, $rankingSystemList->getEntries()[5]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::setRankingSystemList
   * @covers \Tfboe\FmLib\Entity\RankingSystemListEntry::getRankingSystemList
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystemListEntry::__construct
   * @uses   \Tfboe\FmLib\Entity\RankingSystemListEntry::getPlayer
   * @uses   \Tfboe\FmLib\Entity\RankingSystemListEntry::setPlayer
   * @uses   \Tfboe\FmLib\Entity\RankingSystemList
   */
  public function testRankingSystemListRemoveFromOld()
  {
    $entity = $this->instance();
    $rankingSystemList = new RankingSystemList();
    $player = $this->createMock(Player::class);
    $player->method('getPlayerId')->willReturn(5);
    /** @var Player $player */
    $entity->setPlayer($player);

    $entity->setRankingSystemList($rankingSystemList);
    self::assertEquals($rankingSystemList, $entity->getRankingSystemList());
    self::assertEquals(1, $rankingSystemList->getEntries()->count());

    $rankingSystemList2 = new RankingSystemList();
    $entity->setRankingSystemList($rankingSystemList2);
    self::assertEquals(0, $rankingSystemList->getEntries()->count());
    self::assertEquals(1, $rankingSystemList2->getEntries()->count());
    self::assertEquals($entity, $rankingSystemList2->getEntries()[5]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return RankingSystemListEntry
   */
  private function instance(): RankingSystemListEntry
  {
    return new RankingSystemListEntry([]);
  }
//</editor-fold desc="Private Methods">
}