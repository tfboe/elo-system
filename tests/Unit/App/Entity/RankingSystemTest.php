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
 */
class RankingSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Entity\RankingSystem::setAutomaticInstanceGeneration
   * @covers \App\Entity\RankingSystem::getAutomaticInstanceGeneration
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   * @uses   \App\Helpers\BasicEnum
   */
  public function testAutomaticInstanceGeneration()
  {
    $e = $this->instance();
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->setAutomaticInstanceGeneration(AutomaticInstanceGeneration::MONTHLY);
    self::assertEquals(AutomaticInstanceGeneration::MONTHLY, $e->getAutomaticInstanceGeneration());
  }

  /**
   * @covers \App\Entity\RankingSystem::getCompetitions
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testCompetitions()
  {
    $e = $this->instance();
    /** @var Competition $e2 */
    $e2 = $this->createMockWithId(Competition::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getCompetitions()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getCompetitions()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getCompetitions()[$e2->getId()]);
  }

  /**
   * @covers \App\Entity\RankingSystem::__construct
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Helpers\BasicEnum
   * @uses   \App\Entity\RankingSystem::getAutomaticInstanceGeneration
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
    $e = $this->instance();
    self::assertInstanceOf(RankingSystem::class, $e);
    self::assertEquals(AutomaticInstanceGeneration::OFF, $e->getAutomaticInstanceGeneration());
    self::assertNull($e->getDefaultForLevel());
    self::assertNull($e->getOpenSyncFrom());
    self::assertInstanceOf(Collection::class, $e->getTournaments());
    self::assertInstanceOf(Collection::class, $e->getCompetitions());
    self::assertInstanceOf(Collection::class, $e->getPhases());
    self::assertInstanceOf(Collection::class, $e->getMatches());
    self::assertInstanceOf(Collection::class, $e->getGames());
    self::assertInstanceOf(Collection::class, $e->getLists());
    self::assertEquals(0, count($e->getTournaments()));
    self::assertEquals(0, count($e->getCompetitions()));
    self::assertEquals(0, count($e->getPhases()));
    self::assertEquals(0, count($e->getMatches()));
    self::assertEquals(0, count($e->getGames()));
    self::assertEquals(0, count($e->getLists()));
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
    $e = $this->instance();
    $level = Level::COMPETITION;
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->setDefaultForLevel($level);
    self::assertEquals($level, $e->getDefaultForLevel());
  }

  /**
   * @covers \App\Entity\RankingSystem::getGames
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testGames()
  {
    $e = $this->instance();
    /** @var Game $e2 */
    $e2 = $this->createMockWithId(Game::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getGames()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getGames()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getGames()[$e2->getId()]);
  }

  /**
   * @covers \App\Entity\RankingSystem::getMatches
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testMatches()
  {
    $e = $this->instance();
    /** @var Match $e2 */
    $e2 = $this->createMockWithId(Match::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getMatches()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getMatches()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getMatches()[$e2->getId()]);
  }

  /**
   * @covers \App\Entity\RankingSystem::getPhases
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testPhases()
  {
    $e = $this->instance();
    /** @var Phase $e2 */
    $e2 = $this->createMockWithId(Phase::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getPhases()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getPhases()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getPhases()[$e2->getId()]);
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
    $e = $this->instance();
    $e->setServiceName("serviceName");
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals("serviceName", $e->getServiceName());
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
    $e = $this->instance();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property serviceName of the class " . get_class($e) . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getServiceName();
  }

  /**
   * @covers \App\Entity\RankingSystem::getTournaments
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testTournaments()
  {
    $e = $this->instance();
    /** @var Tournament $e2 */
    $e2 = $this->createMockWithId(Tournament::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getTournaments()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getTournaments()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getTournaments()[$e2->getId()]);
  }

  /**
   * @covers \App\Entity\RankingSystem::getLists
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testLists()
  {
    $e = $this->instance();
    /** @var RankingSystemList $e2 */
    $e2 = $this->createMockWithId(RankingSystemList::class);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getLists()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getLists()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getLists()[$e2->getId()]);
  }

  /**
   * @covers \App\Entity\RankingSystem::setOpenSyncFrom
   * @covers \App\Entity\RankingSystem::getOpenSyncFrom
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Entity\RankingSystem::__construct
   */
  public function testOpenSyncFrom()
  {
    $e = $this->instance();
    $e->setOpenSyncFrom(new \DateTime("2017-01-01"));
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals(new \DateTime("2017-01-01"), $e->getOpenSyncFrom());
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