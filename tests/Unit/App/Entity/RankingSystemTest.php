<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 10:39 AM
 */

namespace Tests\Unit\App\Entity;

use App\Entity\Competition;
use App\Entity\Game;
use App\Entity\Helpers\AutomaticInstanceGeneration;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\RankingSystem;
use App\Entity\RankingSystemList;
use App\Entity\Tournament;
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
   * @covers \App\Entity\RankingSystem::getCompetitions
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testCompetitions()
  {
    $entity = $this->instance();
    /** @var Competition $entity2 */
    $entity2 = $this->createStubWithId(Competition::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->getCompetitions()->set($entity2->getId(), $entity2);
    self::assertEquals(1, $entity->getCompetitions()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entity2, $entity->getCompetitions()[$entity2->getId()]);
  }

  /**
   * @covers \App\Entity\RankingSystem::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Helpers\BasicEnum
   * @uses   \App\Entity\RankingSystem::getGenerationInterval
   * @uses   \App\Entity\RankingSystem::getCompetitions
   * @uses   \App\Entity\RankingSystem::getDefaultForLevel
   * @uses   \App\Entity\RankingSystem::getGames
   * @uses   \App\Entity\RankingSystem::getLists
   * @uses   \App\Entity\RankingSystem::getMatches
   * @uses   \App\Entity\RankingSystem::getOpenSyncFrom
   * @uses   \App\Entity\RankingSystem::getPhases
   * @uses   \App\Entity\RankingSystem::getTournaments
   */
  public function testConstructor()
  {
    $entity = $this->instance();
    self::assertInstanceOf(RankingSystem::class, $entity);
    self::assertEquals(AutomaticInstanceGeneration::OFF, $entity->getGenerationInterval());
    self::assertNull($entity->getDefaultForLevel());
    self::assertNull($entity->getOpenSyncFrom());
    self::assertInstanceOf(Collection::class, $entity->getTournaments());
    self::assertInstanceOf(Collection::class, $entity->getCompetitions());
    self::assertInstanceOf(Collection::class, $entity->getPhases());
    self::assertInstanceOf(Collection::class, $entity->getMatches());
    self::assertInstanceOf(Collection::class, $entity->getGames());
    self::assertInstanceOf(Collection::class, $entity->getLists());
    self::assertEquals(0, count($entity->getTournaments()));
    self::assertEquals(0, count($entity->getCompetitions()));
    self::assertEquals(0, count($entity->getPhases()));
    self::assertEquals(0, count($entity->getMatches()));
    self::assertEquals(0, count($entity->getGames()));
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
   * @covers \App\Entity\RankingSystem::getGames
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testGames()
  {
    $entity = $this->instance();
    /** @var Game $entity2 */
    $entity2 = $this->createStubWithId(Game::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->getGames()->set($entity2->getId(), $entity2);
    self::assertEquals(1, $entity->getGames()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entity2, $entity->getGames()[$entity2->getId()]);
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
   * @covers \App\Entity\RankingSystem::getMatches
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testMatches()
  {
    $entity = $this->instance();
    /** @var Match $entity2 */
    $entity2 = $this->createStubWithId(Match::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->getMatches()->set($entity2->getId(), $entity2);
    self::assertEquals(1, $entity->getMatches()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entity2, $entity->getMatches()[$entity2->getId()]);
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
   * @covers \App\Entity\RankingSystem::getPhases
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testPhases()
  {
    $entity = $this->instance();
    /** @var Phase $entity2 */
    $entity2 = $this->createStubWithId(Phase::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->getPhases()->set($entity2->getId(), $entity2);
    self::assertEquals(1, $entity->getPhases()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entity2, $entity->getPhases()[$entity2->getId()]);
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
   * @covers \App\Entity\RankingSystem::getTournaments
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testTournaments()
  {
    $entity = $this->instance();
    /** @var Tournament $entity2 */
    $entity2 = $this->createStubWithId(Tournament::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $entity->getTournaments()->set($entity2->getId(), $entity2);
    self::assertEquals(1, $entity->getTournaments()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($entity2, $entity->getTournaments()[$entity2->getId()]);
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