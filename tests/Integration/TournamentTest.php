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
//</editor-fold desc="Public Methods">
}