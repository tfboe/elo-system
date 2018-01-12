<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Unit\App\Entity;


use App\Entity\Helpers\AutomaticInstanceGeneration;
use App\Entity\Helpers\TournamentHierarchyEntity;
use App\Entity\RankingSystem;
use App\Entity\RankingSystemList;
use App\Exceptions\ValueNotSet;
use App\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use Tests\Helpers\UnitTestCase;


/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class RankingSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Entity\RankingSystem::setGenerationInterval
   * @covers \App\Entity\RankingSystem::getGenerationInterval
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   * @uses   \App\Helpers\BasicEnum
   */
  public function testAutomaticInstanceGeneration()
  {
    $entity = $this->instance();
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->setGenerationInterval(AutomaticInstanceGeneration::MONTHLY);
    self::assertEquals(AutomaticInstanceGeneration::MONTHLY, $entity->getGenerationInterval());
  }

  /**
   * @covers \App\Entity\RankingSystem::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Helpers\BasicEnum
   * @uses   \App\Entity\RankingSystem::getGenerationInterval
   * @uses   \App\Entity\RankingSystem::getDefaultForLevel
   * @uses   \App\Entity\RankingSystem::getLists
   * @uses   \App\Entity\RankingSystem::getOpenSyncFrom
   * @uses   \App\Entity\RankingSystem::getHierarchyEntries
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
   * @covers \App\Entity\RankingSystem::setDefaultForLevel
   * @covers \App\Entity\RankingSystem::getDefaultForLevel
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   * @uses   \App\Helpers\BasicEnum
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
   * @covers \App\Entity\RankingSystem::getLists
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testLists()
  {
    $entity = $this->instance();
    /** @var RankingSystemList $entity2 */
    $entity2 = $this->createStubWithId(RankingSystemList::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->getLists()->set($entity2->getId(), $entity2);
    self::assertEquals(1, $entity->getLists()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entity2, $entity->getLists()[$entity2->getId()]);
  }

  /**
   * @covers \App\Entity\RankingSystem::setOpenSyncFrom
   * @covers \App\Entity\RankingSystem::getOpenSyncFrom
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testOpenSyncFrom()
  {
    $entity = $this->instance();
    $entity->setOpenSyncFrom(new \DateTime("2017-01-01"));
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(new \DateTime("2017-01-01"), $entity->getOpenSyncFrom());
  }

  /**
   * @covers \App\Entity\RankingSystem::setServiceName
   * @covers \App\Entity\RankingSystem::getServiceName
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   */
  public function testServiceName()
  {
    $entity = $this->instance();
    $entity->setServiceName("serviceName");
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals("serviceName", $entity->getServiceName());
  }

  /**
   * @covers \App\Entity\RankingSystem::getServiceName
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   * @uses   \App\Entity\Helpers\UnsetProperty::ensureNotNull
   * @uses   \App\Exceptions\ValueNotSet::__construct
   */
  public function testServiceNameException()
  {
    $entity = $this->instance();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property serviceName of the class " . get_class($entity) . " must be set " .
      "before it can be accessed. Please set the property immediately after you call the " .
      "constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->getServiceName();
  }

  /**
   * @covers \App\Entity\RankingSystem::getHierarchyEntries
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testTournamentRankingEntities()
  {
    $entity = $this->instance();
    /** @var TournamentHierarchyEntity $entity2 */
    $entity2 = $this->createStubWithId(TournamentHierarchyEntity::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->getHierarchyEntries()->set($entity2->getId(), $entity2);
    self::assertEquals(1, $entity->getHierarchyEntries()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
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