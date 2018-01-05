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
use App\Entity\Tournament;
use App\Exceptions\ValueNotSet;
use App\Helpers\Level;
use Doctrine\Common\Collections\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\TestCase;

/**
 * Class BaseEntityTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class RankingSystemTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testAutomaticInstanceGeneration()
  {
    $e = $this->mock();
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->setAutomaticInstanceGeneration(AutomaticInstanceGeneration::MONTHLY);
    self::assertEquals(AutomaticInstanceGeneration::MONTHLY, $e->getAutomaticInstanceGeneration());
  }

  public function testCompetitions()
  {
    $e = $this->mock();
    $e2 = new Competition();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($e2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getCompetitions()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getCompetitions()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getCompetitions()[$e2->getId()]);
  }

  public function testConstructor()
  {
    $e = $this->mock();
    self::assertInstanceOf(RankingSystem::class, $e);
    self::assertEquals(AutomaticInstanceGeneration::OFF, $e->getAutomaticInstanceGeneration());
    self::assertNull($e->getDefaultForLevel());
    self::assertInstanceOf(Collection::class, $e->getTournaments());
    self::assertInstanceOf(Collection::class, $e->getCompetitions());
    self::assertInstanceOf(Collection::class, $e->getPhases());
    self::assertInstanceOf(Collection::class, $e->getMatches());
    self::assertInstanceOf(Collection::class, $e->getGames());
    self::assertEquals(0, count($e->getTournaments()));
    self::assertEquals(0, count($e->getCompetitions()));
    self::assertEquals(0, count($e->getPhases()));
    self::assertEquals(0, count($e->getMatches()));
    self::assertEquals(0, count($e->getGames()));
  }

  public function testDefaultForLevel()
  {
    $e = $this->mock();
    $level = Level::COMPETITION;
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->setDefaultForLevel($level);
    self::assertEquals($level, $e->getDefaultForLevel());
  }

  public function testGames()
  {
    $e = $this->mock();
    $e2 = new Game();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($e2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getGames()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getGames()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getGames()[$e2->getId()]);
  }

  public function testMatches()
  {
    $e = $this->mock();
    $e2 = new Match();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($e2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getMatches()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getMatches()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getMatches()[$e2->getId()]);
  }

  public function testPhases()
  {
    $e = $this->mock();
    $e2 = new Phase();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($e2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getPhases()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getPhases()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getPhases()[$e2->getId()]);
  }

  public function testServiceName()
  {
    $e = $this->mock();
    $e->setServiceName("serviceName");
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals("serviceName", $e->getServiceName());
  }

  public function testServiceNameException()
  {
    $e = $this->mock();
    $this->expectException(ValueNotSet::class);
    $this->expectExceptionMessage("The property serviceName of the class " . get_class($e) . " must be set before it " .
      "can be accessed. Please set the property immediately after you call the constructor(Empty Constructor Pattern)."
    );
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getServiceName();
  }

  public function testTournaments()
  {
    $e = $this->mock();
    $e2 = new Tournament();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($e2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $e->getTournaments()->set($e2->getId(), $e2);
    self::assertEquals(1, $e->getTournaments()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($e2, $e->getTournaments()[$e2->getId()]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|RankingSystem
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForAbstractClass(RankingSystem::class, [[]]);
  }
//</editor-fold desc="Private Methods">
}