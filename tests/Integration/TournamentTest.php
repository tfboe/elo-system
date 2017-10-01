<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/22/17
 * Time: 8:48 PM
 */

namespace Tests\Integration;

use App\Entity\Categories\GameMode;
use App\Entity\Categories\OrganizingMode;
use App\Entity\Categories\ScoreMode;
use App\Entity\Categories\Table;
use App\Entity\Categories\TeamMode;
use App\Entity\Tournament;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\AuthenticatedTestCase;

/**
 * Class TournamentTest
 * @package Tests\Integration
 */
class TournamentTest extends AuthenticatedTestCase
{
//<editor-fold desc="Public Methods">
  public function testCreateTournamentFull()
  {
    $this->jsonAuth('POST', '/createOrUpdateTournament', [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'tournamentListId' => 'testList',
      'gameMode' => 'SPEEDBALL',
      'organizingMode' => 'ELIMINATION',
      'scoreMode' => 'BEST_OF_FIVE',
      'teamMode' => 'DOUBLE',
      'table' => 'ROBERTO_SPORT'
    ])->seeJsonEquals(['type' => 'create']);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    /** @var Tournament $tournament */
    $tournament = $repo->findOneBy(['creator' => $this->user, 'userIdentifier' => 'id0']);
    self::assertEquals('Test Tournament', $tournament->getName());
    self::assertEquals('testList', $tournament->getTournamentListId());
    self::assertEquals(GameMode::SPEEDBALL, $tournament->getGameMode());
    self::assertEquals(OrganizingMode::ELIMINATION, $tournament->getOrganizingMode());
    self::assertEquals(ScoreMode::BEST_OF_FIVE, $tournament->getScoreMode());
    self::assertEquals(TeamMode::DOUBLE, $tournament->getTeamMode());
    self::assertEquals(Table::ROBERTO_SPORT, $tournament->getTable());
    self::assertNotEmpty($tournament->getId());
  }

  public function testCreateTournamentMin()
  {
    $this->jsonAuth('POST', '/createOrUpdateTournament', [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0'
    ])->seeJsonEquals(['type' => 'create']);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    /** @var Tournament $tournament */
    $tournament = $repo->findOneBy(['creator' => $this->user, 'userIdentifier' => 'id0']);
    self::assertEquals('Test Tournament', $tournament->getName());
    self::assertEquals('', $tournament->getTournamentListId());
    self::assertNull($tournament->getGameMode());
    self::assertNull($tournament->getOrganizingMode());
    self::assertNull($tournament->getScoreMode());
    self::assertNull($tournament->getTeamMode());
    self::assertNull($tournament->getTable());
    self::assertNotEmpty($tournament->getId());
  }

  public function testTournamentUpdate()
  {
    /** @var Tournament $tournament */
    $tournament = entity(Tournament::class)->create([
      'userIdentifier' => 't1',
      'creator' => $this->user,
      'gameMode' => GameMode::CLASSIC
    ]);
    $id = $tournament->getId();
    self::assertEquals('t1', $tournament->getUserIdentifier());
    self::assertEquals('', $tournament->getTournamentListId());
    self::assertEquals(GameMode::CLASSIC, $tournament->getGameMode());
    self::assertNull($tournament->getOrganizingMode());
    self::assertNull($tournament->getScoreMode());
    self::assertNull($tournament->getTeamMode());
    self::assertNull($tournament->getTable());
    $this->jsonAuth('POST', '/createOrUpdateTournament', [
      'name' => 'New Name',
      'userIdentifier' => 't1',
      'gameMode' => 'OFFICIAL',
      'table' => 'GARLANDO'
    ])->seeJsonEquals(['type' => 'update']);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    /** @var Tournament[] $tournaments */
    $tournaments = $repo->findAll();
    self::assertEquals(1, count($tournaments));
    $new_tournament = $tournaments[0];
    self::assertEquals($id, $new_tournament->getId());
    self::assertEquals('t1', $new_tournament->getUserIdentifier());
    self::assertEquals($this->user, $new_tournament->getCreator());
    self::assertEquals('', $new_tournament->getTournamentListId());
    self::assertNull($new_tournament->getTeamMode());
    self::assertNull($new_tournament->getScoreMode());
    self::assertNull($new_tournament->getOrganizingMode());
    self::assertEquals(GameMode::OFFICIAL, $new_tournament->getGameMode());
    self::assertEquals('New Name', $new_tournament->getName());
    self::assertEquals(Table::GARLANDO, $new_tournament->getTable());
    self::assertEquals($tournament, $new_tournament);
  }
//</editor-fold desc="Public Methods">
}