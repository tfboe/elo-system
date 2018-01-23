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
use App\Entity\RankingSystemList;
use App\Entity\RankingSystemListEntry;
use Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityChangeTest
 * @package Tests\Unit\App\Entity\Helpers
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RankingSystemListEntryTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Entity\RankingSystemListEntry::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertInstanceOf(RankingSystemListEntry::class, $entity);
  }

  /**
   * @covers \App\Entity\RankingSystemListEntry::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\SubClassData::hasProperty
   */
  public function testConstructorWithAdditionalKeys()
  {
    $entity = new RankingSystemListEntry(["key"]);
    self::assertTrue($entity->hasProperty("key"));
    self::assertFalse($entity->hasProperty("other"));
  }

  /**
   * @covers \App\Entity\RankingSystemListEntry::setNumberRankedEntities
   * @covers \App\Entity\RankingSystemListEntry::getNumberRankedEntities
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemListEntry::__construct
   */
  public function testNumberOfRankedEntities()
  {
    $entity = $this->instance();
    $points = 5;
    $entity->setNumberRankedEntities($points);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($points, $entity->getNumberRankedEntities());
  }

  /**
   * @covers \App\Entity\RankingSystemListEntry::getPlayer
   * @covers \App\Entity\RankingSystemListEntry::setPlayer
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemListEntry::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testPlayer()
  {
    $entity = $this->instance();
    /** @var Player $player */
    $player = $this->createMock(Player::class);
    $entity->setPlayer($player);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($player, $entity->getPlayer());
  }

  /**
   * @covers \App\Entity\RankingSystemListEntry::setPoints
   * @covers \App\Entity\RankingSystemListEntry::getPoints
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemListEntry::__construct
   */
  public function testPoints()
  {
    $entity = $this->instance();
    $points = 24.333;
    $entity->setPoints($points);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($points, $entity->getPoints());
  }

  /**
   * @covers \App\Entity\RankingSystemListEntry::setRankingSystemList
   * @covers \App\Entity\RankingSystemListEntry::getRankingSystemList
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemListEntry::__construct
   * @uses   \App\Entity\RankingSystemListEntry::getPlayer
   * @uses   \App\Entity\RankingSystemListEntry::setPlayer
   * @uses   \App\Entity\RankingSystemList
   */
  public function testRankingSystemList()
  {
    $entity = $this->instance();
    $rankingSystemList = new RankingSystemList();
    $player = $this->createMock(Player::class);
    $player->method('getPlayerId')->willReturn(5);
    /** @var Player $player */
    $entity->setPlayer($player);

    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setRankingSystemList($rankingSystemList);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($rankingSystemList, $entity->getRankingSystemList());
    self::assertEquals(1, $rankingSystemList->getEntries()->count());
    self::assertEquals($entity, $rankingSystemList->getEntries()[5]);
  }

  /**
   * @covers \App\Entity\RankingSystemListEntry::setRankingSystemList
   * @covers \App\Entity\RankingSystemListEntry::getRankingSystemList
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemListEntry::__construct
   * @uses   \App\Entity\RankingSystemListEntry::getPlayer
   * @uses   \App\Entity\RankingSystemListEntry::setPlayer
   * @uses   \App\Entity\RankingSystemList
   */
  public function testRankingSystemListRemoveFromOld()
  {
    $entity = $this->instance();
    $rankingSystemList = new RankingSystemList();
    $player = $this->createMock(Player::class);
    $player->method('getPlayerId')->willReturn(5);
    /** @var Player $player */
    $entity->setPlayer($player);

    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setRankingSystemList($rankingSystemList);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($rankingSystemList, $entity->getRankingSystemList());
    self::assertEquals(1, $rankingSystemList->getEntries()->count());

    $rankingSystemList2 = new RankingSystemList();
    /** @noinspection PhpUnhandledExceptionInspection */
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