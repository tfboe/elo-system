<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 10:06 PM
 */

namespace Tests\Integration;

use App\Entity\Tournament;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\DatabaseTestCase;

/**
 * Class DBPersistenceTest
 * @package Tests\Integration
 */
class DBPersistenceTest extends DatabaseTestCase
{
//<editor-fold desc="Constructor">
  /**
   * DBPersistenceTest constructor.
   * @param string|null $name test name
   * @param array $data test data
   * @param string $dataName test data name
   */
  public function __construct($name = null, array $data = [], $dataName = '')
  {
    parent::__construct($name, $data, $dataName, true);
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  public function testDatetimeWithTimezone()
  {
    $tournament = new Tournament();
    $tournament->setUserIdentifier("test");
    $tournament->setName("TestTournament");
    $start_time = new \DateTime("2017-12-31 15:23:20 +02:00");
    $end_time = new \DateTime("2017-12-31 16:23:20 +03:00");
    $tournament->setStartTime($start_time);
    $tournament->setEndTime($end_time);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($tournament);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::flush();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::clear();
    /** @var Tournament $t */
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUndefinedMethodInspection */
    $t = EntityManager::find(Tournament::class, $tournament->getId());
    $t->setCreatedAt($tournament->getCreatedAt());
    $t->setUpdatedAt($tournament->getUpdatedAt());
    self::assertTrue($t->getCompetitions()->isEmpty());
    self::assertNotEquals($t, $tournament);
    self::assertEquals($start_time, $t->getStartTime());
    self::assertNotEquals($t, $tournament);
    self::assertEquals($end_time, $t->getEndTime());
    self::assertEquals($t, $tournament);
  }
//</editor-fold desc="Public Methods">
}