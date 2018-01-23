<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Unit\App\Entity;


use App\Entity\Helpers\TournamentHierarchyEntity;
use App\Entity\Player;
use App\Entity\RankingSystem;
use App\Entity\RankingSystemChange;
use Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityChangeTest
 * @package Tests\Unit\App\Entity\Helpers
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RankingSystemChangeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Entity\RankingSystemChange::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertInstanceOf(RankingSystemChange::class, $entity);
  }

  /**
   * @covers \App\Entity\RankingSystemChange::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\SubClassData::hasProperty
   */
  public function testConstructorWithAdditionalKeys()
  {
    $entity = new RankingSystemChange(["key"]);
    self::assertTrue($entity->hasProperty("key"));
    self::assertFalse($entity->hasProperty("other"));
  }

  /**
   * @covers \App\Entity\RankingSystemChange::getPlayer
   * @covers \App\Entity\RankingSystemChange::setPlayer
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemChange::__construct
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
   * @covers \App\Entity\RankingSystemChange::setPointsAfterwards
   * @covers \App\Entity\RankingSystemChange::getPointsAfterwards
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemChange::__construct
   */
  public function testPointsAfterwards()
  {
    $entity = $this->instance();
    $points = 24.333;
    $entity->setPointsAfterwards($points);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($points, $entity->getPointsAfterwards());
  }

  /**
   * @covers \App\Entity\RankingSystemChange::setPointsChange
   * @covers \App\Entity\RankingSystemChange::getPointsChange
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemChange::__construct
   */
  public function testPointsChange()
  {
    $entity = $this->instance();
    $points = 24.333;
    $entity->setPointsChange($points);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($points, $entity->getPointsChange());
  }

  /**
   * @covers \App\Entity\RankingSystemChange::setRankingSystem
   * @covers \App\Entity\RankingSystemChange::getRankingSystem
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemChange::__construct
   */
  public function testRankingSystem()
  {
    $entity = $this->instance();
    /** @var RankingSystem $rankingSystem */
    $rankingSystem = $this->createMock(RankingSystem::class);
    $entity->setRankingSystem($rankingSystem);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($rankingSystem, $entity->getRankingSystem());
  }

  /**
   * @covers \App\Entity\RankingSystemChange::setHierarchyEntity
   * @covers \App\Entity\RankingSystemChange::getHierarchyEntity
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Entity\RankingSystemChange::__construct
   */
  public function testTournamentHierarchyEntity()
  {
    $entity = $this->instance();
    /** @var TournamentHierarchyEntity $tEntity */
    $tEntity = $this->createMock(TournamentHierarchyEntity::class);
    $entity->setHierarchyEntity($tEntity);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($tEntity, $entity->getHierarchyEntity());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return RankingSystemChange
   */
  private function instance(): RankingSystemChange
  {
    return new RankingSystemChange([]);
  }
//</editor-fold desc="Private Methods">
}