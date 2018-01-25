<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Tfboe\FmLib\Entity;


use Tfboe\FmLib\Entity\Helpers\AutomaticInstanceGeneration;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\RankingSystem;
use Tfboe\FmLib\Entity\RankingSystemList;
use Tfboe\FmLib\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityTest
 * @package Tests\Tfboe\FmLib\Entity\Helpers
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RankingSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystem::setGenerationInterval
   * @covers \Tfboe\FmLib\Entity\RankingSystem::getGenerationInterval
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::__construct
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testAutomaticInstanceGeneration()
  {
    $entity = $this->instance();
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setGenerationInterval(AutomaticInstanceGeneration::MONTHLY);
    self::assertEquals(AutomaticInstanceGeneration::MONTHLY, $entity->getGenerationInterval());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystem::__construct
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::getGenerationInterval
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::getDefaultForLevel
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::getLists
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::getOpenSyncFrom
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::getHierarchyEntries
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertInstanceOf(RankingSystem::class, $entity);
    self::assertEquals(AutomaticInstanceGeneration::OFF, $entity->getGenerationInterval());
    self::assertNull($entity->getDefaultForLevel());
    self::assertNull($entity->getOpenSyncFrom());
    self::assertInstanceOf(Collection::class, $entity->getHierarchyEntries());
    self::assertInstanceOf(Collection::class, $entity->getLists());
    self::assertEquals(0, count($entity->getHierarchyEntries()));
    self::assertEquals(0, count($entity->getLists()));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystem::setDefaultForLevel
   * @covers \Tfboe\FmLib\Entity\RankingSystem::getDefaultForLevel
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::__construct
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testDefaultForLevel()
  {
    $entity = $this->instance();
    $level = Level::COMPETITION;
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setDefaultForLevel($level);
    self::assertEquals($level, $entity->getDefaultForLevel());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystem::getLists
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::__construct
   */
  public function testLists()
  {
    $entity = $this->instance();
    /** @var RankingSystemList $entity2 */
    $entity2 = $this->createStubWithId(RankingSystemList::class);
    $entity->getLists()->set($entity2->getId(), $entity2);
    self::assertEquals(1, $entity->getLists()->count());
    self::assertEquals($entity2, $entity->getLists()[$entity2->getId()]);
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystem::setOpenSyncFrom
   * @covers \Tfboe\FmLib\Entity\RankingSystem::getOpenSyncFrom
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::__construct
   */
  public function testOpenSyncFrom()
  {
    $entity = $this->instance();
    $entity->setOpenSyncFrom(new \DateTime("2017-01-01"));
    self::assertEquals(new \DateTime("2017-01-01"), $entity->getOpenSyncFrom());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystem::setServiceName
   * @covers \Tfboe\FmLib\Entity\RankingSystem::getServiceName
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::__construct
   */
  public function testServiceName()
  {
    $entity = $this->instance();
    $entity->setServiceName("serviceName");
    self::assertEquals("serviceName", $entity->getServiceName());
  }

  /**
   * @covers \Tfboe\FmLib\Entity\RankingSystem::getHierarchyEntries
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Entity\RankingSystem::__construct
   */
  public function testTournamentRankingEntities()
  {
    $entity = $this->instance();
    /** @var TournamentHierarchyEntity $entity2 */
    $entity2 = $this->createStubWithId(TournamentHierarchyEntity::class);
    $entity->getHierarchyEntries()->set($entity2->getId(), $entity2);
    self::assertEquals(1, $entity->getHierarchyEntries()->count());
    self::assertEquals($entity2, $entity->getHierarchyEntries()[$entity2->getId()]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return RankingSystem
   */
  private function instance(): RankingSystem
  {
    return new RankingSystem([]);
  }
//</editor-fold desc="Private Methods">
}