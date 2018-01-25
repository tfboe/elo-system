<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 10:06 PM
 */

namespace Tests\Integration;

use Tfboe\FmLib\Entity\Tournament;
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
    $startTime = new \DateTime("2017-12-31 15:23:20 +02:00");
    $endTime = new \DateTime("2017-12-31 16:23:20 +03:00");
    $tournament->setStartTime($startTime);
    $tournament->setEndTime($endTime);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::persist($tournament);
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::flush();
    /** @noinspection PhpUndefinedMethodInspection */
    EntityManager::clear();
    /** @var Tournament $resultTournament */
    /** @noinspection PhpUndefinedMethodInspection */
    $resultTournament = EntityManager::find(Tournament::class, $tournament->getId());
    $resultTournament->setCreatedAt($tournament->getCreatedAt());
    $resultTournament->setUpdatedAt($tournament->getUpdatedAt());
    self::assertTrue($resultTournament->getCompetitions()->isEmpty()); //initialize collection to be comparable
    self::assertTrue($resultTournament->getRankingSystems()->isEmpty()); //initialize collection to be comparable
    self::assertNotEquals($resultTournament, $tournament);
    self::assertEquals($startTime, $resultTournament->getStartTime());
    self::assertNotEquals($resultTournament, $tournament);
    self::assertEquals($endTime, $resultTournament->getEndTime());
    self::assertEquals($resultTournament, $tournament);
  }
//</editor-fold desc="Public Methods">
}