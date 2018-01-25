<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Tfboe\FmLib\Entity;


use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\Player;
use Tfboe\FmLib\Entity\RankingSystem;
use Tfboe\FmLib\Entity\RankingSystemChange;
use Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityChangeTest
 * @package Tests\Tfboe\FmLib\Entity\Helpers
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RankingSystemChangeTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertInstanceOf(RankingSystemChange::class, $entity);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::hasProperty
   */
  public function testConstructorWithAdditionalKeys()
  {
    $entity = new RankingSystemChange(["key"]);
    self::assertTrue($entity->hasProperty("key"));
    self::assertFalse($entity->hasProperty("other"));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::getPlayer
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::setPlayer
   * @uses   \Tfboe\FmLib\Entity\RankingSystemChange::__construct
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
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::setPointsAfterwards
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::getPointsAfterwards
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystemChange::__construct
   */
  public function testPointsAfterwards()
  {
    $entity = $this->instance();
    $points = 24.333;
    $entity->setPointsAfterwards($points);
    self::assertEquals($points, $entity->getPointsAfterwards());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::setPointsChange
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::getPointsChange
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystemChange::__construct
   */
  public function testPointsChange()
  {
    $entity = $this->instance();
    $points = 24.333;
    $entity->setPointsChange($points);
    self::assertEquals($points, $entity->getPointsChange());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::setRankingSystem
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::getRankingSystem
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystemChange::__construct
   */
  public function testRankingSystem()
  {
    $entity = $this->instance();
    /** @var RankingSystem $rankingSystem */
    $rankingSystem = $this->createMock(RankingSystem::class);
    $entity->setRankingSystem($rankingSystem);
    self::assertEquals($rankingSystem, $entity->getRankingSystem());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::setHierarchyEntity
   * @covers \Tfboe\FmLib\Entity\RankingSystemChange::getHierarchyEntity
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystemChange::__construct
   */
  public function testTournamentHierarchyEntity()
  {
    $entity = $this->instance();
    /** @var TournamentHierarchyEntity $tEntity */
    $tEntity = $this->createMock(TournamentHierarchyEntity::class);
    $entity->setHierarchyEntity($tEntity);
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